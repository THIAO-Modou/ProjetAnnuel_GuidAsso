<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../../config/BD.php';

if (!function_exists('getCraigActivityFromSocialObjet')) {
    function getCraigActivityFromSocialObjet($socialObjet) {
        if ($socialObjet === null) {
            return '';
        }
        // Fallback: return the provided value as-is when no mapping is available.
        return trim((string)$socialObjet);
    }
}
if (!isset($_POST['query']) || strlen(trim($_POST['query'])) < 2) {
    exit;
}

$query = trim($_POST['query']);

// Construit une requete Lucene compatible ODS pour simuler "contient":
// "agam" => title:agam* OR title:gam* OR title:am*
// Puis on garde le filtre PHP stripos() pour garantir le vrai "contient".
function buildContainsFieldQuery(string $field, string $input): string {
    $terms = preg_split('/\s+/', $input);
    $parts = [];

    foreach ($terms as $term) {
        $term = trim($term);
        if ($term === '') {
            continue;
        }

        // Nettoie les caracteres speciaux Lucene
        $clean = preg_replace('/[+\-!(){}\[\]^"~*?:\\\\\/]/', ' ', $term);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        if ($clean === '') {
            continue;
        }

        $len = strlen($clean);
        $variants = [];

        // Max 4 suffixes pour limiter le volume de la requete
        for ($i = 0; $i < $len; $i++) {
            $suffix = substr($clean, $i);
            if (strlen($suffix) < 2) {
                break;
            }
            $variants[] = $field . ':' . $suffix . '*';
            if (count($variants) >= 4) {
                break;
            }
        }

        if (!empty($variants)) {
            $parts[] = '(' . implode(' OR ', array_unique($variants)) . ')';
        }
    }

    if (empty($parts)) {
        return '';
    }

    return '(' . implode(' AND ', $parts) . ')';
}

// Fallback sans requete SQL: extrait le premier code et le ramene au millier (006020 -> 006000).
function fallbackCraigCodeFromSocialObject($socialObject): ?string {
    $raw = (string)$socialObject;
    if ($raw === '') {
        return null;
    }

    if (preg_match('/\d{3,}/', $raw, $m) !== 1) {
        return null;
    }

    $digits = preg_replace('/\D+/', '', $m[0]);
    if ($digits === '') {
        return null;
    }

    $digits = str_pad($digits, 6, '0', STR_PAD_LEFT);
    $base = intdiv((int)$digits, 1000) * 1000;

    return sprintf('%06d', $base);
}

$titleQuery = buildContainsFieldQuery('title', $query);
$shortTitleQuery = buildContainsFieldQuery('short_title', $query);

if ($titleQuery === '' && $shortTitleQuery === '') {
    exit;
}

// Filtre departement:
// - par defaut on utilise le NumeroDepartement de la table GUIDASSO
// - si l'utilisateur coche "Hors departement", le front envoie le champ saisi

$depFilter = isset($_POST['dep']) ? trim($_POST['dep']) : '';
if ($depFilter === '') {
    $depFilter = getNumeroDepartement() ?? '';
}
if ($depFilter !== '' && ctype_digit((string)$depFilter)) {
    $depFilter = str_pad((string)$depFilter, 2, '0', STR_PAD_LEFT);
}

$base = "https://public.opendatasoft.com/api/records/1.0/search/";
$params = [
    "dataset" => "ref-france-association-repertoire-national",
    "q"       => $titleQuery . ' OR ' . $shortTitleQuery,
    "rows"    => 1000 // plus large pour couvrir davantage de resultats
];

if ($depFilter !== '') {
    // API filtering by dep_code to avoid local post-filtering limits
    $params["refine.dep_code"] = $depFilter;
}

$url = $base . "?" . http_build_query($params, encoding_type: PHP_QUERY_RFC3986);

$response = file_get_contents($url);

if ($response === false) {
    echo "<ul><li>Erreur lors de l'appel à l'API OpenDataSoft</li></ul>";
    exit;
}

$data = json_decode($response, true);

if (empty($data['records'])) {
    echo "<ul><li>Aucune association trouvée</li></ul>";
    exit;
}

// On extrait les records pour pouvoir les trier par dep_code
$records = $data['records'];

// Tri par numéro de département (dep_code)
usort($records, function($a, $b) {
    $fa = $a['fields'] ?? [];
    $fb = $b['fields'] ?? [];

    $depA = $fa['dep_code'] ?? '99';
    $depB = $fb['dep_code'] ?? '99';

    return strcmp($depA, $depB);
});

echo "<ul>";

foreach ($records as $record) {
    if (!isset($record['fields']) || !is_array($record['fields'])) {
        continue;
    }

    $f = $record['fields'];

    $nomRaw     = $f['title'] ?? $f['short_title'] ?? '';
    $nom        = htmlspecialchars($nomRaw, ENT_QUOTES);
    $rna        = htmlspecialchars($f['id'] ?? '', ENT_QUOTES);
    $depCodeRaw = $f['dep_code'] ?? '';
    $depCode    = htmlspecialchars($depCodeRaw, ENT_QUOTES);
    $depName    = htmlspecialchars($f['dep_name'] ?? '', ENT_QUOTES);
    $commune    = htmlspecialchars($f['com_name_asso'] ?? $f['routed_address_manager'] ?? '', ENT_QUOTES);
    $siret      = htmlspecialchars($f['siret'] ?? '', ENT_QUOTES);
    $objetCode1Raw = $f['social_object1'] ?? '';
    $objetCode2Raw = $f['social_object2'] ?? '';
    $objetCode1 = htmlspecialchars($objetCode1Raw, ENT_QUOTES);
    $objetCode2 = htmlspecialchars($objetCode2Raw, ENT_QUOTES);
    $objetTxt   = htmlspecialchars($f['object'] ?? '', ENT_QUOTES);
    $activityMain = getCraigActivityFromSocialObject($objetCode1Raw);
    $activitySec = getCraigActivityFromSocialObject($objetCode2Raw);
    if (empty($activityMain)) {
        $activityMain = fallbackCraigCodeFromSocialObject($objetCode1Raw);
    }
    if (empty($activitySec)) {
        $activitySec = fallbackCraigCodeFromSocialObject($objetCode2Raw);
    }

    $streetNum  = htmlspecialchars($f['street_number_asso'] ?? '', ENT_QUOTES);
    $streetType = htmlspecialchars($f['street_type_asso'] ?? '', ENT_QUOTES);
    $streetName = htmlspecialchars($f['street_name_asso'] ?? '', ENT_QUOTES);
    $cp         = htmlspecialchars($f['pc_address_asso'] ?? '', ENT_QUOTES);

    if ($nom === '') {
        continue;
    }

    // Filtrer pour ne garder que les associations dont le NOM contient la chaîne recherchée
    if (stripos($nomRaw, $query) === false) {
        continue;
    }

// Texte affiché dans la liste 
    $label = trim($depCode . ' - ' . $nom . ' (' . $commune . ')');

    echo "
        <li
            data-nom=\"{$nom}\"
            data-rna=\"{$rna}\"
            data-dep-code=\"{$depCode}\"
            data-dep-name=\"{$depName}\"
            data-commune=\"{$commune}\"
            data-siret=\"{$siret}\"
            data-objet-code1=\"{$objetCode1}\"
            data-objet-code2=\"{$objetCode2}\"
            data-activity-main=\"" . htmlspecialchars($activityMain ?? '', ENT_QUOTES) . "\"
            data-activity-sec=\"" . htmlspecialchars($activitySec ?? '', ENT_QUOTES) . "\"
            data-objet-txt=\"{$objetTxt}\"
            data-street-num=\"{$streetNum}\"
            data-street-type=\"{$streetType}\"
            data-street-name=\"{$streetName}\"
            data-cp=\"{$cp}\"
        >
            <strong>{$label}</strong><br>
            <small>{$commune}</small>
        </li>
    ";
}

echo "</ul>";






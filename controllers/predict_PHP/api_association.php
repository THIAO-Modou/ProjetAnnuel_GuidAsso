<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['query']) || strlen(trim($_POST['query'])) < 2) {
    exit;
}

$query = urlencode($_POST['query']);

$base = "https://public.opendatasoft.com/api/records/1.0/search/";
$params = [
    "dataset" => "ref-france-association-repertoire-national",
    "q"       => $query,
    "rows"    => 50 // un peu plus large pour trier par département
];

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

    $nom        = htmlspecialchars($f['title'] ?? $f['short_title'] ?? '', ENT_QUOTES);
    $rna        = htmlspecialchars($f['id'] ?? '', ENT_QUOTES);
    $depCode    = htmlspecialchars($f['dep_code'] ?? '', ENT_QUOTES);
    $depName    = htmlspecialchars($f['dep_name'] ?? '', ENT_QUOTES);
    $commune    = htmlspecialchars($f['com_name_asso'] ?? $f['routed_address_manager'] ?? '', ENT_QUOTES);
    $siret      = htmlspecialchars($f['siret'] ?? '', ENT_QUOTES);
    $objetCode1 = htmlspecialchars($f['social_object1'] ?? '', ENT_QUOTES);
    $objetCode2 = htmlspecialchars($f['social_object2'] ?? '', ENT_QUOTES);
    $objetTxt   = htmlspecialchars($f['object'] ?? '', ENT_QUOTES);

    $streetNum  = htmlspecialchars($f['street_number_asso'] ?? '', ENT_QUOTES);
    $streetType = htmlspecialchars($f['street_type_asso'] ?? '', ENT_QUOTES);
    $streetName = htmlspecialchars($f['street_name_asso'] ?? '', ENT_QUOTES);
    $cp         = htmlspecialchars($f['pc_address_asso'] ?? '', ENT_QUOTES);

    if ($nom === '') {
        continue;
    }

    // Texte affiché dans la liste : "31 - AGAMA (Toulouse)"
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
            data-objet-txt=\"{$objetTxt}\"
            data-street-num=\"{$streetNum}\"
            data-street-type=\"{$streetType}\"
            data-street-name=\"{$streetName}\"
            data-cp=\"{$cp}\"
        >
            <strong>{$label}</strong><br>
            <small>{$cp} {$commune}</small>
        </li>
    ";
}

echo "</ul>";

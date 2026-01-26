<?php 
session_start();
//Vider la saission des boutons
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    unset($_SESSION['bouton_monEspace_actif']);
}

error_log(" Email stocké en session: " . ($_SESSION['MAIL'] ?? 'Aucun'));

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';
include_once __DIR__ . '/../controllers/user/get_user_info.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: /views/pageconnexion.php");
    exit();
}
$email = $_SESSION['MAIL'];
// Recuperation des information de l'utilisateur connecté
$user = getUserInfoByEmail($_SESSION['MAIL']);
$boutonClique = $_SESSION['bouton_monEspace_actif'] ?? null;

if ($user) {
    $nom = $user['NOMPERSONNE'];
    $prenom = $user['PRENOMPERSONNE'];
    $idFonction = $user['IDFONCTION'];
    $classification = $user['CLASSIFICATION'];
    $role = $user['NAMEFONCTION'];
    // Information à envoyer au fichier JS
    $data = [
        "NOMPERSONNE" => $user['NOMPERSONNE'],
        "PRENOMPERSONNE" => $user['PRENOMPERSONNE'],
        "IDFONCTION" => $user['IDFONCTION'],
        "CLASSIFICATION" => $user['CLASSIFICATION']
    ];
} else {
    $prenom = "Utilisateur inconnu";
}
//  Envoi des infos sous forme de JSON accessible en JavaScript
echo "<script>var userData = " . json_encode($data) . ";</script>";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exploitation statistique</title>
  
  <!-- Inclusion de Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

  <!-- Inclusion de jsPDF -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  
  <!-- Inclusion de html2canvas -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  
  <link rel="stylesheet" href="stylestats.css">
  <link rel="stylesheet" href="/../public/css/responsive_home.css">
</head>

<body>
    <div class="image-container">
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoRéseau1.png" alt="Logo Réseau">
	</a>
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoInformation.png" alt="Logo Information">
	</a>
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoOrientation1.png" alt="Logo Orientation">
	</a>
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoAccompagnementG1.png" alt="Logo Accompagnement">
	</a>
</div>

    <div class="setting-button-container">
        <a href="https://guide-asso-m2.geniephy.net/views/pageadmin.php" class="setting-button">Page Admin</a>
        <a href="https://guide-asso-m2.geniephy.net/views/questionnaire.php" class="setting-button">Questionnaire</a>
        <a href="/../config/deconnexion.php" class="setting-button">Se déconnecter</a>
    </div>

  <h1>Page de Statistiques</h1>

  <form id="statisticsForm">
    <label for="menuPrincipal">Choix graphique :</label>
    <select id="menuPrincipal">
        <option value="">-- Sélectionnez une option --</option>
        <option value="departement">Département</option>
        <option value="epci">EPCI</option>
    </select>
    
    <!-- Sous-menu pour le département -->
    <select id="menuDepartement" name="choix" style="display:none;">
        <option value="graph1">Nb tot assos accompagnées par EPCI</option>
        <option value="graph2">Nb tot assos accompagnées par thém. activité</option>
        <option value="graph3">Nb tot assos accompagnées par thém. questions</option>
        <option value="graph4">Nb tot assos accompagnées par type de rendez-vous</option>
        <option value="graph5">Nb tot assos accompagnées par classification Guid'Asso</option>
    </select>
    
    <!-- Sous-menu pour choisir un EPCI -->
    <select id="menuEPCI" style="display:none;">
        <option value="">-- Sélectionnez un EPCI --</option>
        <option value="grand-poitiers">Grand-Poitiers</option>
        <option value="haut-poitou">Haut-Poitou</option>
        <option value="vallees-clain">Vallées du Clain</option>
        <option value="grand-chatellerault">Grand-Châtellerault</option>
        <option value="vienne-gartempe">Vienne-et-Gartempe</option>
        <option value="civraisien-poitou">Civraisien-en-Poitou</option>
        <option value="pays-loudunais">Pays Loudunais</option>
    </select>
    
    <!-- Sous-menu pour les graphiques de chaque EPCI -->
    <select id="menuGraphEPCI" name="choix" style="display:none;"></select>

    <script src="script_menu.js"></script>

    <label for="typegraph">Type de graphique :</label>
    <select id="typegraph" name="typegraph">
      <option value="bar">Barres verticales</option>
      <option value="pie">Camembert</option>
    </select><br>

    <label for="legende">Emplacement légende :</label>    <select id="legende" name="legende">
      <option value="top">En haut</option>
      <option value="bottom">En bas</option>
      <option value="right">A droite</option>
    </select><br>

    <button type="button" id="generateButton">Générer le graphique</button>
  </form>

  <!-- Zone d'affichage du graphique -->
  <canvas id="myChart" style="max-width: 830px; max-height: 600px;"></canvas>


  <!-- Bouton pour exporter le graphique en PDF -->
  <div class="button-container">
    <button id="exportPdf">Exporter en PDF</button>
  </div>
  <br><br><br><br><br><br><br><br>
    <!--BANDEROLE FIN DE PAGE-->
    <section id="footer" class="footer">
    <div class="footer-content">
        <div class="footer-text">
            <p>Guid'Asso</p>
            <p>Patrice Mancino : 06 07 08 09 10</p>
            <p>Assistance client : vieasso86@guidasso86.fr</p>
        </div>
        <div class="footer-image">
          <a href="/../views/pageadmin.php" class="btn-home" title="Retour à l'accueil">
              <img src="/../public/img/home.png" alt="Accueil" />
          </a>
          <img src="/../public/img/CRAIG.png" alt="Logo" style="max-width: 100px; border-radius: 50%;">
        </div>
    </div>
</section>
  <script>
    let chartInstance;
    let titreGraphique = "";

    // Gestionnaire pour générer le graphique

    document.getElementById("generateButton").addEventListener("click", function() {
      let graphChoice;

      // Vérifie quel menu est sélectionné

      const choixPrincipal = document.getElementById("menuPrincipal").value;

      if (choixPrincipal === "departement") {
          graphChoice = document.getElementById("menuDepartement").value;
      } else if (choixPrincipal === "epci") {
          graphChoice = document.getElementById("menuGraphEPCI").value;
      }

      if (!graphChoice) {
        alert("Veuillez sélectionner un graphique !");
        return; // Stoppe l'exécution si aucune sélection

      }
      const typeGraph = document.getElementById("typegraph").value;
      const legende = document.getElementById("legende").value;
      
      const titresGraphiques = {
        "graph1": "Total d'associations accompagnées par EPCI",
        "graph2": "Total d'associations accompagnées par activité principale",
        "graph3": "Total d'associations accompagnées en fonction de la thématique question",
        "graph4": "Distribution des types de rendez-vous",
        "graph5": "Total d'associations accompagnées en fonction de la classification Guid'Asso",
        "graph6": "Total d'associations accompagnées de l'EPCI Grand-Poitiers par activité principale",
        "graph7": "Total d'associations accompagnées de l'EPCI Haut-Poitou par activité principale",
        "graph8": "Total d'associations accompagnées de l'EPCI Vallées du clain par activité principale",
        "graph9": "Total d'associations accompagnées de l'EPCI Grand-Châtellerault par activité principale",
        "graph10": "Total d'associations accompagnées de l'EPCI Vienne-et-Gartempe par activité principale",
        "graph11": "Total d'associations accompagnées de l'EPCI Civraisien-en-Poitou par activité principale",
        "graph12": "Total d'associations accompagnées de l'EPCI Pays loudunais par activité principale",
        "graph13": "Total d'associations accompagnées de l'EPCI Grand-Poitiers en fonction de la thématique question",
        "graph14": "Total d'associations accompagnées de l'EPCI Haut-Poitou en fonction de la thématique question",
        "graph15": "Total d'associations accompagnées de l'EPCI Vallées du clain en fonction de la thématique question",
        "graph16": "Total d'associations accompagnées de l'EPCI Grand-Châtellerault en fonction de la thématique question",
        "graph17": "Total d'associations accompagnées de l'EPCI Vienne-et-Gartempe en fonction de la thématique question",
        "graph18": "Total d'associations accompagnées de l'EPCI Civraisien-en-Poitou fonction de la thématique question",
        "graph19": "Total d'associations accompagnées de l'EPCI Pays loudunais en fonction de la thématique question",

      };

      titreGraphique = titresGraphiques[graphChoice] || "Statistiques des associations";

      fetch('requete_data.php', {
        method: 'POST',
        body: new FormData(document.getElementById("statisticsForm"))
      })
      //.then(response => response.json())
      .then(response => response.text())
      .then(text => {
        if (!text) throw new Error("Réponse vide du serveur");
        try {
          return JSON.parse(text);
        } catch (err) {
          console.error("⛔ JSON mal formé :", text);
          throw err;
        }
      })

      .then(data => {
        if (data.error) return console.error(data.error);

        const sortedData = data.sort((a, b) => b.total - a.total);
        const labels = sortedData.map(row => row.epci || row.ACTIVITEPRINCIPALEASSO || row.THEMEGENERAL || row.categorie || row.CLASSIFICATIONGUIDASSO);
        const values = sortedData.map(row => row.total);

        const couleurs = [
          'rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)', 'rgba(255, 159, 64, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)',
          'rgba(255, 205, 86, 0.5)', 'rgba(231, 76, 60, 0.5)', 'rgba(46, 204, 113, 0.5)', 'rgba(52, 152, 219, 0.5)', 'rgba(155, 89, 182, 0.5)',
          'rgba(241, 196, 15, 0.5)', 'rgba(39, 174, 96, 0.5)', 'rgba(244, 67, 54, 0.5)', 'rgba(155, 89, 182, 0.5)', 'rgba(41, 128, 185, 0.5)',
          'rgba(142, 68, 173, 0.5)', 'rgba(26, 188, 156, 0.5)', 'rgba(52, 152, 219, 0.5)', 'rgba(231, 76, 60, 0.5)', 'rgba(236, 240, 241, 0.5)'
        ];

        const datasetColors = labels.map((_, index) => couleurs[index % couleurs.length]);

        if (chartInstance) chartInstance.destroy();
        
        Chart.register(ChartDataLabels);

        chartInstance = new Chart(document.getElementById('myChart'), {
          type: typeGraph,
          data: {
            labels: labels,
            datasets: [{
              label: 'Nombre d\'associations',
              data: values,
              backgroundColor: datasetColors,
              borderColor: datasetColors,
              borderWidth: 1

            }]
          },
          options: {
            responsive: true,
            plugins: {
              layout: {
                padding: {
                top: 20,
                bottom: 20,
                right: 20

                }
              },      
              legend: {
                display: true,
                position: legende, 
                labels: {
                  padding: 10, 
                  font: { size: 12 } 
                }
              },
              title: {
                display: true,
                text: titreGraphique,
                font: { size: 18 },
                padding: 20 
              },
              datalabels: {  
                anchor: 'end',  
                align: 'center',  
                color: 'rgba(0, 0, 0, 0.5)',  
                font: { weight: 'bold', size: 11 },  
                formatter: (value) => value,  
                padding: 10,  
                offset: 5,  
                //clip: true,  
            }
            },
            scales: { y: { beginAtZero: true } }
          }
        });
      })
      .catch(error => console.error("Erreur lors du chargement des données : ", error));
    });

    // Exporter le graphique en PDF

    document.getElementById("exportPdf").addEventListener("click", function() {
    const canvas = document.getElementById("myChart");
    const margin = 10; // Marge autour du graphique


    html2canvas(canvas, { scale: 2 }).then(canvas => {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

        const pageWidth = pdf.internal.pageSize.getWidth() - (2 * margin);
        const pageHeight = pdf.internal.pageSize.getHeight() - (2 * margin);

        let imgWidth = pageWidth;
        let imgHeight = (canvas.height * imgWidth) / canvas.width;

        if (imgHeight > pageHeight) {
            imgHeight = pageHeight;
            imgWidth = (canvas.width * imgHeight) / canvas.height;
        }

        pdf.addImage(canvas.toDataURL("image/png"), "PNG", margin, margin, imgWidth, imgHeight);
        pdf.save(titreGraphique.replace(/\s+/g, "-") + ".pdf");
    });
  });

  </script>
</body>
</html>
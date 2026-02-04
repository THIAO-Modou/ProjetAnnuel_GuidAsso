//--------------------------------------------------------------------------------------
//------------------------------- INFO UTILISATEUR -------------------------------------
//--------------------------------------------------------------------------------------

if (userData.error) {
    console.error(" Erreur :", userData.error);
} else {
    console.log(" Infos utilisateur :", userData);

    // Stocker les infos utilisateur dans sessionStorage
    const Nom = sessionStorage.setItem("NOMPERSONNE", userData.NOMPERSONNE);
    const Prenom = sessionStorage.setItem("PRENOMPERSONNE", userData.PRENOMPERSONNE);
    sessionStorage.setItem("IDFONCTION", userData.IDFONCTION);
    sessionStorage.setItem("CLASSIFICATION", userData.CLASSIFICATION);
}
//--------------------------------------------------------------------------------------
//------------------------------- VISIONNEUSE DE MON ESPACE --------------------------
//--------------------------------------------------------------------------------------

$(document).ready(function(){
    // --------- Initialisation des variables pour la pagination -------
    let currentPage = 1;
    let totalPages = 1;
    let nbLignes = $('#nbLignes').val();

     // ------------ Adaptation responsive de la pagination -----------------
    // Si l’écran est petit (mobile), on affiche moins de lignes
    if (window.innerWidth <= 768) {
        $('#nbLignes').val("5").trigger("change");
    }

    // -------- Fonction principale pour charger les entrées ----------------
    function loadEntries(page = 1) {
        $.ajax({
            url: "/../controllers/visionneuse/get_user_entries.php",
            method: "GET",
            data: { nbLignes, page },
            success: function(response) {
                if (!response.entries || !Array.isArray(response.entries)) {
                    console.error(" Erreur : Les données ne sont pas un tableau valide :", response);
                    $('#visionneuse_admin_monEspace').html("<p>Aucune entrée trouvée.</p>");
                    return;
                }
                // Référence avec l'id vers la zone d’affichage des entrées
                let visionneuse = $('#visionneuse_admin_monEspace');
                visionneuse.empty();

                // Met à jour les infos de pagination
                totalPages = response.totalPages || 1;
                $('#pageInfo').text(`Page ${page} / ${totalPages}`);

                // Active/désactive les boutons de navigation
                $('#prevPage').prop('disabled', page <= 1);
                $('#nextPage').prop('disabled', page >= totalPages);

                // Affiche les entrées si elles existent
                if (response.entries.length > 0) {
let table = `
<table border="1" cellspacing="0" cellpadding="5">
	<thead>
    	<tr>
        	<th>Horodateur</th>
        	<th>Accompagnement</th>
        	<th>Nom contact</th>
        	<th>Thématique</th>
        	<th>Référent Guid'Asso</th>
        	<th>Email Référent</th>
    	</tr>
	</thead>
	<tbody></tbody>
</table>`;
visionneuse.append(table);

let tbody = visionneuse.find('tbody');
response.entries.forEach(entry => {
	tbody.append(`
    	<tr>
        	<td>${entry.HORODATEUR}</td>
        	<td>${entry.NOMASSO || ""} ${entry.NOMSTRUCTURE || ""} ${entry.NOMEVENEMENT || ""}</td>
        	<td>${entry.CIVILITE || ""} ${entry.NOMCONTACT || ""}</td>
        	<td>${entry.THEMEGENERAL || ""} ${entry.AUTRETHEMATIQUE || ""}</td>
        	<td>${entry.NOMUTILISATEUR || ""} ${entry.PRENOMUTILISATEUR || ""}</td>
        	<td>
            	<a href="https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(entry.EMAILUtilisateur)}"
   target="_blank"
   style="text-decoration:none;font-size:24px;">
   📧
</a>

        	</td>
    	</tr>`);
});

                } else {
                    visionneuse.html("<p>Aucune entrée trouvée.</p>");
                }
            },
            error: function(xhr, status, error) {
                console.error(" Erreur AJAX :", error);
                $('#visionneuse_admin_monEspace').html("<p>Erreur lors du chargement des données.</p>");
            }
        });
    }
    // -------- Chargement initial des entrées ----------------
    loadEntries();

    // -------- Quand l'utilisateur change le nombre de lignes par page -------
    $('#nbLignes').on('change', function() {
        nbLignes = $(this).val();
        currentPage = 1;
        loadEntries(currentPage);
    });

    // -------- Navigation : Page suivante -------
    $('#nextPage').on('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadEntries(currentPage);
        }
    });

    // -------- Navigation : Page précédente -------
    $('#prevPage').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadEntries(currentPage);
        }
    });

    //-----------------------------------------------------------------------
    //----------------------------- IMPRESSION EN PDF -----------------------
    //-----------------------------------------------------------------------
    $('#btnExportPdf').on('click', function () {
    $.ajax({
        url: "/../controllers/visionneuse/get_user_entries.php",
        method: "GET",
        data: { nbLignes: 500, page: 1 },
        success: function (response) {
            if (!response.entries || !Array.isArray(response.entries)) {
                alert("Les données reçues sont invalides.");
                return;
            }

            //Création du fichier PDF avec format A4 paysage
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });

            //Génère un horodatage pour nommer le fichier PDF
            const now = new Date();
            const timestamp = now.toLocaleDateString('fr-FR').replace(/\//g, '-') + '_' +
                now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }).replace(/:/g, '-');

            //Titre en haut de page
            doc.setFontSize(12);
            const nom = sessionStorage.getItem("NOMPERSONNE");
            doc.text(`${nom.toUpperCase() || ""} ${sessionStorage.getItem("PRENOMPERSONNE") || ""} - CRAIG - Mes réponses - (${timestamp})`, 14, 20);

            //Date d’export
            doc.setFontSize(12);
            doc.text(`Date : ${timestamp}`, 14, 26);

            //En-têtes du tableau
            const headers = [["Horodateur", "Accompagnement", "Contact", "Thématique", "Référent"]];
            
            //Remplissage du tableau ligne par ligne
            const rows = response.entries.map(entry => [
                entry.HORODATEUR || "",
                [entry.NOMASSO, entry.NOMSTRUCTURE, entry.NOMEVENEMENT].filter(Boolean).join(" "),
                [entry.CIVILITE, entry.NOMCONTACT].filter(Boolean).join(" "),
                [entry.THEMEGENERAL, entry.AUTRETHEMATIQUE].filter(Boolean).join(" "),
                [entry.NOMUTILISATEUR, entry.PRENOMUTILISATEUR].filter(Boolean).join(" ")
            ]);

            //Insertion du tableau dans le PDF
            doc.autoTable({
                //Styles personnalisés du tableau
                head: headers,
                body: rows,
                startY: 28, // Point de départ vertical après le titre
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [0, 102, 128], textColor: 255 },
                alternateRowStyles: { fillColor: [245, 245, 245] },
                margin: { top: 20 },
                theme: 'grid',

                // Numerotation automatique des page
                didDrawPage: function (data) {
                    const pageCount = doc.internal.getNumberOfPages();
                    const pageSize = doc.internal.pageSize;
                    const pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();

                    doc.setFontSize(8);
                    doc.text(`Page ${doc.internal.getCurrentPageInfo().pageNumber} / {totalPages}`, 
                            pageSize.width - 30, pageHeight - 10);
                }

            });
            // Remplace la balise par le vrai total après génération
            doc.putTotalPages("{totalPages}");
            //Sauvegarde avec le nom visionneuse-Mon-Espace suivie de l'heure
            
            doc.save(`${nom.toUpperCase() || ""} ${sessionStorage.getItem("PRENOMPERSONNE") || ""} - CRAIG - REPORT -(${timestamp}).pdf`);
        },
        //En cas d’échec AJAX
        error: function () {
            alert(" Erreur lors de la récupération des données.");
        }
    });
});


});


//--------------------------------------------------------------------------------------
//------------------------------- PIECES JOINTES  ------------------------------------
//--------------------------------------------------------------------------------------
// Quand le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", () => {
  // -------- Initialisation des variables de pagination --------
  let currentPage = 1;      // Page actuelle
  let totalPages = 1;       // Nombre total de pages
  let nbLignes = 5;         // Nombre d'éléments par page

  // -------- Fonction pour charger les pièces jointes --------
  function loadPiecesJointes(page = 1) {
    // Appel de l'API pour récupérer les fichiers
    fetch(`/../controllers/admin/get_file.php?page=${page}&nbLignes=${nbLignes}`)
      .then(res => res.json())
      .then(data => {
        const tbody = document.querySelector("#fileTable tbody");
        tbody.innerHTML = ""; // Nettoie le tableau avant remplissage

        // Si aucun fichier n’est renvoyé
        if (!data.files || data.files.length === 0) {
          tbody.innerHTML = "<tr><td colspan='5'>Aucun fichier enregistré.</td></tr>";
          document.getElementById("pageInfo-pj").textContent = "Page 0";
          document.getElementById("prevPage-pj").disabled = true;
          document.getElementById("nextPage-pj").disabled = true;
          return;
        }

        // Mise à jour des infos de pagination
        totalPages = data.totalPages;
        document.getElementById("pageInfo-pj").textContent = `Page ${page} / ${totalPages}`;
        document.getElementById("prevPage-pj").disabled = page <= 1;
        document.getElementById("nextPage-pj").disabled = page >= totalPages;

        // Pour chaque fichier reçu, on construit une ligne dans le tableau
        data.files.forEach(file => {
            const filePath = file.FILE ? `/../controllers/questionnaire/${file.FILE}` : "#";
            const downloadURL = file.DOWNLOAD_URL || "#";

            // Gestion de l'aperçu si le fichier existe
            const filePreview = file.FILE
                ? `<a href="${filePath}" target="_blank" class="preview-btn"><img src="/../public/img/apercu.png" alt="Aperçu"> Aperçu</a>`
                : `<span class="no-file">Aucun aperçu</span>`;

                // Téléchargement uniquement si un fichier est disponible
            const fileDownload = file.FILE
            ? `<a href="${downloadURL}" class="download-btn"><img src="/../public/img/telechargement.jpg" alt="Télécharger"> Télécharger</a>`
            : `<span class="no-file">Aucun fichier</span>`;

            // Construction de la ligne du tableau
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${file.HORODATEUR}</td>
                <td>${file.NOMEVENEMENT}</td>
                <td>${file.FILE_NAME}</td>
                <td class="file-actions">${filePreview}</td>
                <td class="file-actions">${fileDownload}</td>
                <td><button class="delete-pj" data-id="${file.IDQUESTIONNAIRE}">
                <img src="/../public/img/delete-icon.png" alt="Supprimer">
                </button></td>
            `;
            tbody.appendChild(row);
        });

        // Ajout de l’écoute sur les boutons de suppression
        document.querySelectorAll(".delete-pj").forEach(button => {
          button.addEventListener("click", () => {
            const id = button.dataset.id;

            if (confirm("Voulez-vous vraiment supprimer ce fichier ?")) {
                // Requête POST pour supprimer le fichier
              fetch("/../controllers/admin/get_file.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `idquestionnaire=${encodeURIComponent(id)}`
              })
                .then(res => res.json())
                .then(response => {
                  if (response.success) {
                    loadPiecesJointes(currentPage);
                  } else {
                    alert("Erreur lors de la suppression.");
                  }
                })
                .catch(() => {
                  alert("Une erreur est survenue.");
                });
            }
          });
        });
      })
      .catch(error => {
        console.error(" Erreur JSON :", error);
        alert("Erreur lors du chargement des fichiers.");
      });
  }

  // -------- Gestion du changement du nombre de lignes --------
  document.getElementById("nbLignes-pj").addEventListener("change", e => {
    nbLignes = parseInt(e.target.value);
    currentPage = 1;
    loadPiecesJointes(currentPage);
  });

  // -------- Navigation vers la page suivante --------
  document.getElementById("nextPage-pj").addEventListener("click", () => {
    if (currentPage < totalPages) {
      currentPage++;
      loadPiecesJointes(currentPage);
    }
  });

  // -------- Navigation vers la page précédente --------
  document.getElementById("prevPage-pj").addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--;
      loadPiecesJointes(currentPage);
    }
  });

  // -------- Chargement initial des fichiers --------
  loadPiecesJointes(currentPage);
});

//---------------------------------------------------------------------------------
//-------------------------------VISIONNEUSE BLOCS NOTES  -------------------------
//---------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function () {
     //Variables pour la pagination
    let currentPage = 1;
    let totalPages = 1;
    let nbLignes = 10;

    function loadBlocsNotes(page = 1) {
        // Appel pour récupérer les blocs-notes en fonction du nombre de ligne
        fetch(`/../controllers/admin/get_pjbn.php?page=${page}&nbLignes=${nbLignes}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector("#blocsNotesTable tbody");
                tableBody.innerHTML = ""; // vider le tableau avant de charger les nouvelles données

                if (!data.files || data.files.length === 0) { //gestion cas si aucun blocnote on met ça dans le tableau 
                    tableBody.innerHTML = "<tr><td colspan='9'>Aucun bloc-note enregistré.</td></tr>";
                    document.getElementById("pageInfo-bloc").textContent = "Page 0";
                    document.getElementById("prevPage-bloc").disabled = true;
                    document.getElementById("nextPage-bloc").disabled = true;
                    return;
                }
                // Gestion de la navigation entre les pages selon le nombre de lignes affichées
                totalPages = data.totalPages || 1;
                document.getElementById("pageInfo-bloc").textContent = `Page ${page} / ${totalPages}`;
                document.getElementById("prevPage-bloc").disabled = page <= 1;
                document.getElementById("nextPage-bloc").disabled = page >= totalPages;

                data.files.forEach(file => {
                    // Vérification et remplacement des valeurs nulles avec classe "no-file" pour griser
                    const horodateur = file.HORODATEUR || `<span class="no-file">N/A</span>`;
                    const nomAsso = file.NOMASSO || `<span class="no-file">N/A</span>`;
                    const themeGeneral = file.THEMEGENERAL || `<span class="no-file">N/A</span>`;
                    const blocNote = file.BLOCNOTE || `<span class="no-file">N/A</span>`;
                    const fileName = file.FILE_NAME || `<span class="no-file">Aucune PJ</span>`;
                    const uploadedBy = file.UPLOADED_BY || `<span class="no-file">Inconnu</span>`;
                    const filePath = file.FILE ? `/controllers/questionnaire/${file.FILE}` : "#";
                    const downloadURL = file.DOWNLOAD_URL || "#";

                    // Gestion de l'aperçu si le fichier existe
                    const filePreview = file.FILE
                        ? `<a href="${filePath}" target="_blank" class="preview-btn"><img src="/../public/img/apercu.png" alt="Aperçu"> Aperçu</a>`
                        : `<span class="no-file">Aucun aperçu</span>`;

                     // Téléchargement uniquement si un fichier est disponible
                    const fileDownload = file.FILE
                        ? `<a href="${downloadURL}" class="download-btn"><img src="/../public/img/telechargement.jpg" alt="Télécharger"> Télécharger</a>`
                        : `<span class="no-file">Aucun fichier</span>`;

                     // Ajout d’une nouvelle ligne dans le tableau avec toutes les informations
                    const row = `<tr>
                        <td>${horodateur}</td>
                        <td>${nomAsso}</td>
                        <td>${themeGeneral}</td>
                        <td>${blocNote}</td>
                        <td>${fileName}</td>
                        <td>${uploadedBy}</td>
                        <td class="file-actions">${filePreview}</td>
                        <td class="file-actions">${fileDownload}</td>
                        <td><button class="delete-blocNote" data-id="${file.IDQUESTIONNAIRE}">
                            <img src="/../public/img/delete-icon.png" alt="Supprimer"></button>
                        </td>
                    </tr>`;
                    tableBody.innerHTML += row;
                });

                // Ajout de l’écoute sur les boutons de suppression
                document.querySelectorAll(".delete-blocNote").forEach(btn => {
                    btn.addEventListener("click", function () {
                        let idquestionnaire = this.dataset.id;
                        if (confirm("Voulez-vous vraiment supprimer ce bloc note ?")) {
                            deleteBlocNotes(idquestionnaire);
                        }
                    });
                });
            })
            .catch(error => console.error(" Erreur lors du chargement des blocs-notes :", error));
    }

    //Fonction de suppression de bloc Note
    function deleteBlocNotes(idquestionnaire) {
         // Vérifie si l'utilisateur connecté est un "Visiteur" (IDFONCTION = 3)
        if (sessionStorage.getItem("IDFONCTION") == 4) {
            alert(" Vous n'avez pas le droit de supprimer un bloc note..");
            return;
        }
        $.ajax({
            // Requête POST pour supprimer le fichier
            url: "/../controllers/admin/get_pjbn.php",
            method: "POST",
            data: { idquestionnaire },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    alert("Bloc note supprimé avec succès !");
                    loadBlocsNotes(currentPage);
                } else {
                    alert(` Erreur lors de la suppression : ${response.error}`);
                }
            },
            error: function (xhr) {
                console.error(" Erreur AJAX lors de la suppression :", xhr.responseText);
                alert(" Une erreur est survenue !");
            }
        });
    }

    // Gestion de la navigation ou chargement de nombre de lignes
    document.getElementById("nbLignes-bloc").addEventListener("change", function () {
        nbLignes = this.value;
        currentPage = 1;
        loadBlocsNotes(currentPage);
    });

    // -------- Navigation vers la page suivante --------
    document.getElementById("nextPage-bloc").addEventListener("click", function () {
        if (currentPage < totalPages) {
            currentPage++;
            loadBlocsNotes(currentPage);
        }
    });

    // -------- Navigation vers la page précédente --------
    document.getElementById("prevPage-bloc").addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            loadBlocsNotes(currentPage);
        }
    });

    // -------- Chargement initial des fichiers --------
    loadBlocsNotes(currentPage);
});

//--------------------------------------------------------------------------------------
//------------------------------- EXPORT BDD  ------------------------------------
//--------------------------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {
    console.log(" Script Admin chargé et exécuté.");

    // Sélection des éléments HTML nécessaires
    const exportContainer = document.getElementById("import-export-form-container");
    const gestionBddButton = document.querySelector(".dashboard-button-green-export"); 
    const exportButton = document.getElementById("exportButton"); 
    const dateDebutInput = document.getElementById("start"); // Champ Date début (optionnel)
    const dateFinInput = document.getElementById("end");     // Champ Date fin (optionnel)

    /**
     * Affiche uniquement le conteneur Import/Export en masquant les autres sections admin
     */
    function showImportExportContainer() {
        document.querySelectorAll('.admin-section').forEach(section => {
            if (section !== exportContainer) {
                section.style.display = 'none';
            }
        });

        if (exportContainer) {
            exportContainer.style.display = 'block';

            //  Animation pour centrer la vue
            setTimeout(() => {
                exportContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        } else {
            console.error(" Erreur : Impossible de trouver le conteneur Import/Export.");
        }
    }

    /**
     * Si le bouton “Gestion BDD” est présent, on active le clic
     */
    if (gestionBddButton) {
        gestionBddButton.addEventListener("click", showImportExportContainer);
    } else {
        console.error(" Erreur : Bouton Gestion BDD introuvable.");
    }

    /**
      Fonction principale d’exportation CSV
     */
    if (exportButton) {
        exportButton.addEventListener("click", function () {
            event.preventDefault();
            console.log(" Exportation en cours...");

            //  Générer la date actuelle formatée pour nommer le fichier
            const now = new Date();
            const formattedDate = now.getFullYear() + "-" + 
                                  String(now.getMonth() + 1).padStart(2, '0') + "-" + 
                                  String(now.getDate()).padStart(2, '0') + "_" + 
                                  String(now.getHours()).padStart(2, '0') + "-" + 
                                  String(now.getMinutes()).padStart(2, '0') + "-" + 
                                  String(now.getSeconds()).padStart(2, '0');

            //  Récupération des dates si elles existent
            const dateDebut = dateDebutInput?.value;
            const dateFin = dateFinInput?.value;

            // Création du tableau de paramètres
            const params = [];

            // Récupération des utilisateurs sélectionnés (checkbox)
            const selectedUsers = Array.from(
                document.querySelectorAll("input[name='users[]']:checked")
            ).map(cb => cb.value);

            // Ajout des utilisateurs dans les paramètres GET
            if (selectedUsers.length > 0) {
                selectedUsers.forEach(user => {
                    params.push(`users[]=${encodeURIComponent(user)}`);
                });
            }

            if (dateDebut) params.push(`start=${encodeURIComponent(dateDebut)}`);
            if (dateFin) params.push(`end=${encodeURIComponent(dateFin)}`);

            // Construction dynamique de l’URL 
            let url = "/../database/export.php";
            if (params.length > 0) { 
                url += "?" + params.join("&"); 
            }

            // Lancement de la requête Ajax
            const xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.responseType = "blob"; // On s’attend à un fichier binaire (CSV)

            // Quand le fichier est reçu correctement
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const blob = xhr.response;
                    const link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = `.._BDD_${formattedDate}.csv`;
                    link.click();

                    console.log(" Exportation terminée.");
                    console.log("URL envoyée :", url);
                    console.log("Utilisateurs sélectionnés :", selectedUsers);

                } else {
                    console.error(" Erreur lors de l'exportation :", xhr.status);
                }
            };

            //  En cas d’erreur réseau
            xhr.onerror = function () {
                console.error(" Une erreur s'est produite lors de la requête.");
            };

            xhr.send(); // Envoie la requête vers export.php
        });
    } else {
        console.error(" Erreur : Bouton Export introuvable.");
    }
});


//--------------------------------------------------------------------------------------
//------------------------------- GESTION DU FOOTER ------------------------------------
//--------------------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const footer = document.getElementById('footer');
    let lastScrollY = window.scrollY;

    window.addEventListener('scroll', function () {
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        // Afficher le footer si on est en bas ou en haut de la page
        if (scrollY === 0 || scrollY + windowHeight >= documentHeight - 10) {
            footer.classList.remove('hidden-footer');
            footer.classList.add('visible-footer');
        } 
        // Masquer ou afficher selon le défilement
        else if (scrollY > lastScrollY) {
            footer.classList.remove('visible-footer');
            footer.classList.add('hidden-footer');
        } else {
            footer.classList.remove('hidden-footer');
            footer.classList.add('visible-footer');
        }

        lastScrollY = scrollY;
    });
});


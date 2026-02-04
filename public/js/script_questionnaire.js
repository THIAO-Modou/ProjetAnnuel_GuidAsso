document.addEventListener("DOMContentLoaded", function () {
    console.log("Script chargé et exécuté.");

    // Sélections des éléments utiles dans la page
    const forms = document.querySelectorAll('.formulaire-container-vert, .formulaire-container-bleu'); // Tous les formulaires
    const visionneuseContainer = document.getElementById('visionneuse-container'); // Visionneuse
    const errorContainers = document.querySelectorAll('.error-container'); // Tous les conteneurs d'erreur
    const successContainers = document.querySelectorAll('.success-container'); // Tous les conteneurs de succès

    // Fonction pour masquer tous les formulaires
    function hideAllForms() {
        forms.forEach(form => form.style.display = 'none'); // Cache tous les formulaires
    }

    // Fonction qui détecte les erreurs et affiche uniquement le formulaire concerné
    function showRelevantForm() {
        let formToShow = null;
        let hasError = false;

        errorContainers.forEach(errorContainer => {
            if (errorContainer.innerText.trim() !== '') {
                formToShow = errorContainer.closest('.formulaire-container-vert, .formulaire-container-bleu');
                hasError = true;
            }
        });

        if (formToShow) {
            hideAllForms(); // Cache tous les formulaires
            formToShow.style.display = 'block'; // Affiche le bon formulaire
            visionneuseContainer.style.display = 'none';
            setTimeout(() => {
                formToShow.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        } else {
            visionneuseContainer.style.display = 'block';
        }
    }
    showRelevantForm(); // Vérifie au chargement s’il faut afficher un formulaire spécifique

    // Gestion des boutons qui affichent un formulaire spécifique
    document.querySelectorAll('.dashboard-button-green, .dashboard-button-blue').forEach(button => {
        button.addEventListener('click', function () {
            // Récupère l’id du formulaire à afficher depuis l’attribut onclick
            const targetId = this.getAttribute('onclick').match(/showForm\('(.+?)'\)/)[1];
            hideAllForms();
            const targetForm = document.getElementById(targetId);
            if (targetForm) {
                targetForm.style.display = 'block';
                visionneuseContainer.style.display = 'none';
                setTimeout(() => {
                    targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        });
    });

    // Fonction pour afficher ou non la visionneuse selon les formulaires visibles
    function checkAndShowVisionneuse() {
        let anyFormVisible = Array.from(forms).some(form => form.style.display === 'block');
        visionneuseContainer.style.display = anyFormVisible ? 'none' : 'block';
    }

    // Comportement des boutons dans le dashboard principal
    document.querySelectorAll(".dashboard button").forEach(button => {
        button.addEventListener("click", function () {
            // Désactive les autres boutons et active celui-ci visuellement
            document.querySelectorAll(".dashboard button").forEach(btn => btn.classList.remove("active-button"));
            this.classList.add("active-button");
            // Mise à jour de la visionneuse après 300ms
            setTimeout(checkAndShowVisionneuse, 300);
        });
    });

    // Masque automatiquement les erreurs après 10 secondes
    setTimeout(() => {
        errorContainers.forEach(errorContainer => errorContainer.style.display = 'none');
    }, 10000);

    // Masque automatiquement les messages de succès après 10 secondes
    setTimeout(() => {
        successContainers.forEach(successContainer => successContainer.style.display = 'none');
    }, 10000);
});

//--------------------------------------------------------------------------------------
//---------------------------AFFICHAGE BOUTONS -----------------------------------------
//--------------------------------------------------------------------------------------

// ----------------- Gestion association (oui / non / projet) -----------------

document.addEventListener("DOMContentLoaded", function () {
    const radios = document.querySelectorAll('input[name="choixAsso"]');
    const inputAsso = document.getElementById("association");

    if (!inputAsso || radios.length === 0) return;

    function toggleAssociationField() {
        const selected = document.querySelector('input[name="choixAsso"]:checked')?.value;

        if (selected === "oui") {
            inputAsso.disabled = false;
            inputAsso.required = true;
            inputAsso.classList.remove("input-disabled");
        } else {
            inputAsso.value = "";
            inputAsso.disabled = true;
            inputAsso.required = false;
            inputAsso.classList.add("input-disabled");
        }
    }

    radios.forEach(radio => {
        radio.addEventListener("change", toggleAssociationField);
    });

    // état initial
    toggleAssociationField();
});


// -----------------------------  HORS DEPARTEMENT ------------------------------------

document.addEventListener("DOMContentLoaded", function () {
  const communeInput = document.getElementById("commune");
  const horsDepartementCheckbox = document.getElementById("horsDepartement");

  if (!communeInput || !horsDepartementCheckbox) return;

  // Lorsqu’on coche "Hors département"  désactive le champ commune
  horsDepartementCheckbox.addEventListener("change", function () {
    if (this.checked) {
      communeInput.disabled = true;
      communeInput.value = ""; // vide le champ si on coche la case
    } else {
      communeInput.disabled = false;
    }
  });

  // Lorsqu’on commence à écrire dans commune ➜ décoche la case
  communeInput.addEventListener("input", function () {
    if (this.value.trim() !== "") {
      horsDepartementCheckbox.checked = false;
    }
  });
});

    document.addEventListener("DOMContentLoaded", function () {
        const checkbox = document.getElementById("horsDepartement");
        const departementField = document.getElementById("departementField");

        checkbox.addEventListener("change", function () {
            if (this.checked) {
                departementField.style.display = "block";
            } else {
                departementField.style.display = "none";
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
        const numeroDepartement = document.getElementById("numeroDepartement");

        if (!numeroDepartement) return; // Empêche l’erreur

        numeroDepartement.addEventListener("input", function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 2) this.value = this.value.slice(0,2);
        });
    });

// ----------------- Questionnaire Réseau : "Autres" (dans menu déroulant) ---------------------

document.addEventListener("DOMContentLoaded", function() {
    function setupThemeToggle(selectId, autreContainerId, autreInputId) {
        const select = document.getElementById(selectId);
        const autreContainer = document.getElementById(autreContainerId);
        const autreInput = document.getElementById(autreInputId);

        // Vérifie que tous les éléments sont bien présents
        if (!select || !autreContainer || !autreInput) {
            console.warn("Un des éléments nécessaires n'a pas été trouvé pour l'ID:", selectId);
            return;
        }

        // Écouteur sur le changement de valeur du menu déroulant
        select.addEventListener("change", function() {
            if (select.value === "Autre") {
                autreContainer.style.display = "block";
            } else {
                autreContainer.style.display = "none";
                autreInput.value = "";
            }
        });
    }

    /**
     * Fonction pour afficher un champ texte lorsqu'une case "Autre" est cochée
     * @param {string} checkboxValue - valeur de la case à cocher ciblée
     * @param {string} autreChampId - ID du champ à afficher/masquer
     */
    function setupCheckboxToggle(checkboxValue, autreChampId) {
        const checkbox = document.querySelector(`input[name='theme[]'][value='${checkboxValue}']`);
        const autreChamp = document.getElementById(autreChampId);

        if (!checkbox || !autreChamp) {
            console.warn("Un des éléments nécessaires n'a pas été trouvé pour la valeur:", checkboxValue);
            return;
        }

        checkbox.addEventListener("change", function() {
            if (checkbox.checked) {
                autreChamp.style.display = "block";
                checkbox.value = "";
            } else {
                autreChamp.style.display = "none";
                autreChamp.value = "";
            }
        });
    }

    // Appliquer la gestion des sélections pour tous les champs concernés
    setupThemeToggle("themeG", "autreThemeContainer", "autreTheme"); // Q&R
    setupThemeToggle("themeG1", "autreThemeContainer1", "autreTheme1"); // RDV
    setupThemeToggle("themeG2", "autreThemeContainer2", "autreTheme2"); // Evenement
    setupThemeToggle("themeG3", "autreThemeContainer3", "autreTheme3"); // Longsuivi
    setupThemeToggle("themeG6", "autreThemeContainer6", "autreTheme6"); // Réseau
    
    setupCheckboxToggle("Autre", "autreChamp"); //Q&R
    setupCheckboxToggle("Autre1", "autreChamp1"); // RDV
    setupCheckboxToggle("Autre2", "autreChamp2"); // Evenement
    setupCheckboxToggle("Autre3", "autreChamp3"); // Longsuivi
    setupCheckboxToggle("Autre6", "autreChamp6"); // Réseau
});

// ----------------- Vérifie si ressource cochée  ---------------------


document.addEventListener("DOMContentLoaded", function() {
    function validateRessourcesSelection(formId, errorId) {
        const form = document.getElementById(formId);
        const errorMessage = document.getElementById(errorId);

        if (!form || !errorMessage) {
            console.warn("Un des éléments nécessaires n'a pas été trouvé pour l'ID:", formId);
            return;
        }

        form.addEventListener("submit", function(event) {
            var ressourcesChecked = document.querySelectorAll('input[name="ressources[]"]:checked');
            if (ressourcesChecked.length === 0) {
                event.preventDefault(); // Empêche la soumission du formulaire
                errorMessage.style.display = 'block'; // Affiche le message d'erreur
            } else {
                errorMessage.style.display = 'none'; // Cache le message d'erreur si une case est cochée
            }
        });
    }

    // Appliquer la validation aux formulaires
    validateRessourcesSelection("ressourcesForm", "ressourcesError");
});

//--------------Autocompletion de commune
$(document).on('click', suggestionsBoxSelector + ' li', function(){
    const nom = $(this).data('nom');
    const rna = $(this).data('rna');
    const commune = $(this).data('commune');
    const objet = $(this).data('objet');

    $('#association').val(nom);
    $('#rna_id').val(rna);

    // Auto-remplissage
    if ($('#commune').length) {
        $('#commune').val(commune);
    }

    if ($('#objet').length) {
        $('#objet').val(objet);
    }

    $(suggestionsBoxSelector).fadeOut();
});


//--------------------------------------------------------------------------------------
//------------------------------- VISIONNEUSE PAGE QUESTIONNAIRE -----------------------
//--------------------------------------------------------------------------------------
$(document).ready(function() {
    // Initialisation des variables liées à la pagination
    let currentPage = 1;                        // Page actuelle affichée
    let totalPages = 1;                         // Nombre total de pages disponibles
    let nbLignes = $('#nbLignes').val();        // Nombre d'entrées par page (depuis le champ select)

    // Fonction principale pour charger les entrées dans la visionneuse
    function loadEntries(page = 1) {
        $.ajax({
            url: "/../controllers/visionneuse/get_last_entries.php",   // Endpoint PHP
            method: "GET",
            dataType: "json",
            data: { nbLignes, page },       // Paramètres transmis à l'API
            success: function(response) {
                console.log(" Réponse brute AJAX :", response);

                // Vérification : la réponse doit contenir un tableau 'entries'
                if (!response || !Array.isArray(response.entries)) {
                    console.warn(" Réponse invalide :", response);
                    $('#visionneuse').html("<p>Erreur inattendue du serveur.</p>");
                    return;
                }

                // Référence vers la zone d'affichage avec l'id de la visionneuse
                const visionneuse = $('#visionneuse');
                visionneuse.empty();

                // Mise à jour des infos de pagination
                totalPages = response.totalPages;
                $('#pageInfo').text(`Page ${page} / ${totalPages}`);
                $('#prevPage').prop('disabled', page <= 1);
                $('#nextPage').prop('disabled', page >= totalPages);

                // Vérifie s'il y a des entrées à afficher
                if (response.entries.length > 0) {
                    const table = `
                        <table border="1" cellspacing="0" cellpadding="5">
                            <thead>
                                <tr>
                                    <th>Horodateur</th>
                                    <th>Accompagnement</th>
                                    <th>Nom contact</th>              
                                    <th>Thématique</th>
                                    <th>Référent guid'asso</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>`;
                    visionneuse.append(table);

                    // Remplissage du tableau avec les données reçues
                    const tbody = visionneuse.find('tbody');
                    response.entries.forEach(entry => {
                        tbody.append(`
                            <tr>
                                <td>${entry.HORODATEUR || ''}</td>
                                <td>${entry.NOMASSO || ''} ${entry.NOMSTRUCTURE || ''} ${entry.NOMEVENEMENT || ''}</td>
                                <td>${entry.CIVILITE || ''} ${entry.NOMCONTACT || ''}</td>
                                <td>${entry.THEMEGENERAL || ''} ${entry.AUTRETHEMATIQUE || ''}</td>
                                <td>${entry.NOMUTILISATEUR || ''} ${entry.PRENOMUTILISATEUR || ''}</td>
                                <td>${entry.TYPEQUESTIONNAIRE || ''}</td>
                            </tr>`);
                    });
                } else {
                    // Si aucun résultat trouvé
                    visionneuse.html("<p>Aucune entrée trouvée.</p>");
                }
            },
            error: function(xhr) {
                console.error(" AJAX ERROR", xhr.status, xhr.statusText);
                console.warn(" Réponse brute du serveur :", xhr.responseText);
                $('#visionneuse').html("<p>Erreur lors du chargement des données.</p>");
            }
        });
    }

    // Chargement initial des entrées à l’ouverture de la page
    loadEntries();

     // Événement : Changement du nombre de lignes à afficher
    $('#nbLignes').on('change', function() {
        nbLignes = $(this).val();      // Récupère la nouvelle valeur sélectionnée
        currentPage = 1;               // Reviens à la première page
        loadEntries(currentPage);      // Recharge les données
    });

    // Événement : Navigation vers la page suivante
    $('#nextPage').on('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadEntries(currentPage);
        }
    });

    // Événement : Navigation vers la page précédente
    $('#prevPage').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadEntries(currentPage);
        }
    });
});


//--------------------------------------------------------------------------------------
//------------------ SAUVEGARDE AUTOMATIQUE MODE BROUILLON -----------------------------
//--------------------------------------------------------------------------------------

// Attend que le DOM soit complètement chargé
document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form"); // Récupère le premier <form> de la page
    if (!form) return; // Si aucun formulaire n’est trouvé, on quitte

    const storageKey = `form_autosave_${form.id}`; // Clé unique pour stocker les données dans localStorage

    // ----------------------------------------
    // Tentative de restauration des données sauvegardées
    // ----------------------------------------
    const savedRaw = localStorage.getItem(storageKey); // Récupère les données brutes
    if (savedRaw) {
        const savedObj = JSON.parse(savedRaw); // Convertit le JSON en objet JS
        const now = Date.now(); // Horodatage actuel
        const age = now - (savedObj.timestamp || 0); // Calcule l’âge des données

        const delay = 60 * 1000; // Durée maximale de validité : 60 secondes

        if (age > delay) {
            // Si les données ont plus d'une minute, on les supprime
            localStorage.removeItem(storageKey);
        } else {
            // Sinon, on restaure les valeurs dans les champs du formulaire
            const saved = savedObj.data || {};
            for (const [name, value] of Object.entries(saved)) {
                const fields = form.querySelectorAll(`[name="${name}"]`);
                if (!fields.length) continue;

                fields.forEach(field => {
                    if (field.type === "checkbox") {
                        // Pour les cases à cocher : coche si la valeur est dans le tableau
                        if (Array.isArray(value) && value.includes(field.value)) {
                            field.checked = true;
                        }
                    } else if (field.type === "radio") {
                        // Pour les boutons radio : coche si la valeur correspond
                        if (field.value === value) {
                            field.checked = true;
                        }
                    } else {
                        // Pour les autres champs : on affecte directement la valeur
                        field.value = value;
                    }
                });
            }
        }
    }

    // Sauvegarde automatique à chaque saisie
    form.addEventListener("input", () => {
        const data = {};
        Array.from(form.elements).forEach(field => {
            if (!field.name || field.disabled) return; // Ignore les champs sans nom ou désactivés

            if (field.type === "checkbox") {
                // Sauvegarde les cases cochées sous forme de tableau
                if (!data[field.name]) data[field.name] = [];
                if (field.checked) data[field.name].push(field.value);
            } else if (field.type === "radio") {
                // Sauvegarde la valeur du bouton radio sélectionné
                if (field.checked) data[field.name] = field.value;
            } else {
                // Sauvegarde la valeur des autres champs
                data[field.name] = field.value;
            }
        });

        // Stocke le tout dans localStorage avec timestamp
        localStorage.setItem(storageKey, JSON.stringify({
            timestamp: Date.now(),
            data
        }));
    });

    //  Nettoyage des données après envoi réussi
    const hasSuccess = document.querySelector(".success-container"); // Détection d'une confirmation sur la page
    if (hasSuccess) {
        localStorage.removeItem(storageKey); // Supprime les données sauvegardées
    }
});

//--------------------------------------------------------------------------------------
//------------------------------- FICHE ASSOCIATION ------------------------------------
//--------------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function () {
  const ficheAssocBtn = document.getElementById("ficheAssocBtn");

  if (ficheAssocBtn) {
    ficheAssocBtn.addEventListener("click", function () {
      const url = "https://guide-asso-m2.geniephy.net/views/fiche_asso.php";
      const nomFenetre = "FicheAssociation";
      const options = "width=1000,height=700,resizable=yes,scrollbars=yes";

      window.open(url, nomFenetre, options);
    });
  } else {
    console.error(" Bouton Fiche association introuvable !");
  }
});


// ------------------------------------------------------------------------------
// -------------------- NOUVELLE FENETTRE MENU RECHERCHE ASSO ------------------
// ------------------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {
  // Sélectionne tous les boutons radio du groupe "recherche"
  const rechercheRadios = document.querySelectorAll("input[name='recherche']");

  rechercheRadios.forEach(radio => {
    radio.addEventListener("change", function () {
      if (this.checked && this.value === "Oui") {
        window.open(
          "https://guide-asso-m2.geniephy.net/views/questionnaire.php?formulaire=recherche", //  ton URL vers le formulaire
          "RecherchePopup",                   // nom de la fenêtre (identifiant)
          "width=900,height=700,resizable=yes,scrollbars=yes"
        );
      }
    });
  });
});


//--------------------------------------------------------------------------------------
//------------------- CODE POUR QUE CA RESTE BLANC QUAND INSERT ------------------------
//--------------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function() {
    let inputs = document.querySelectorAll("input, textarea, select");

    inputs.forEach(input => {
        input.addEventListener("input", function() {
            if (this.value.trim() !== "") {
                this.style.backgroundColor = "#e6e6e6"; // Fond gris clair
            } else {
                this.style.backgroundColor = ""; // Retour au style normal
            }
        });
    });
});


//--------------------------------------------------------------------------------------
//------------------------------- Gestion des champs autres ----------------------------
//--------------------------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {

  document.querySelectorAll("select[data-target]").forEach(select => {

    const targetId = select.dataset.target;
    const targetDiv = document.getElementById(targetId);
    const input = targetDiv?.querySelector("input");

    if (!targetDiv || !input) return;

    function toggle() {
      if (select.value === "Autre") {
        targetDiv.style.display = "block";
        input.required = true;
      } else {
        targetDiv.style.display = "none";
        input.value = "";
        input.required = false;
      }
    }

    select.addEventListener("change", toggle);
    toggle(); // état initial
  });

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



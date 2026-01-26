

document.addEventListener('DOMContentLoaded', function () {
    var radios = document.querySelectorAll('.derouler.case-speciale');

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            var questionnaireId = this.getAttribute('data-questionnaire');
            var questionnaire = document.getElementById(questionnaireId);

            // Masquer tous les questionnaires
            radios.forEach(function (otherRadio) {
                var otherQuestionnaireId = otherRadio.getAttribute('data-questionnaire');
                var otherQuestionnaire = document.getElementById(otherQuestionnaireId);

                if (otherRadio !== radio) {
                    otherQuestionnaire.style.display = 'none';
                    clearQuestionnaireContent(otherQuestionnaire);
                }
            });

            // Afficher ou masquer le questionnaire actuel
            if (this.checked) {
                questionnaire.style.display = 'block';
            } else {
                questionnaire.style.display = 'none';
                clearQuestionnaireContent(questionnaire);
            }

            // Afficher ou masquer le champ "Autre" selon l'état du bouton
            toggleAutreInput(questionnaireId);
        });
    });

    // Masquer initialement les questionnaires non sélectionnés
    radios.forEach(function (radio) {
        var questionnaireId = radio.getAttribute('data-questionnaire');
        var questionnaire = document.getElementById(questionnaireId);
        questionnaire.style.display = 'none';
    });

    function clearQuestionnaireContent(questionnaire) {
        // Effacer le contenu du questionnaire
        var inputElements = questionnaire.querySelectorAll('input[type="text"]');
        var selectElement = questionnaire.querySelector('select');

        inputElements.forEach(function (input) {
            input.value = '';
        });

        if (selectElement) {
            selectElement.selectedIndex = 0;
        }
    }
});


document.addEventListener('DOMContentLoaded', function () {
    var radios = document.querySelectorAll('.derouler.case-speciale');

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            var questionnaireId = this.getAttribute('data-questionnaire');
            var questionnaire = document.getElementById(questionnaireId);

            // Masquer tous les questionnaires
            radios.forEach(function (otherRadio) {
                var otherQuestionnaireId = otherRadio.getAttribute('data-questionnaire');
                var otherQuestionnaire = document.getElementById(otherQuestionnaireId);

                if (otherRadio !== radio) {
                    otherQuestionnaire.style.display = 'none';
                    clearQuestionnaireContent(otherQuestionnaire);
                }
            });

            // Afficher ou masquer le questionnaire actuel
            if (this.checked) {
                questionnaire.style.display = 'block';
            } else {
                questionnaire.style.display = 'none';
                clearQuestionnaireContent(questionnaire);
            }

            // Afficher ou masquer le champ "Autre" selon l'état du bouton
            toggleAutreInput(questionnaireId);
        });
    });

    // Masquer initialement les questionnaires non sélectionnés
    radios.forEach(function (radio) {
        var questionnaireId = radio.getAttribute('data-questionnaire');
        var questionnaire = document.getElementById(questionnaireId);
        questionnaire.style.display = 'none';
    });

    function clearQuestionnaireContent(questionnaire) {
        // Effacer le contenu du questionnaire
        var inputElements = questionnaire.querySelectorAll('input[type="text"]');
        var selectElement = questionnaire.querySelector('select');

        inputElements.forEach(function (input) {
            input.value = '';
        });

        if (selectElement) {
            selectElement.selectedIndex = 0;
        }
    }

    
});

// Fonction pour incrémenter le compteur
function incrementer() {
    var compteur = document.getElementById('compteur');
    var valeur = parseInt(compteur.value);
    compteur.value = valeur + 1;
}

// Fonction pour décrémenter le compteur
function decrementer() {
    var compteur = document.getElementById('compteur');
    var valeur = parseInt(compteur.value);
    if (valeur > 0) {
        compteur.value = valeur - 1;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const buttonQnR = document.querySelector('[data-questionnaire="Q&R"]');
    const buttonRDV = document.querySelector('[data-questionnaire="RDV"]');
    const buttonEvenement = document.querySelector('[data-questionnaire="evenement"]');
    const buttonRecherche = document.querySelector('[data-questionnaire="recherche"]');
    const buttonLongSuivi = document.querySelector('[data-questionnaire="longsuivi"]');

    const questionnaireQnR = document.getElementById('Q&R');
    const questionnaireRDV = document.getElementById('RDV');
    const questionnaireEvenement = document.getElementById('evenement');
    const questionnaireRecherche = document.getElementById('recherche');
    const questionnaireLongSuivi = document.getElementById('longsuivi');

    // Cache tous les questionnaires au chargement de la page
    questionnaireQnR.style.display = "none";
    questionnaireRDV.style.display = "none";
    questionnaireEvenement.style.display = "none";
    questionnaireRecherche.style.display = "none";
    questionnaireLongSuivi.style.display = "none";

    buttonQnR.addEventListener('click', () => toggleQuestionnaire(questionnaireQnR));
    buttonRDV.addEventListener('click', () => toggleQuestionnaire(questionnaireRDV));
    buttonEvenement.addEventListener('click', () => toggleQuestionnaire(questionnaireEvenement));
    buttonRecherche.addEventListener('click', () => toggleQuestionnaire(questionnaireRecherche));
    buttonLongSuivi.addEventListener('click', () => toggleQuestionnaire(questionnaireLongSuivi));

    function toggleQuestionnaire(questionnaire) {
        // Cache tous les questionnaires sauf celui associé au bouton cliqué
        [questionnaireQnR, questionnaireRDV, questionnaireEvenement, questionnaireRecherche, questionnaireLongSuivi]
            .filter(q => q !== questionnaire)
            .forEach(q => q.style.display = "none");

        // Affiche ou masque le questionnaire associé uniquement au bouton cliqué
        if (questionnaire.style.display === "none") {
            questionnaire.style.display = "block";
        } else {
            questionnaire.style.display = "none";
        }
    }
});

function showAddUserForm() {
    // Vérifie si le formulaire est déjà affiché
    var addUserFormContainer = document.getElementById("add-user-form-container");
    if (addUserFormContainer.style.display === "block") {
        // Si le formulaire est déjà affiché, le masquer
        addUserFormContainer.style.display = "none";
    } else {
        // Sinon, afficher le formulaire
        addUserFormContainer.style.display = "block";
    }

    // Masque le tableau de bord
    document.getElementById("dashboard").style.display = "block";
    document.getElementById("import-export-form-container").style.display = "none";
    document.getElementById("ModifUser").style.display = "none";

}

function showModifUser() {
    // Vérifie si le formulaire est déjà affiché
    var addUserFormContainer = document.getElementById("ModifUser");
    if (addUserFormContainer.style.display === "block") {
        // Si le formulaire est déjà affiché, le masquer
        addUserFormContainer.style.display = "none";
    } else {
        // Sinon, afficher le formulaire
        addUserFormContainer.style.display = "block";
    }

    // Masque le tableau de bord
    document.getElementById("dashboard").style.display = "block";
    document.getElementById("import-export-form-container").style.display = "none";
    document.getElementById("add-user-form-container").style.display = "none";

}

function showImportExportForm() {
    document.getElementById("add-user-form-container").style.display = "none";
    var importExportFormContainer = document.getElementById("import-export-form-container");
    if (importExportFormContainer.style.display === "block") {
        importExportFormContainer.style.display = "none";
    } else {
        importExportFormContainer.style.display = "block";
    }
}


function generateMap() {
    // Ajoute la logique pour le bouton "Générer Cartographie"
    document.getElementById("add-user-form-container").style.display = "none";
    document.getElementById("import-export-form-container").style.display = "none";

}

// couleur boutons formulaire actif
  const buttons = document.querySelectorAll('.dashboard-button');

  buttons.forEach(button => {
    button.addEventListener('click', function() {
      // Réinitialiser la couleur de fond de tous les boutons
      buttons.forEach(btn => {
        btn.style.backgroundColor = ''; // Réinitialiser la couleur de fond
      });
      // Définir la couleur de fond du bouton cliqué en vert foncé
      this.style.backgroundColor = 'darkgreen';
    });
  });

 // Code champ temps consacré

// Sélectionner tous les champs "heures" et "minutes" dans tous les formulaires
var heuresInputs = document.querySelectorAll('input[name="heures"]');
var minutesInputs = document.querySelectorAll('input[name="minutes"]');

// Ajouter un écouteur d'événement à chaque champ "heures"
heuresInputs.forEach(function(input) {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, ''); // Remplacer tout ce qui n'est pas un chiffre par une chaîne vide
    });
});

// Ajouter un écouteur d'événement à chaque champ "minutes"
minutesInputs.forEach(function(input) {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, ''); // Remplacer tout ce qui n'est pas un chiffre par une chaîne vide
        if (parseInt(this.value) > 59) { // Limiter les minutes à 59
            this.value = '59';
        }
    });
});

// Ajouter un écouteur d'événement à chaque champ "heures" et "minutes" pour gérer le flou (blur)
var tousInputs = document.querySelectorAll('input[name="heures"], input[name="minutes"]');
tousInputs.forEach(function(input) {
    input.addEventListener('blur', function() {
        if (this.value === '') {
            this.value = '0';
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    console.log(" Script Admin chargé et exécuté.");

    // Sélection des éléments
    const forms = document.querySelectorAll('.admin-section');
    const buttons = document.querySelectorAll('.dashboard-button-green, .dashboard-button-purple, .dashboard-button-pink, .dashboard-button-user, .dashboard-button-blue');
    const errorContainers = document.querySelectorAll('.error-container');
    const successContainer = document.querySelector(".success-container");
    const visionneuseContainer = document.getElementById('visionneuse-containerEspace_admin_monEspace'); // Ajout explicite
    const piecesJointesContainer = document.getElementById('pieces-jointes-container');
    const blocsNotesContainer = document.getElementById('blocs-notes-container');
    const exportContainer = document.getElementById('import-export-form-container');
    //const modifUserFormContainer = document.getElementById("modif-user-form-container");
    const emailForm = document.getElementById("email-form");
    const nameForm = document.getElementById("nom-form");
    const searchButtonsContainer = document.getElementById("search-buttons-container");
    const searchByEmailBtn = document.getElementById("search-by-email");
    const searchByNameBtn = document.getElementById("search-by-name");


    // Fonction pour masquer toutes les sections, y compris la visionneuse
    function hideAllForms() {
        forms.forEach(form => form.style.display = 'none');
        if (visionneuseContainer) visionneuseContainer.style.display = 'none';
        if (piecesJointesContainer) piecesJointesContainer.style.display = 'none';
        if (blocsNotesContainer) blocsNotesContainer.style.display = 'none';
        if (exportContainer) exportContainer.style.display = 'none';
    }

    // Affiche un formulaire spécifique
    function showForm(formId) {
        hideAllForms();
        const form = document.getElementById(formId);
        if (form) {
            form.style.display = "block";

            // Cas spécial pour formulaire utilisateur
            if (formId === "modif-user-form-container" && searchButtonsContainer) {
                searchButtonsContainer.style.display = "flex";
                if (emailForm) emailForm.style.display = "none";
                if (nameForm) nameForm.style.display = "none";
            }

            // Scroll vers le formulaire affiché
            setTimeout(() => {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        }
    }

    // Gestion des boutons du dashboard
    buttons.forEach(button => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            if (targetId) {
                showForm(targetId);
            }
            if (successContainer) successContainer.style.display = "none";
        });
    });

    // Gestion des boutons internes de modification utilisateur
    // Modification par email
    if (searchByEmailBtn) {
        searchByEmailBtn.addEventListener("click", () => {
            emailForm.style.display = "block";
            nameForm.style.display = "none";
            searchButtonsContainer.style.display = "none";
        });
    }
    // Modification par nom
    if (searchByNameBtn) {
        searchByNameBtn.addEventListener("click", () => {
            nameForm.style.display = "block";
            emailForm.style.display = "none";
            searchButtonsContainer.style.display = "none";
        });
    }

    // Vérifie s’il y a une erreur visible et affiche uniquement le bon formulaire
    function showRelevantForm() {
        let hasError = false;
        let parentFormId = null;

        errorContainers.forEach(errorContainer => {
            const message = errorContainer.innerText.trim();
            if (message !== "") {
                console.log(" Erreur détectée :", message);
                hasError = true;
                const parent = errorContainer.closest('.admin-section');
                if (parent && parent.id) {
                    parentFormId = parent.id;
                }
            }
        });

        if (hasError && parentFormId) {
            hideAllForms(); // cache tout, y compris visionneuse
            const targetForm = document.getElementById(parentFormId);
            if (targetForm) {
                targetForm.style.display = "block";
                setTimeout(() => {
                    targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        }
        return hasError;
    }

    // Affiche la visionneuse uniquement s'il n'y a PAS d'erreur
    const hasError = showRelevantForm();

    if (!hasError) {
        hideAllForms(); // Masque tous les blocs
        if (visionneuseContainer) {
            visionneuseContainer.style.display = "none";

            // Appelle loadEntries uniquement si la fonction existe
            if (typeof loadEntries === "function") {
                loadEntries();
            } else {
                console.warn(" La fonction loadEntries n’est pas encore disponible.");
            }

            setTimeout(() => {
                visionneuseContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        } 
    }

    // Pièces jointes
    document.querySelector(".dashboard-button-pink")?.addEventListener("click", function () {
        hideAllForms();
        piecesJointesContainer.style.display = 'block';
        setTimeout(() => {
            piecesJointesContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    });

    // Blocs-notes
    document.querySelector(".dashboard-button-blue")?.addEventListener("click", function () {
        hideAllForms();
        blocsNotesContainer.style.display = 'block';
        setTimeout(() => {
            blocsNotesContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    });

    //--------------------------------- MODIFICATION PERSONNE ----------------------------
        // ----- AUTOCOMPLÉTION & CHARGEMENT DES DÉTAILS ------------
        function fetchUserDetails(email) {
            $.ajax({
                url: '/../controllers/admin/get-details-email.php',
                method: 'POST',
                data: { email1: email },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data) {
                        $('#nom1').val(data.NOMPERSONNE || '');
                        $('#prenom1').val(data.PRENOMPERSONNE || '');
                        $('#structure1').val(data.STRUCTURE || '');
                        $('#email1').val(email).prop('readonly', true);
                        if (data.CLASSIFICATION) {
                            $('input[name="classification"][value="' + data.CLASSIFICATION + '"]').prop('checked', true);
                        }
                        $('#details-container').fadeIn();
                    } else {
                        alert('Aucune information trouvée pour cet e-mail.');
                        $('#details-container').hide();
                    }
                }
            });
        }

        // -------------------- POUR L'EMAIL ---------------------
        $('#email1').on('input', function () {
            var query = $(this).val();
            if (query !== '') {
                $.ajax({
                    url: '/../controllers/admin/autocomplete.php',
                    method: 'POST',
                    data: { query: query },
                    success: function (data) {
                        $('#emailSuggestions').fadeIn().html(data);
                    }
                });
            } else {
                $('#emailSuggestions').fadeOut();
            }
        });

        $(document).on('click', '#emailSuggestions li', function () {
                var selectedEmail = $(this).text();
                $('#email1').val(selectedEmail).prop('readonly', true);
                $('#emailSuggestions').fadeOut();
                fetchUserDetails(selectedEmail);
            });

            // Double clic pour modifier l'email
            $('#email1').on('dblclick', function () {
                $(this).val("").prop('readonly', false);
                $('#details-container').fadeOut();
            });

            // ------------------- POUR LE NOM -------------------
            function fetchUserDetailsByName(nom) {
                $.ajax({
                    url: '/../controllers/admin/get-details-nom.php',
                    method: 'POST',
                    data: { nom2: nom },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data && !data.error) {
                            $('#email2').val(data.MAIL || '').prop('readonly', true);
                            $('#prenom2').val(data.PRENOMPERSONNE || '');
                            $('#structure2').val(data.STRUCTURE || '');
                            if (data.CLASSIFICATION) {
                                $('input[name="classification"][value="' + data.CLASSIFICATION + '"]').prop('checked', true);
                            }
                            $('#details-container2').fadeIn();
                        } else {
                            alert(data?.error || "Aucune donnée trouvée pour ce nom.");
                            $('#details-container2').hide();
                        }
                    },
                    error: function () {
                        alert('Erreur lors de la récupération des données.');
                    }
                });
            }

            $('#nom2').on('input', function () {
                var query = $(this).val();
                if (query !== '') {
                    $.ajax({
                        url: '/../controllers/admin/autocomplete_nom.php',
                        method: 'POST',
                        data: { query: query },
                        success: function (data) {
                            $('#nomSuggestions').fadeIn().html(data);
                        }
                    });
                } else {
                    $('#nomSuggestions').fadeOut();
                }
            });

            $(document).on('click', '#nomSuggestions li', function () {
                const selectedNom = ($(this).data('nom') || $(this).text() || '').trim();
                if (!selectedNom) {
                    return;
                }
                $('#nom2').val(selectedNom);             // insère uniquement le nom
                $('#nomSuggestions').fadeOut();
                fetchUserDetailsByName(selectedNom);     // charge les infos liées à ce nom
            });
    });

//--------------------------------------------------------------------------------------
//------------------------------- INFO UTILISATEUR -------------------------------------
//--------------------------------------------------------------------------------------

if (userData.error) {
    console.error(" Erreur :", userData.error);
} else {
    console.log(" Infos utilisateur :", userData);

    // Stocker les infos utilisateur dans sessionStorage
    sessionStorage.setItem("NOMPERSONNE", userData.NOMPERSONNE);
    sessionStorage.setItem("PRENOMPERSONNE", userData.PRENOMPERSONNE);
    sessionStorage.setItem("IDFONCTION", userData.IDFONCTION);
    sessionStorage.setItem("CLASSIFICATION", userData.CLASSIFICATION);
}

//--------------------------------------------------------------------------------------
//------------------------------- GESTION MDP  ------------------------------------
//--------------------------------------------------------------------------------------

function togglePasswordVisibility(inputId) {
    const passwordField = document.getElementById(inputId);
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}


document.querySelectorAll('[data-target]').forEach(button => {
  button.addEventListener("click", function () {
    const target = this.getAttribute("data-target");

    // Appelle PHP pour stocker ce clic
    fetch("/../controllers/admin/set_admin_button.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "boutonClique=" + encodeURIComponent(target)
    });
  });
});


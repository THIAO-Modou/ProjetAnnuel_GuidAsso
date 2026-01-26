//----------------------------------------------------------------------------
//------------------ VISIONNEUSE FICHE ASSO - ASSOC + NOM --------------------
//----------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function () {
    console.log("Script chargé et exécuté.");

    // Variables pour la recherche par association
    const resultsContainer = document.getElementById("results-container");
    const resultsBody = document.getElementById("results-body-assoc");

    // Variables pour la recherche par nom
    const resultsContainer1 = document.getElementById("visionneuse-container-fiche");
    const resultsBodyNom = document.getElementById("results-body-nom");

    // Variables pour les formulaires
    const formAssociation = document.getElementById("association");
    const formNom = document.getElementById("nom");

    // Variables pour les boutons du tableau de bord
    const buttons = document.querySelectorAll(".dashboard-button-purple");

    // Pagination
    let currentPage = 1;
    let totalPages = 1;
    let nbLignes = 20; // Modifier ici si tu veux un nombre par défaut différent

    const prevButton = document.getElementById("prevPage");
    const nextButton = document.getElementById("nextPage");
    const pageInfo = document.getElementById("pageInfo");

    if (prevButton && nextButton) {
        prevButton.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                loadEntries();
            }
        });

        nextButton.addEventListener('click', function () {
            if (currentPage < totalPages) {
                currentPage++;
                loadEntries();
            }
        });
    }

        document.getElementById("nbLignes").addEventListener("change", function () {
        nbLignes = parseInt(this.value);
        currentPage = 1;
        loadEntries();
    });


    // Affichage des formulaires
    window.showForm = function(formType) {
        formAssociation.style.display = "none";
        formNom.style.display = "none";
        resultsContainer.style.display = "none";
        resultsContainer1.style.display = "none";
        document.getElementById("visionneuse-container-fiche").style.display = "none";

        buttons.forEach(button => button.classList.remove("active-button"));

        const targetForm = document.getElementById(formType);
        if (targetForm) {
            targetForm.style.display = "block";
            document.querySelector(`[onclick*="showForm('${formType}')"]`).classList.add("active-button");
            setTimeout(() => {
                targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        }
    };


    //--------------------------------------------------------------------------------------
    //------------------------------- INFO UTILISATEUR -------------------------------------
    //--------------------------------------------------------------------------------------

    if (userData.error) {
        console.error("❌ Erreur :", userData.error);
    } else {
        console.log("🔹 Infos utilisateur :", userData);

        // 🔹 Stocker les infos utilisateur dans sessionStorage
        sessionStorage.setItem("NOMPERSONNE", userData.NOMPERSONNE);
        sessionStorage.setItem("PRENOMPERSONNE", userData.PRENOMPERSONNE);
        sessionStorage.setItem("IDFONCTION", userData.IDFONCTION);
        sessionStorage.setItem("CLASSIFICATION", userData.CLASSIFICATION);
    }


    //--------------------------------------------------------------------------------------
    //------------------------------- VISIONNEUSE ------------------------------------------
    //--------------------------------------------------------------------------------------

    $(document).ready(function(){
    console.log("🔹 Script chargé et exécuté.");

    let currentPage = 1;
    let totalPages = 1;
    let nbLignes = $('#nbLignes').val();

    function loadEntries(page = 1) {
        console.log(`🔹 Envoi de la requête AJAX... (page: ${page}, nbLignes: ${nbLignes})`);

        $.ajax({
            url: "/../controllers/visionneuse/Users_Visionneuse.php",
            method: "GET",
            data: { nbLignes: nbLignes, page: page },
            dataType: "json",
            success: function(response) {
                console.log("🔹 Réponse serveur :", response);

                let Users_visionneuse = $('#Users_visionneuse');
                Users_visionneuse.empty();

                totalPages = response.totalPages || 1;
                $('#pageInfo').text(`Page ${page} / ${totalPages}`);

                $('#prevPage').prop('disabled', page <= 1);
                $('#nextPage').prop('disabled', page >= totalPages);

                if (response.entries.length > 0) {
                    let table = `<table border="1">
                        <thead>
                            <tr>
                                <th>Prénom Nom</th>
                                <th>Adresse mail</th>
                                <th>Classification Guid'Asso</th>
                                <th>Fonction</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>`;
                    
                    Users_visionneuse.append(table);
                    let tbody = Users_visionneuse.find('tbody');
                    response.entries.forEach(entry => {
                        let row = `<tr>
                            <td>${entry.NOMPERSONNE} ${entry.PRENOMPERSONNE}</td>
                            <td>${entry.MAIL}</td>
                            <td>${entry.CLASSIFICATION}</td>
                            <td>${entry.NAMEFONCTION}</td>
                            <td><button class="delete-user" data-mail="${entry.MAIL}">
                                <img src="/../public/img/delete-icon.png" alt="Supprimer"></button>
                            </td>
                        </tr>`;
                        tbody.append(row);
                    });

                    $(".delete-user").on("click", function () {
                        let email = $(this).data("mail");
                        console.log(`🔹 Suppression de l'utilisateur avec l'email : ${email}`);

                        if (confirm("Voulez-vous vraiment supprimer cet utilisateur ?")) {
                            deleteUser(email);
                        }
                    });
                } else {
                    $('#Users_visionneuse').html("<p>Aucune entrée trouvée.</p>");
                }
            },
            error: function(xhr) {
                console.error("❌ Erreur AJAX :", xhr.responseText);
                $('#Users_visionneuse').html("<p>Erreur lors du chargement des données.</p>");
            }
        });
    }

    loadEntries();

    $('#nbLignes').on('change', function() {
        nbLignes = $(this).val();
        currentPage = 1;
        loadEntries(currentPage);
    });

    $('#nextPage').on('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadEntries(currentPage);
        }
    });

    $('#prevPage').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadEntries(currentPage);
        }
    });

    function deleteUser(email) {
        // Vérifie si l'utilisateur connecté est un "Visiteur" (IDFONCTION = 3)
        if (sessionStorage.getItem("IDFONCTION") == 3) {
            alert("❌ Vous n'avez pas le droit de supprimer un utilisateur.");
            return;
        }
        $.ajax({
            url: "/../controllers/visionneuse/Users_Visionneuse.php",
            method: "POST",
            data: { mail: email },
            dataType: "json",
            success: function(response) {
                console.log("🔹 Réponse suppression :", response);

                if (response.success) {
                    alert("Utilisateur supprimé avec succès !");
                    loadEntries(); 
                } else {
                    alert(`❌ Erreur lors de la suppression : ${response.error}`);
                }
            },
            error: function(xhr) {
                console.error("❌ Erreur AJAX lors de la suppression :", xhr.responseText);
                alert("❌ Une erreur est survenue !");
            }
        });
    }
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
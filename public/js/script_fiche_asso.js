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

    // Affichage du formulaire
    window.showForm = function(formType) {
        formAssociation.style.display = "none";
        formNom.style.display = "none";
        resultsContainer.style.display = "none";
        resultsContainer1.style.display = "none";
        document.getElementById("visionneuse-container-fiche").style.display = "none";

        // Activation du bouton actif
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

    // Gestion des soumissions de formulaires
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            
            const assocName = document.getElementById("association_fiche")?.value;
            const contactName = document.getElementById("NcontactInput_fiche")?.value;
            
            if (assocName) {
                console.log(" Association saisie :", assocName);
                fetch("/../controllers/predict_PHP/recherche_assoc.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `assoc=${encodeURIComponent(assocName)}&nbLignes=20&page=1`
                })
                .then(response => response.json())
                .then(data => {
                    console.log("✅ Réponse serveur (contact) :", data);
                    resultsBody.innerHTML = data.entries.length > 0 ? data.entries.map(item => `
                        <tr>
                            <td>${item.HORODATEUR}</td>
                            <td>${item.THEMEGENERAL}</td>
                            <td>${item.AUTRETHEMATIQUE}</td>
                            <td>${item.MAILGUIDASSO}</td>
                            <td>${item.TRANSMISPAR}</td>
                            <td>${item.TRANSMISA}</td>
                        </tr>
                    `).join('') : '<tr><td colspan="6">Aucun résultat trouvé</td></tr>';
                    resultsContainer.style.display = "block";
                    document.getElementById("visionneuse-container-fiche").style.display = "block";
                    setTimeout(() => {
                        resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                })
                .catch(error => alert("Une erreur est survenue lors de la recherche."));
            }

            if (contactName) {
                fetch("/../controllers/predict_PHP/recherche_contact.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `nom_contact=${encodeURIComponent(contactName)}&nbLignes=20&page=1`
                })
                .then(response => response.json())
                .then(data => {
                    resultsBodyNom.innerHTML = data.entries.length > 0 ? data.entries.map(item => `
                        <tr>
                            <td>${item.HORODATEUR}</td>
                            <td>${item.THEMEGENERAL}</td>
                            <td>${item.AUTRETHEMATIQUE}</td>
                            <td>${item.MAILGUIDASSO}</td>
                            <td>${item.TRANSMISPAR}</td>
                            <td>${item.TRANSMISA}</td>
                        </tr>
                    `).join('') : '<tr><td colspan="6">Aucun résultat trouvé</td></tr>';
                    resultsContainer1.style.display = "block";
                    document.getElementById("visionneuse-container-fiche").style.display = "block";
                    setTimeout(() => {
                        resultsContainer1.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                })
                .catch(error => alert("Une erreur est survenue lors de la recherche."));
            }
        });
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


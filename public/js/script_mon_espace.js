document.addEventListener("DOMContentLoaded", function () {
    console.log(" Script mon_espace lancé");

    // Références aux blocs
    const visionneuse = document.getElementById("visionneuse-containerEspace_admin_monEspace");
    const blocsNotes = document.getElementById("blocs-notes-container");
    const piecesJointes = document.getElementById("pieces-jointes-container");
    const exportContainer = document.getElementById("import-export-form-container");
    const forms = document.querySelectorAll(".formulaire-container-vert, .formulaire-container-bleu");

    // Références aux messages
    const errorContainers = document.querySelectorAll(".error-container");
    const successContainers = document.querySelectorAll(".success-container");

    // Références aux boutons
    const boutons = {
        blocsNotes: document.querySelector(".dashboard-button-blue-bn"),
        piecesJointes: document.querySelector(".dashboard-button-pink"),
        export: document.querySelector(".dashboard-button-green-export"),
        formulaires: document.querySelectorAll("[onclick*='showForm']")
    };

    // Fonction générique pour tout cacher
    function toutCacher() {
        [blocsNotes, piecesJointes, exportContainer].forEach(el => {
            if (el) el.style.display = "none";
        });
        forms.forEach(form => form.style.display = "none");
    }

    // Cacher tous les blocs
    toutCacher()

    // Gestion affichage basé sur messages
    function afficherMessageSiPresent() {
        let affiche = false;

        [...errorContainers, ...successContainers].forEach(container => {
            if (container.innerText.trim() !== "") {
                const cible = container.closest(
                    ".formulaire-container-vert, .formulaire-container-bleu, #pieces-jointes-container"
                );
                if (cible) {
                    visionneuse.style.display = "none"
                    toutCacher();
                    cible.style.display = "block";
                    affiche = true;
                }
            }
        });

        if (!affiche && visionneuse) {
            visionneuse.style.display = "block";
        }
    }

    // Affichage visionneuse par défaut si aucun message
    afficherMessageSiPresent();

    // Gestion des boutons formulaires personnels
    boutons.formulaires.forEach(btn => {
        btn.addEventListener("click", () => {
            const match = btn.getAttribute("onclick")?.match(/showForm\('(.+?)'\)/);
            if (!match || !match[1]) return;

            const cible = document.getElementById(match[1]);
            if (cible) {
                visionneuse.style.display = "none"
                toutCacher();
                cible.style.display = "block";
                cible.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        });
    });

    // Bouton : blocs notes
    if (boutons.blocsNotes && blocsNotes) {
        boutons.blocsNotes.addEventListener("click", () => {
            visionneuse.style.display = "none"
            toutCacher();
            blocsNotes.style.display = "block";
            blocsNotes.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    // Bouton : pièces jointes
    if (boutons.piecesJointes && piecesJointes) {
        boutons.piecesJointes.addEventListener("click", () => {
            visionneuse.style.display = "none"
            toutCacher();
            piecesJointes.style.display = "block";
            piecesJointes.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    // Bouton : gestion BDD
    if (boutons.export && exportContainer) {
        boutons.export.addEventListener("click", () => {
            visionneuse.style.display = "none"
            toutCacher();
            exportContainer.style.display = "block";
            exportContainer.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    // Masquer automatiquement les messages après 5 secondes
    setTimeout(() => {
        errorContainers.forEach(e => (e.style.display = "none"));
        successContainers.forEach(s => (s.style.display = "none"));
    }, 5000);
});


//--------------------------------------------------------------------------------------
//--------- ECOUTEUR ET DETECTEUR D'ACTIVITES DES BOUTONS -----------------------------
//--------------------------------------------------------------------------------------
document.querySelectorAll('.dashboard-button-green, .dashboard-button-blue, .dashboard-button-pink, .dashboard-button-blue-bn, .dashboard-button-green-export')
.forEach(button => {
    button.addEventListener("click", function () {
        const boutonId = this.className;

        // Appelle le serveur pour stocker le clic en session
        fetch("/../controllers/user/set_monEspace_button.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "boutonClique=" + encodeURIComponent(boutonId)
        });
    });
});

//-------------------------------------------------------------------
//------------------ AFFICHAGE MDP ----------------------------------
//-------------------------------------------------------------------

// fonction qui affiche ou non le mdp (traduit en texte le mdp)
  function togglePasswordVisibility(inputId) {
    const passwordField = document.getElementById(inputId);
      if (passwordField.type === "password") {
          passwordField.type = "text";
      } else {
          passwordField.type = "password";
      }
  }

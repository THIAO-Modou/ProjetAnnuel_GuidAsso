document.getElementById("menuPrincipal").addEventListener("change", function() {
    let choix = this.value;
    document.getElementById("menuDepartement").style.display = (choix === "departement") ? "inline-block" : "none";
    document.getElementById("menuEPCI").style.display = (choix === "epci") ? "inline-block" : "none";
    document.getElementById("menuGraphEPCI").style.display = "none";
});

document.getElementById("menuEPCI").addEventListener("change", function() {
    let epci = this.value;
    let menuGraph = document.getElementById("menuGraphEPCI");
    menuGraph.innerHTML = ""; 
    menuGraph.style.display = epci ? "inline-block" : "none";

    let options = {
        "grand-poitiers": [
            { value: "graph6", text: "Nb assos Grand-Poitiers - Thém. activités" },
            { value: "graph13", text: "Nb assos Grand-Poitiers - Thém. questions" }
        ],
        "haut-poitou": [
            { value: "graph7", text: "Nb assos Haut-Poitou - Thém. activités" },
            { value: "graph14", text: "Nb assos Haut-Poitou - Thém. questions" }
        ],
        "vallees-clain": [
            { value: "graph8", text: "Nb assos Vallées du Clain - Thém. activités" },
            { value: "graph15", text: "Nb assos Vallées du Clain - Thém. questions" }
        ],
        "grand-chatellerault": [
            { value: "graph9", text: "Nb assos Grand-Châtellerault - Thém. activités" },
            { value: "graph16", text: "Nb assos Grand-Châtellerault - Thém. questions" }
        ],
        "vienne-gartempe": [
            { value: "graph10", text: "Nb assos Vienne-et-Gartempe - Thém. activités" },
            { value: "graph17", text: "Nb assos Vienne-et-Gartempe - Thém. questions" }
        ],
        "civraisien-poitou": [
            { value: "graph11", text: "Nb assos Civraisien-en-Poitou - Thém. activités" },
            { value: "graph18", text: "Nb assos Civraisien-en-Poitou - Thém. questions" }
        ],
        "pays-loudunais": [
            { value: "graph12", text: "Nb assos Pays Loudunais - Thém. activités" },
            { value: "graph19", text: "Nb assos Pays Loudunais - Thém. questions" }
        ]
    };

    if (options[epci]) {
        options[epci].forEach(option => {
            let opt = document.createElement("option");
            opt.value = option.value;
            opt.textContent = option.text;
            menuGraph.appendChild(opt);
        });
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


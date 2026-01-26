//------------------------------- SCRIPT FOOTER----------------------------------------------/
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


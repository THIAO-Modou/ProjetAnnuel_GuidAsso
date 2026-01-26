let tempsInactivite = (25 * 60 * 1000); // 1 minute en millisecondes

let timer;
function resetTimer() {
    clearTimeout(timer);
    timer = setTimeout(() => {
        window.location.href = "/../views/pageconnexion.php"; // Redirection automatique
    }, tempsInactivite);
}

// Écouter l'activité de l'utilisateur
window.onload = resetTimer;
document.onmousemove = resetTimer;
document.onkeypress = resetTimer;


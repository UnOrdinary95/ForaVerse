/**
 * Copie l'URL actuelle de la page dans le presse-papiers
 * et affiche une alerte pour informer l'utilisateur
 */
function partagerURL() {
    // Récupère l'URL complète de la page courante
    const lien = window.location.href;

    // Tente de copier le lien dans le presse-papiers
    navigator.clipboard.writeText(lien).then(() => {
        // En cas de succès, affiche une confirmation
        alert('Lien copié dans le presse-papiers !');
    }).catch(err => {
        // En cas d'erreur, affiche un message d'erreur
        alert('Erreur lors de la copie du lien');
        // Log l'erreur dans la console pour le débogage
        console.error(err);
    });
}
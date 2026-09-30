// TODO (mission 5) : regrouper les requêtes vers PHP autour de jQuery.ajax().
// Prévoir réponses JSON, jeton CSRF et erreurs ; aucune requête n'est fournie.
// Charger ce fichier après jQuery et avant app.js quand tu l'utiliseras.
window.apiRenommerProjet = function (donnees) {
    return $.ajax({
        url: 'projet.php?id=' + encodeURIComponent(donnees.project_id),
        method: 'POST',
        dataType: 'json',
        data: {
            project_id: donnees.project_id,
            nom: donnees.nom,
            csrf_token: donnees.csrf_token
        }
    });
};
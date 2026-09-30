// Les échanges avec PHP passent par jQuery.ajax().
window.apiRenommerProjet = function (donnees) {
    return $.ajax({
        url: 'projet.php?id=' + encodeURIComponent(donnees.project_id),
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'renommer',
            project_id: donnees.project_id,
            nom: donnees.nom,
            csrf_token: donnees.csrf_token
        }
    });
};

window.apiEnvoyerImages = function (formData, projectId) {
    return $.ajax({
        url: 'projet.php?id=' + encodeURIComponent(projectId),
        method: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false
    });
};

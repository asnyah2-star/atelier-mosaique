// TODO (mission 3) : ajouter les interactions avec jQuery, chargé avant ce fichier.
// TODO (mission 5) : appeler les fonctions de api.js pour les échanges AJAX.
// Le moteur de mosaïque et Canvas peut conserver son JavaScript natif.
// La page de départ fonctionne sans JavaScript.
$(document).ready(function () {
    $('.formulaire button[type="button"]').on('click', function () {
        document.getElementById('nom').focus();
    });
     $('#form-renommer').on('submit', function (evenement) {
        evenement.preventDefault();

        var formulaire = this;
        var bouton = $('#bouton-renommer');
        var message = document.getElementById('message-renommage');

        var donnees = {
            project_id: formulaire.elements.project_id.value,
            nom: formulaire.elements.nom.value,
            csrf_token: formulaire.elements.csrf_token.value
        };

        bouton.prop('disabled', true);
        message.textContent = 'Renommage en cours…';

        window.apiRenommerProjet(donnees)
            .done(function (reponse) {
                message.textContent = reponse.message;
                formulaire.elements.nom.value = reponse.name;
                document.querySelector('h1').textContent = reponse.name;
                document.title = reponse.name;
            })
            .fail(function (xhr) {
                var reponse = xhr.responseJSON;
                message.textContent = reponse
                    ? reponse.message
                    : 'Le renommage a échoué. Réessaie.';
            })
            .always(function () {
                bouton.prop('disabled', false);
            });
    });   

});

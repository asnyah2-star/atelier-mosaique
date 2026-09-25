// TODO (mission 3) : ajouter les interactions avec jQuery, chargé avant ce fichier.
// TODO (mission 5) : appeler les fonctions de api.js pour les échanges AJAX.
// Le moteur de mosaïque et Canvas peut conserver son JavaScript natif.
// La page de départ fonctionne sans JavaScript.

$(document).ready(function () {

    $('.formulaire button[type="button"]').on('click', function () {
        document.getElementById('nom').focus();
    });

});
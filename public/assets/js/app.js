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

ok on peut continuer







4:31 PM






Worked for 20s



// J’ai vérifié : la fonction AJAX est dans api.js, et le gestionnaire du formulaire est dans app.js. Les éléments ciblés correspondent à ceux de projet.php. L’indentation de quelques lignes dans app.js est irrégulière, mais ne gêne pas le fonctionnement.
// On peut vérifier la réussite d’abord :
// 1. Ouvre un projet.
// 2. Remplace son nom par un nom valide, puis clique sur Renommer.
// 3. La page ne devrait pas se recharger. Le message « Le projet a été renommé. » devrait apparaître, et le titre de la page ainsi que le champ devraient afficher le nouveau nom.
// Essaie et dis-moi ce que tu observes. Ensuite, on vérifiera le refus d’un nom invalide et le bouton après une erreur.

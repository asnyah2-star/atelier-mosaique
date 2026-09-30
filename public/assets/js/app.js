// TODO (mission 3) : ajouter les interactions avec jQuery, chargé avant ce fichier.
// TODO (mission 5) : appeler les fonctions de api.js pour les échanges AJAX.
// Le moteur de mosaïque et Canvas peut conserver son JavaScript natif.
// La page de départ fonctionne sans JavaScript.
$(document).ready(function () {
    var envoiImagesEnCours = false;

    function afficherResultatsImages(resultats) {
        var liste = document.getElementById('resultats-envoi-images');
        if (!liste) {
            return;
        }

        liste.replaceChildren();
        liste.hidden = false;
        resultats.forEach(function (resultat) {
            var ligne = document.createElement('li');
            var nom = document.createElement('strong');

            nom.textContent = resultat.name + ' : ';
            ligne.appendChild(nom);
            ligne.appendChild(document.createTextNode(
                (resultat.success ? 'acceptée — ' : 'refusée — ') + resultat.message
            ));
            liste.appendChild(ligne);
        });
    }

    function ajouterImageGalerie(resultat, projectId, csrfToken) {
        var liste = document.getElementById('liste-galerie-images');
        var element = document.createElement('li');
        var figure = document.createElement('figure');
        var image = document.createElement('img');
        var legende = document.createElement('figcaption');
        var formulaire = document.createElement('form');

        image.src = 'image.php?id=' + encodeURIComponent(resultat.image_id)
            + '&project_id=' + encodeURIComponent(projectId);
        image.alt = resultat.name;
        image.loading = 'lazy';
        legende.textContent = resultat.name;
        figure.append(image, legende);

        formulaire.method = 'post';
        [
            ['action', 'demander_retrait'],
            ['csrf_token', csrfToken],
            ['project_id', projectId],
            ['image_id', resultat.image_id]
        ].forEach(function (champ) {
            var entree = document.createElement('input');
            entree.type = 'hidden';
            entree.name = champ[0];
            entree.value = champ[1];
            formulaire.appendChild(entree);
        });

        var boutonRetrait = document.createElement('button');
        boutonRetrait.type = 'submit';
        boutonRetrait.textContent = 'Retirer cette image';
        formulaire.appendChild(boutonRetrait);

        element.append(figure, formulaire);
        liste.prepend(element);
        document.getElementById('galerie-vide').hidden = true;
    }

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

    $('#form-envoi-images').on('submit', function (evenement) {
        evenement.preventDefault();
        if (envoiImagesEnCours) {
            return;
        }

        var formulaire = this;
        var bouton = document.getElementById('bouton-envoi-images');
        var message = document.getElementById('message-envoi-images');
        var projectId = formulaire.elements.project_id.value;
        var csrfToken = formulaire.elements.csrf_token.value;
        var formData = new FormData(formulaire);

        envoiImagesEnCours = true;
        bouton.disabled = true;
        bouton.textContent = 'Envoi en cours…';
        formulaire.setAttribute('aria-busy', 'true');
        message.textContent = 'Les images sont en cours de vérification et d’envoi…';

        window.apiEnvoyerImages(formData, projectId)
            .done(function (reponse) {
                afficherResultatsImages(reponse.results || []);
                message.textContent = reponse.message;

                (reponse.results || []).forEach(function (resultat) {
                    if (resultat.success) {
                        ajouterImageGalerie(resultat, reponse.project_id, csrfToken);
                    }
                });

                // Les fichiers refusés pourront être resélectionnés seuls.
                formulaire.querySelector('input[type="file"]').value = '';
            })
            .fail(function (xhr) {
                var reponse = xhr.responseJSON;
                message.textContent = reponse && reponse.message
                    ? reponse.message
                    : 'L’envoi a échoué. Vérifie ta connexion et réessaie.';
            })
            .always(function () {
                envoiImagesEnCours = false;
                bouton.disabled = false;
                bouton.textContent = 'Envoyer les images';
                formulaire.removeAttribute('aria-busy');
            });
    });

});

$(document).ready(function() {
    $('#btnAbonnement').click(function() {
        const pseudo = $(this).data('pseudo');
        const action = $(this).text() === 'S\'abonner' ? 'ajouterabonnement' : 'supprimerabonnement';

        $.post('../../app/utils/traitement_abonnement.php',
            {
                action: action,
                utilisateur: pseudo
            },
            function() {
            if (action === 'ajouterabonnement') {
                $('#btnAbonnement').text('Se désabonner');
            } else {
                $('#btnAbonnement').text('S\'abonner');
            }

        })
        .done(function(data) {
            const response = JSON.parse(data);
            $('#compteur_abonne').text('Abonnés : ' + response.nbAbonnes);
        });
    });
});
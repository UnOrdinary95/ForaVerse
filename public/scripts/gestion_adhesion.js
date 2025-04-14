$(document).ready(function() {
    $('#btnAdhesion').click(function() {
        const communaute_id = $(this).data('communaute_id');
        const action = $(this).text() === 'Rejoindre' ? 'rejoindrecommu' : 'quittercommu';

        $.post('../../app/utils/traitement_adhesion.php',
            {
                action: action,
                communaute_id: communaute_id
            },
            function() {
            if (action === 'rejoindrecommu') {
                $('#btnAdhesion').text('Quitter');
            } else {
                $('#btnAdhesion').text('Rejoindre');
            }
        })
        .done(function(data) {
            const response = JSON.parse(data);
            $('#compteurMembres').text(response.nbMembres + ' Membres');
        });
    });
});
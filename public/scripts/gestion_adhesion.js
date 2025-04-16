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

    $('#btnAdhesionPrivee').click(function(){
        const communaute_id = $(this).data('communaute_id');
        const buttonText = $(this).text();
        
        let action = '';
        
        if(buttonText === 'Demander à rejoindre') {
            action = 'rejoindrecommu';
        } else if(buttonText === 'Quitter') {
            action = 'quittercommu';
        } else {
            return; // Aucune action à effectuer
        }

        $.post('../../app/utils/traitement_adhesion.php',
            {
                action: action,
                communaute_id: communaute_id
            },
            function(data) {
                const response = JSON.parse(data);
                if (action === 'rejoindrecommu') {
                    $('#btnAdhesionPrivee').text('Demande en attente');
                } else if (action === 'quittercommu') {
                    $('#btnAdhesionPrivee').text('Demander à rejoindre');
                } else {
                    return; // Aucune action à effectuer
                }
            })
    });

    
});
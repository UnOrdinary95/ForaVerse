document.addEventListener('DOMContentLoaded', function() {
    // ===== SYSTÈME DE VOTE =====
    const upvoteButtons = document.querySelectorAll('.vote-up');
    const downvoteButtons = document.querySelectorAll('.vote-down');

    upvoteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            console.log('Clic sur le bouton upvote');
            gererVote(event, 1);
        });
    });
    
    downvoteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            console.log('Clic sur le bouton downvote');
            gererVote(event, -1);
        });
    });
    
    function gererVote(event, voteValue) {
        const voteContainer = event.currentTarget.closest('.vote-container');
        if (!voteContainer) {
            console.error('Conteneur de vote non trouvé');
            return;
        }
        
        const publicationId = voteContainer.dataset.publicationId;
        if (!publicationId) {
            console.error('ID de publication non trouvé');
            return;
        }
        
        const scoreElement = voteContainer.querySelector('.score-value');
        const upButton = voteContainer.querySelector('.vote-up');
        const downButton = voteContainer.querySelector('.vote-down');
        
        let finalVoteValue = voteValue;
        
        if (
            (voteValue === 1 && upButton.classList.contains('active')) ||
            (voteValue === -1 && downButton.classList.contains('active'))
        ) {
            finalVoteValue = 0;
        }
        
        console.log('Envoi du vote:', publicationId, finalVoteValue);
        
        $.ajax({
            type: 'POST',
            url: '../../app/utils/traitement_vote.php',
            data: {
                action: 'voter',
                publication_id: publicationId,
                valeur: finalVoteValue
            },
            dataType: 'json',
            success: function(response) {
                upButton.classList.remove('active');
                downButton.classList.remove('active');
                
                if (response.vote === 1) {
                    upButton.classList.add('active');
                    console.log('Vote positif enregistré');
                } else if (response.vote === -1) {
                    downButton.classList.add('active');
                    console.log('Vote négatif enregistré');
                } else {
                    console.log('Aucun vote actif, valeur reçue:', response.vote);
                }
                
                if (scoreElement) {
                    scoreElement.textContent = response.score;
                }
            },
        });
    }
});
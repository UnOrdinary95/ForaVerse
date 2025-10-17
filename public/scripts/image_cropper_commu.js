let cropper;

// Ajouter un gestionnaire d'événements pour le clic sur l'image de communauté
document.addEventListener('DOMContentLoaded', function() {
    const communauteImage = document.getElementById('communauteImage');
    if (communauteImage) {
        communauteImage.addEventListener('click', function() {
            document.getElementById('imageInput').click();
        });
    }
});

document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('imagePreview').src = event.target.result;
            document.getElementById('cropperContainer').style.display = 'block';

            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(document.getElementById('imagePreview'), {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1
            });
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('cancelButton').addEventListener('click', function() {
    document.getElementById('cropperContainer').style.display = 'none';
    document.getElementById('imageInput').value = '';
    if (cropper) {
        cropper.destroy();
    }
});

document.getElementById('cropButton').addEventListener('click', function() {
    const canvas = cropper.getCroppedCanvas({
        width: 200,
        height: 200
    });

    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('image', blob, 'community.jpg');
        
        // Récupérer le nom de la communauté depuis l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const nomCommu = urlParams.get('nomCommu');
        
        if (!nomCommu) {
            alert("Erreur: Impossible de déterminer le nom de la communauté.");
            return;
        }

        fetch('../../app/utils/upload_community_photo.php?nomCommu=' + encodeURIComponent(nomCommu), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'upload: ' + data.error);
            }
        });
    }, 'image/jpeg');
});
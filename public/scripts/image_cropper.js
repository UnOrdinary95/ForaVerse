let cropper;

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
        formData.append('image', blob, 'profile.jpg');

        fetch('../../app/utils/upload_photo.php', {
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
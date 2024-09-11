


function previewImages(input, previewElementId) {
    const preview = document.getElementById(previewElementId);
    preview.innerHTML = "";

    if (input.files) {
        const files = Array.from(input.files);
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                img.style.maxWidth = "100px";
                img.style.margin = "5px";
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }
}


function uploadImages(houseId) {
    const formData = new FormData();
    formData.append('house_id', houseId);

    const mainImg = document.getElementById('main-file-upload').files[0];
    if (mainImg) {
        formData.append('main_img', mainImg);
    } else {
        console.log('No main image selected');
    }

    const indoorImgs = document.getElementById('indoor-file-upload').files;
    for (let i = 0; i < indoorImgs.length; i++) {
        formData.append('indoor_img[]', indoorImgs[i]);

    }

    const outdoorImgs = document.getElementById('outdoor-file-upload').files;
    for (let i = 0; i < outdoorImgs.length; i++) {
        formData.append('outdoor_img[]', outdoorImgs[i]);

    }

    console.log('FormData contents:');
    for (let pair of formData.entries()) {
        console.log(pair[0], pair[1]);
    }

    fetch('../upload_imgs.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearForm();
                alert('added successfully');
            } else {
                alert('Error uploading images: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function imgsAreValid() {
    const indoorImgs = document.getElementById('indoor-file-upload').files;
    const outdoorImgs = document.getElementById('outdoor-file-upload').files;
    const mainImg = document.getElementById('main-file-upload').files[0];

    if (indoorImgs.length >= 5 && outdoorImgs.length >= 5 && mainImg) {
        return true;
    } else {
        return false;
    }
}




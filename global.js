let user;
let houseDetail;


window.addEventListener('load', () => {
    getUser();
});

function getUser(){
    let userFromLS = localStorage.getItem('user');
    if (userFromLS) {
        user = JSON.parse(userFromLS);
        user.id = parseInt(user.id, 10);
    }
}

function handleAuthentication(){
    if (user) {
        handleRoles();
    } else {
        window.location.href = '/holidayhome/index.html';
    }
}

function handleRoles(){
    let currentUrl = window.location.href; 
    if (currentUrl.includes('admin-view') && user.role === 'user') {
        window.location.href = '/holidayhome/user-view/user-view.php';
    }
    if (currentUrl.includes('user-view') && user.role === 'admin' ) {
        window.location.href = '/holidayhome/admin-view/gallery/gallery.php';
    }
}

async function getHouseDetails(id) {
    try {
        const response = await fetch('/holidayhome/get_house_details.php?house_id=' + id);
        if (!response.ok) {
            throw new Error('Netzwerkantwort war nicht ok.');
        }
        const text = await response.text();
        houseDetail = JSON.parse(text);
    } catch (error) {
        console.error('Fehler beim Abrufen der Hausdetails:', error);
    }
}


function getMainImg() {
    const images = houseDetail.images;
    const mainImage = images.find(img => img.image_type === 'main');
    imgUrl = mainImage.image_url;

    return imgUrl;
}

function getIndoorImages() {
    const images = houseDetail.images;
    const indoorImages = images.filter(img => img.image_type === 'indoor');
    
    return indoorImages;
}

function getOutdoorImages() {
    const images = houseDetail.images;
    const outdoorImages = images.filter(img => img.image_type === 'outdoor');
    
    return outdoorImages;
}

function getFeedback(message){
    const container = document.getElementById('feedback-container');
    const dialog = document.getElementById('feedback-dialog');
    dialog.showModal();

    container.innerHTML = `
        <p>${message}</p>
        <button onclick="closeFeedback()" class="btn-2">Ok</button>
    `;
}

function closeFeedback(){
    const dialog = document.getElementById('feedback-dialog');
    dialog.close();
}


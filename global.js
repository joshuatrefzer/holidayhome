let user = {
    name: 'Max',
    id: 1 //admin 1
}

let houseDetail;


async function getHouseDetails(id) {
    try {
        const response = await fetch('/holidayhome/get_house_details.php?house_id=' + id);
        if (!response.ok) {
            throw new Error('Netzwerkantwort war nicht ok.');
        }
        const text = await response.text();
        houseDetail = JSON.parse(text);
        console.log(houseDetail);
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


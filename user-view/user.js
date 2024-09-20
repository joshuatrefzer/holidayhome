houses = [];


window.onload = () => {
    getAllHouses();
}

function getAllHouses(){
    fetch('get_all_houses.php').
    then(response => response.json()).
    then(data => {
        houses = data
        updateHouses();
        
    });
}


async function updateHouses() {
    const container = document.getElementById('house-container');
    
    for (const house of houses) {
        const img = await getMainImg(house.id); 

        container.innerHTML += `
        <div onclick="prepareHouseDetail(${house.id})"  id="house-container" class="house-container user-gallery">
            <img src="/holidayhome/${img}" alt="${house.name}-Image">
            <div class="img-overlay">
                <h2>${house.name}</h2>
                <span>${house.country}</span>
                <span class="price">Dayprice: <strong>${house.price_per_day}$</strong></span>
            </div>
        </div>
        `;
    }
}


async function getMainImg(id) {
    try {
        const response = await fetch(`../get_main_img.php?house_id=${id}`);
        const data = await response.json();

        if (data.success) {
            return data.image.image_url;
        } else {
            console.log('Message:', data.message);
            alert(data.message);
            return 'error';
        }
    } catch (error) {
        console.error('Fetch error:', error);
        return 'error';
    }
}


function prepareHouseDetail(id) {
    let index = houses.findIndex(house => house.id == id);
    houseDetail = houses[index];
    window.location.href = "/holidayhome/user-view/house-detail/house-detail.php?house_id=" + houseDetail.id;
}




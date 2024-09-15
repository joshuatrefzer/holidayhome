
houses = [];


window.onload = (() => {
    getHousesAfterAdminId(admin.id);
});


function getHousesAfterAdminId(adminId) {
    fetch(`get_offerings.php?landlord=${adminId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                houses = data.houses;
                updateHouses();
            } else {
                console.log('Message:', data.message);
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}


async function getMainImg(id) {
    try {
        const response = await fetch(`../../get_main_img.php?house_id=${id}`);
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

async function updateHouses() {
    const container = document.getElementById('house-container');
    
    for (const house of houses) {
        const img = await getMainImg(house.id); 

        container.innerHTML += `
        <div id="house-container" class="house-container">
            <img src="/holidayhome/${img}" alt="${house.name}-Image">
            <div class="img-overlay">
                <h2>${house.name}</h2>
                <span>${house.country}</span>
                <span class="price">Dayprice: <strong>${house.price_per_day}$</strong></span>
            </div>

            <div class="button-overlay">
                <button class="btn-4"><img src="../../img/delete.png" alt="Delete"></button>
                <button class="btn-4"><img src="../../img/edit.png" alt="Edit"></button>
            </div>
        </div>
        `;
    }
}

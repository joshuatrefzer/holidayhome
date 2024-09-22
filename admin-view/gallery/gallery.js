
houses = [];
let houseToDelete;

window.onload = (() => {
    getHousesAfterAdminId(user.id);
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
    container.innerHTML = "";
    
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
                <button onclick="deleteQuestion(${house.id})" class="btn-4"><img src="../../img/delete.png" alt="Delete"></button>
                <button onclick="editHouse(${house.id})" class="btn-4"><img src="../../img/edit.png" alt="Edit"></button>
            </div>
        </div>
        `;
    }
}

function deleteQuestion(id){
    houseToDelete = id;
    const dialog = document.getElementById('delete-question');
    dialog.showModal();
}


function deleteHouse() {
    let id = houseToDelete;
    fetch('../delete_house.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            getHousesAfterAdminId(user.id);
            closeDialog();
        } else {
            console.error('Fehler:', data.message);
            alert('Fehler: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Ein Fehler ist aufgetreten');
    });
}


function closeDialog(){
    const dialog = document.getElementById('delete-question');
    dialog.close();
}

function editHouse(houseId){
    window.location.href = '../edit-house/edit_house.php?house_id='+ houseId;
}
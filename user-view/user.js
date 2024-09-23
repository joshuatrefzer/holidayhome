houses = [];

window.onload = () => {
    handleAuthentication();
    getAllHouses();
}

function getAllHouses() {
    fetch('get_all_houses.php').
        then(response => response.json()).
        then(data => {
            houses = data
            updateHouses();

        });
}


async function updateHouses() {
    const container = document.getElementById('house-container');
    container.innerHTML = "";
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
            getFeedback('Error by fetching the imgs');
            return 'error';
        }
    } catch (error) {
        getFeedback('Error by fetching the imgs');
        return 'error';
    }
}


function prepareHouseDetail(id) {
    let index = houses.findIndex(house => house.id == id);
    houseDetail = houses[index];
    window.location.href = "/holidayhome/user-view/house-detail/house-detail.php?house_id=" + houseDetail.id;
}


function search() {
    const searchInput = document.getElementById('search').value;
    const searchTerms = searchInput.split(',').map(term => term.trim()).filter(term => term !== '');

    if (searchInput.length == 0) {
        updateHouses();
        return;
    }

    if (searchTerms.length > 3) {
        getFeedback('You have reached the maximum of 3 search values');
        return;
    }

    const searchData = {
        search: searchTerms.join(',')
    };

    fetch('search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(searchData)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSearchResult(data.houses);
            } else {
                getFeedback('Error by searching houses..');
            }
        })
        .catch(error => {
            getFeedback('Error by searching houses..');
        });
}

function showSearchResult(houseList){
    const container = document.getElementById('house-container');
    container.innerHTML = "";

    for (const house of houseList) {
        container.innerHTML += `
        <div onclick="prepareHouseDetail(${house.id})"  id="house-container" class="house-container user-gallery">
            <img src="/holidayhome/${house.main_img}" alt="${house.name}-Image">
            <div class="img-overlay">
                <h2>${house.name}</h2>
                <span>${house.country}</span>
                <span class="price">Dayprice: <strong>${house.price_per_day}$</strong></span>
            </div>
        </div>
        `;
    }
}




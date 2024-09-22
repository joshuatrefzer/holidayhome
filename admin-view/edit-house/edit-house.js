

let houseId;
let existingImages = [];

window.addEventListener('load', () => {
    houseId = getHouseIdFromUrl();
    editMode = true;
    showData(houseId); 
});

function getHouseIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('house_id'); 
}

async function showData() {
    try {
        await getHouseDetails(houseId); 
        fillForm();
        
    } catch (error) {
        console.error('Fehler beim Anzeigen der Daten:', error);
    }
}

function fillForm(){
    fillInputFields();
}

function fillInputFields(){
    document.getElementById('name').value = houseDetail.house.name;
    document.getElementById('price-per-day').value = houseDetail.house.price_per_day;
    document.getElementById('country').value = houseDetail.house.country;
    document.getElementById('street').value = houseDetail.house.street;
    document.getElementById('house-number').value = houseDetail.house.house_number;
    document.getElementById('postal-code').value = houseDetail.house.postal_code;
    selectedTags=  fillTags();
    updateSelectedTags();
    fillFacilities();
    fillActivities();
    fillImages();
}


function fillTags() {
    let tags = houseDetail.tags;
    let newSelection = [];

    tags.forEach(t => {
        const filteredTags = allTags.filter(tag => tag.tag_name === t);
        newSelection.push(...filteredTags); 
    });
    
    return newSelection;
}

function fillFacilities() {
    const myFacilities = houseDetail.facilities;
    const facilityIds = [];

    // Filter alle relevanten Facilities basierend auf den Namen
    const newSelection = allFacilities.filter(fac => myFacilities.includes(fac.facility_name));

    // Iteriere durch die gefilterten Facilities und setze die Checkbox auf gecheckt
    newSelection.forEach(element => {
        const checkbox = document.getElementById(`facility${element.id}`);
        if (checkbox) { // Überprüfen, ob die Checkbox existiert
            checkbox.checked = true; // Checkbox auf "gecheckt" setzen
            facilityIds.push(element.id);
        }
    });

    // Setze die globale Variable facilities
    facilities = facilityIds;
}

function fillActivities() {
    const myActivities = houseDetail.activities;
    const activityIds = [];

    // Filter alle relevanten Facilities basierend auf den Namen
    const newSelection = allActivities.filter(ac => myActivities.includes(ac.activity_name));

    // Iteriere durch die gefilterten Facilities und setze die Checkbox auf gecheckt
    newSelection.forEach(element => {
        const checkbox = document.getElementById(`activity${element.id}`);
        if (checkbox) { // Überprüfen, ob die Checkbox existiert
            checkbox.checked = true; // Checkbox auf "gecheckt" setzen
            activityIds.push(element.id);
        }
    });

    // Setze die globale Variable facilities
    activities = activityIds;
}


function fillImages() {
    let container = document.getElementById('existing-imgs');
    let imgs = houseDetail.images.filter( img => img.image_type != 'main');
    
    imgs.forEach(i => {
        container.innerHTML += `
            <img class="existing-img" src="/holidayhome${i.image_url}" alt="img">
        `;

    });
    
}


function editHouse(event){
    event.preventDefault();

    if (formIsValid()) {
        const houseData = getJSON();
        updateHouseData(houseData);
        uploadImages(houseId);
        updateHouseActivities();
        updateHouseFacilities();

    } else {
        alert('Bitte Fülle die Form richtig aus!')
    }
}

function updateHouseActivities() {
    const data = {
        house_id: houseId,
        activities: activities  
    };

    fetch('../update_activities.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Activities successfully updated:', data.message);
        } else {
            console.error('Failed to update activities:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}


function updateHouseFacilities() {
    const data = {
        house_id: houseId,
        facilities: facilities  
    };

    fetch('../update_facilities.php', {  
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Facilities successfully updated:', data.message);
        } else {
            console.error('Failed to update facilities:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}


function updateHouseData(houseData) {
    fetch('../update_house.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: houseId,
            ...houseData
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('House successfully updated:', data.message);
        } else {
            console.error('Update failed:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}



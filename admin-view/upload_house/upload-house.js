
let allTags = [];
let selectedTags = [];
let facilities = [];
let activities = [];
createdHouseId = 0;


window.onload = () => {
    fetchFacilities();
    fetchActivities();
    fetchTags();
};

function fetchFacilities() {
    fetch('../get_facilities.php')
        .then(response => response.json())
        .then(data => {
            console.log(data);

            let facilitiesSelect = document.getElementById('facilities');
            data.forEach(facility => {
                facilitiesSelect.innerHTML += `
                <input class="checkbox" type="checkbox" value="${facility.id}" onchange="handleCheckboxChange(this , 'facilities')">
                <label>${facility.facility_name}</label>
            `;
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function fetchActivities() {
    fetch('../get_activities.php')
        .then(response => response.json())
        .then(data => {
            let activitySelect = document.getElementById('activities');
            data.forEach(activity => {
                activitySelect.innerHTML += `
                <input class="checkbox" type="checkbox" value="${activity.id}" onchange="handleCheckboxChange(this , 'activities')">
                <label>${activity.activity_name}</label>
            `;
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function fetchTags() {
    fetch('../get_tags.php')
        .then(response => response.json())
        .then(data => {
            allTags = data;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


function searchTags() {
    const input = document.getElementById('search-input').value;
    const container = document.getElementById('tag-dropdown');

    if (input.length <= 0) {
        hideDropdown();
        return;
    }

    container.classList.add('d-flex');
    const filteredTags = allTags.filter((tag) => {
        return tag.tag_name.includes(input) && !selectedTags.some(selectedTag => selectedTag.tag_name === tag.tag_name);
    });


    if (filteredTags.length == 0 && input.length >= 3 && !selectedTags.some(selectedTag => selectedTag.tag_name === input)) {
        container.innerHTML = `
            <button  type="button" class="btn-3" onclick="createNewTag('${input}');">add "${input}" to tags </button>
        `;
        return;
    }

    container.innerHTML = "";
    filteredTags.forEach(tag => {
        container.innerHTML += `
            <span onclick="selectTag(${tag.id} , '${tag.tag_name}')"  class="tag select-tag">#${tag.tag_name}</span>
        `;
    });
}

function selectTag(id, name) {
    let tag = {
        id: id,
        tag_name: name,
    }
    selectedTags.push(tag);
    updateSelectedTags();
}


function createNewTag(input) {
    const searchInput = document.getElementById('search-input');
    searchInput.value = "";

    fetch('../create_tag.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ tag_name: input })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchTags();
                selectTag(data.tag.id, `${data.tag.tag_name}`);
            } else {
                alert('Fehler beim Erstellen des Tags.');
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
        });
}


function updateSelectedTags() {
    const container = document.getElementById('tag-container');
    hideDropdown();

    container.innerHTML = "";
    selectedTags.forEach(tag => {
        container.innerHTML += `
            <span class="tag">#${tag.tag_name}
                <span onclick="removeSelection(${tag.id})"  class="remove-tag" ><img class="close-little" src="../../img/close.svg"><span>
            </span>
        `;
    });
}

function hideDropdown() {
    const dropDown = document.getElementById('tag-dropdown');
    dropDown.innerHTML = "";
    dropDown.classList.remove('d-flex');
    const searchInput = document.getElementById('search-input');
    searchInput.value = "";
}

function removeSelection(id) {
    let index = selectedTags.findIndex(tag => tag.id === id);
    if (index !== -1) {
        selectedTags.splice(index, 1);
        updateSelectedTags();
    }

}

function clearForm() {
    document.getElementById('main-preview').innerHTML = "";
    document.getElementById('indoor-preview').innerHTML = "";
    document.getElementById('outdoor-preview').innerHTML = "";
    hideDropdown();
    selectedTags = [];
    updateSelectedTags();
}


function handleCheckboxChange(checkbox, arrayName) {
    const boxId = checkbox.value;

    if (arrayName === 'facilities') {
        if (checkbox.checked) {
            facilities.push(boxId);
        } else {
            facilities = facilities.filter(id => id !== boxId);
        }
    }

    if (arrayName === 'activities') {
        if (checkbox.checked) {
            activities.push(boxId);
        } else {
            activities = activities.filter(id => id !== boxId);
        }
    }
}


function uploadHouse(event) {
    event.preventDefault();

    if (formIsValid()) {
        const house = getJSON();
        createNewHouse(house);
    } else {
        alert('Bitte Fülle die Form richtig aus!')
    }
}


function createNewHouse(house) {
    fetch('../create_house.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(house)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createdHouseId = data.id;
                createHouseActivities();
                createHouseFacilities();
                createTags();
                uploadImages(createdHouseId);
            }
        })
        .catch(error => console.error('Error:', error));
}


function createTags() {
    const tagIds = [];
    selectedTags.forEach(tag => {
        tagIds.push(tag.id);
    });

    tagIds.forEach(tagId => {
        const data = {
            tag_id: tagId,
            house_id: createdHouseId
        }
        fetch('../set_house_tags.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => console.error('Error:', error));
    });
}


function createHouseActivities() {
    activities.forEach(activity => {
        const data = {
            house_id: createdHouseId,
            activity_id: activity
        }
        fetch('../create_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => console.error('Error:', error));
    });
}


function createHouseFacilities() {
    facilities.forEach(facility => {
        const data = {
            house_id: createdHouseId,
            facility_id: facility
        }
        fetch('../create_facility.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => console.error('Error:', error));
    });
}


function getJSON() {
    let data = {
        name: getValue('name'),
        price_per_day: getValue('price-per-day'),
        country: getValue('country'),
        street: getValue('street'),
        house_number: getValue('house-number'),
        postal_code: getValue('postal-code'),
        landlord: 1
    }
    return data
}

function getValue(id) {
    let value = document.getElementById(id).value;
    return value;
}

function formIsValid() {
    return activities.length >= 3 &&
        facilities.length >= 3 &&
        selectedTags.length >= 5 &&
        imgsAreValid();
}


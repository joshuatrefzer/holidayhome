
let allTags = [];
let selectedTags = [];


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
                <input class="checkbox" type="checkbox" value="${facility.id}">${facility.facility_name}</input>
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
                <input class="checkbox" type="checkbox" value="${activity.id}">${activity.activity_name}</input>
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
            <span onclick="selectTag(${tag.id} , '${ tag.tag_name}')"  class="tag select-tag">#${tag.tag_name}</span>
        `;
    });
}

function selectTag(id, name){
    let tag = {
        id: id,
        tag_name:name,
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
            selectTag(data.tag.id , `${data.tag.tag_name}`);
        } else {
            alert('Fehler beim Erstellen des Tags.');
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
    });
}


function updateSelectedTags(){
    const container = document.getElementById('tag-container');
    hideDropdown();

    container.innerHTML = "";
    selectedTags.forEach( tag => {
        container.innerHTML += `
            <span class="tag">#${tag.tag_name}
                <span onclick="removeSelection(${tag.id})"  class="remove-tag" ><img class="close-little" src="../../img/close.svg"><span>
            </span>
        `;
    });
}

function hideDropdown(){
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
};


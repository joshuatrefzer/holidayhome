let houseId;
let today = new Date().toISOString().split('T')[0];
let tomorrow = getTomorrow();
let bookingDetails;
let bookedDaysAmount;
let galleryHouseIndex;
let indoorImages = [];
let outdoorImages = [];
let selectedFacilities = [];

window.onload = () => {
    handleAuthentication();
    houseId = getHouseIdFromUrl();
    showData();
    initDatePicker();
}

function getHouseIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('house_id'); 
    
    
}

async function showData() {
    try {
        await getHouseDetails(houseId); 
        showMainImg();
        showActivities();
        showFacilities();
        showTags();
        showHouseDetails();
        showImages();
        getBookingDates();
    } catch (error) {
        getFeedback('Error by showing data');
    }
}

function showMainImg() {
    const img = document.getElementById('main-img');
    const url = '/holidayhome' + getMainImg(); 
    img.src = url;
}

function initDatePicker(){
    document.getElementById("start-date").setAttribute("min", today);
    document.getElementById("end-date").setAttribute("min", tomorrow);
}

function getTomorrow() {
    let today = new Date();
    let tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
}

function showActivities(){
    const container = document.getElementById('activity-container');
    let activities = houseDetail.activities;
    activities.forEach( activity => {
        container.innerHTML += `
            <span class="activity">${activity}</span>
        `;
    });
}

function showFacilities(){
    const container = document.getElementById('facility-container');
    let facilities = houseDetail.facilities;
    facilities.forEach( facility => {
        container.innerHTML += `
             <label>
                <input onchange="handleCheckboxChange(this)" class="checkbox" type="checkbox" value="${facility}">
                <span >${facility}</span>
            </label>
        `;
    });
}

function handleCheckboxChange(checkbox) {
    const boxId = checkbox.value;
    if (checkbox.checked) {
        selectedFacilities.push(boxId);
    } else {
        selectedFacilities = selectedFacilities.filter(id => id !== boxId);
    }
}

function showTags(){
    const container = document.getElementById('tag-container');
    let tags = houseDetail.tags;
    tags.forEach( tag => {
        container.innerHTML += `
            <span class="tag">#${tag}</span>
        `;
    });
}

function showHouseDetails() {
    const house = houseDetail.house;
    const container = document.getElementById('house-container');
    container.innerHTML = `
        <div class="row">
                <h1>${house.name}</h1>
            </div>
            <div class="row">
                <h3 class="price">${house.price_per_day}$ per day</h3>
            </div>
            <div class="adress">
                <span class="country">${house.country}</span><br>
                <span>${house.street}</span>
                <span>${house.house_number}</span><br>
                <span>${house.postal_code}</span>
            </div>
    
    `;
}

function showImages(){
    indoorImages = getIndoorImages();
    outdoorImages = getOutdoorImages();
    showIndoorImages();
    showOutdoorImages();
}

function showIndoorImages(){
    const container = document.getElementById('indoor-container');
    indoorImages.forEach( img => {
        container.innerHTML += `
            <img onclick="openGallery('${img.image_url}')" src="/holidayhome${img.image_url}" alt="" class="gallery-image">
        `;
    });
}

function showOutdoorImages(){
    const container = document.getElementById('outdoor-container');
    outdoorImages.forEach( img => {
        container.innerHTML += `
            <img onclick="openGallery('${img.image_url}')" src="/holidayhome${img.image_url}" alt="" class="gallery-image">
        `;
    });
}

function getBookingDates() {
    const houseId = houseDetail.house.id;

    fetch('get_booking_dates.php?house_id=' + houseId)
        .then(response => {
            if (!response.ok) {
                getFeedback('Something went wrong');
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                const disabledDates = [];

                result.bookings.forEach(booking => {
                    let currentDate = new Date(booking.check_in);
                    const endDate = new Date(booking.check_out);

                    while (currentDate <= endDate) {
                        disabledDates.push(new Date(currentDate)); 
                        currentDate.setDate(currentDate.getDate() + 1); 
                    }
                });

                disableBookedDates(disabledDates);
            } else {
                getFeedback('Error ..wrong booking dates' + result.message);
            }
        })
        .catch(error => {
            getFeedback('Error ..wrong booking dates' + error);
        });
}

function disableBookedDates(disabledDates) {
    const startDatePicker = document.getElementById('start-date');
    const endDatePicker = document.getElementById('end-date');

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const disabledDateStrings = disabledDates.map(date => formatDate(date));

    startDatePicker.addEventListener('input', function() {
        const selectedDate = new Date(startDatePicker.value);
        if (disabledDateStrings.includes(startDatePicker.value)) {
            startDatePicker.setCustomValidity("Dieser Tag ist bereits belegt.");
        } else {
            startDatePicker.setCustomValidity("");
        }
    });

    endDatePicker.addEventListener('input', function() {
        const selectedDate = new Date(endDatePicker.value);
        if (disabledDateStrings.includes(endDatePicker.value)) {
            endDatePicker.setCustomValidity("Dieser Tag ist bereits belegt.");
        } else {
            endDatePicker.setCustomValidity("");
        }
    });

    startDatePicker.setAttribute('min', new Date().toISOString().split('T')[0]);
    endDatePicker.setAttribute('min', new Date().toISOString().split('T')[0]);

    startDatePicker.setAttribute('data-disabled-dates', disabledDateStrings);
    endDatePicker.setAttribute('data-disabled-dates', disabledDateStrings);
}


function handleSubmit(event) {
    event.preventDefault();
    if (!dateRangeIsValid()) return;

    let startDate = document.getElementById('start-date').value;
    let endDate = document.getElementById('end-date').value;
    let totalPrice = getPrice(startDate, endDate);
    
    let data = {
        user_id: user.id,
        house_id: houseId,
        check_in: startDate,
        check_out: endDate,
        total_price: totalPrice,
        facilities: selectedFacilities 
    };

    fetch('create_booking.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            bookingDetails = result.booking;
            showDialog();
        } else {
            getFeedback('Something went wrong, error by booking your house :(');
        }
    })
    .catch(error => {
        getFeedback('Something went wrong, error by booking your house :(');
    });
}



function getPrice(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    const timeDifference = end - start;
    const daysAmount = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
    bookedDaysAmount = daysAmount;

    return daysAmount > 0 ? daysAmount * houseDetail.house.price_per_day : 0; 
}

function dateRangeIsValid() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    if (!startDate || !endDate) {
        return true; 
    }

    const start = new Date(startDate);
    const end = new Date(endDate);

    if (start > end) {
        document.getElementById('end-date').setCustomValidity('Das Enddatum darf nicht vor dem Startdatum liegen.');
        return false;
    } else {
        document.getElementById('end-date').setCustomValidity('');
        return true;
    }
}

function showDialog(){
    const dialog = document.getElementById('bookingDialog');
    dialog.showModal();
    dialog.classList.remove('hide');
    showBookingDetails();
}

function showBookingDetails(){
    let container = document.getElementById('booking-details');
    container.innerHTML += `
            <table class="booking-table">
            <thead>
                <tr>
                    <th>Details</th>
                    <th style="text-align: end;">Values</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Your house</td>
                    <td>${houseDetail.house.name}</td>
                </tr>
                <tr>
                    <td>Check-in</td>
                    <td>${convertDateFormat(bookingDetails.check_in)}</td>
                </tr>
                <tr>
                    <td>Check-out</td>
                    <td>${convertDateFormat(bookingDetails.check_out)}</td>
                </tr>
                <tr>
                    <td>Dayprice</td>
                    <td>${houseDetail.house.price_per_day}$</td>
                </tr>
                <tr>
                    <td>Booked days</td>
                    <td>${bookedDaysAmount}</td>
                </tr>
                <tr>
                    <td class="total-sum">Total Sum</td>
                    <td class="total-sum">${bookingDetails.total_price}$</td>
                </tr>
            </tbody>
        </table>
    
    `;
}

function convertDateFormat(dateString) {
    const [year, month, day] = dateString.split('-');
    return `${day}.${month}.${year}`;
}


function openGallery(url){
    debugger
    let dialog = document.getElementById('gallery');
    dialog.showModal();
    let imgs = houseDetail.images;
    galleryHouseIndex= imgs.findIndex( img => img.image_url === url );
    showGalleryImg(galleryHouseIndex);
}

function showGalleryImg(index){
    debugger
    let imgs = houseDetail.images;
    let container = document.getElementById('gallery-container');

    if (index > imgs.length || index < 0) {
        galleryHouseIndex = 0;
    } else {
        galleryHouseIndex = index;
    }

    container.innerHTML = `
        <img class="gallery-img" src="/holidayhome${imgs[galleryHouseIndex].image_url}" alt="${imgs[index].image_url}">
    `;
}

function nextImg() {
    let newIndex= galleryHouseIndex + 1;
    showGalleryImg(newIndex);
}

function previousImg() {
    let newIndex= galleryHouseIndex - 1;
    showGalleryImg(newIndex);
}

function closeGallery(){
    let dialog = document.getElementById('gallery');
    dialog.close();
}
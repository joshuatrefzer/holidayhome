<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../global.css">
    <link rel="stylesheet" href="house-detail.css">
    <script src="../../global.js"></script>
    <script src="house-detail.js"></script>
</head>
<body>
<?php include '../../header/user-header.php'; ?> 
<main class="component-wrapper">

    <a class="back-button" href="/holidayhome/user-view/user-view.php"><img src="../../img/back.png" alt=""></a>

    <div class="left">
        <img class="br main-img" id="main-img" src="" alt="img">
        <div id="tag-container" class="tag-container left-boxes br"></div>
        <div id="activity-container" class="activity-container left-boxes br">
            <h3>Activities:</h3>
        </div>
        <form id="form" onsubmit="handleSubmit(event)" >
            <div id="facility-container" class="facility-container left-boxes br">
                <h3>Select your facilities:</h3>
            </div>
            <div class="left-boxes">
                <input class="datepicker" required id="start-date" type="date">
                <input class="datepicker" required id="end-date" type="Date">

                <button style="margin: 0;" class="btn-1" type="submit">Book house</button>
            </div>
        </form>
        
    </div>

    <div class="right">
        <div id="house-container" class="house-container"></div>
        <div class="img-container">
            <div class="indoor-container">
                <h3 class="fixed-headline" >Indoor</h3>
                <div id="indoor-container" class="row"></div>
            </div>

            <div class="indoor-container">
                <h3 class="fixed-headline" >Outdoor</h3>
                <div id="outdoor-container" class="row"></div>
            </div>
        </div>
        
    </div>

    <dialog class="hide booking-dialog" id="bookingDialog"> 
        <h3>Booking successful</h3>
        <div id="booking-details" class="booking-details"></div>
        <div class="row-space-around">
            <button class="btn-1" onclick="window.location.href = '/holidayhome/user-view/user-view.php'">Back to main-page</button>
        </div>
    </dialog>

    <dialog class="gallery" id="gallery">
        <span class="close" onclick="closeGallery()">+</span>
        <div id="gallery-container" class="gallery-container">
            
        </div>
        <div class="row-space-around">
            <button onclick="previousImg()" class="btn-1">Previous</button>
            <button onclick="nextImg()" class="btn-2">Next</button>
        </div>
    </dialog>

    <dialog class="feedback-dialog" id="feedback-dialog">
        <div id="feedback-container" class="feedback-container"></div>
    </dialog>

</main>   






</body>
</html>
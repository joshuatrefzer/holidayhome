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
    <div class="left">
        <img class="br main-img" id="main-img" src="" alt="img">
        <div id="tag-container" class="tag-container left-boxes br"></div>
        <div id="activity-container" class="activity-container left-boxes br">
            <strong>Activities:</strong>
        </div>
        <form id="form" onsubmit="handleSubmit(event)" >
            <div id="facility-container" class="facility-container left-boxes br">
                <strong>Facilities:</strong>
            </div>
            <div class="left-boxes">
                <input class="datepicker" required id="start-date" type="date">
                <input class="datepicker" required id="end-date" type="Date">

                <button class="btn-1" type="submit">Book house</button>
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
</main>   
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../gallery.css">
    <script src="../../global.js"></script>
    <script src="gallery.js"></script>
    <title>Document</title>
</head>
<body>
    <!-- HEADER -->
    <?php include '../../header/admin-header.php'; ?>    

    <main class="component-wrapper">
        <h1>MY OFFERINGS</h1>
        <div id="house-container" class="container"></div>


        <dialog id="delete-question">
            <p>Do you really want to delete this offering?</p>
            <div class="row-space-around">
                <button onclick="closeDialog()" class="btn-1">No</button>
                <button onclick="deleteHouse()" class="btn-2">Yes</button>
            </div>
        </dialog>

        <dialog class="feedback-dialog" id="feedback-dialog">
            <div id="feedback-container" class="feedback-container"></div>
        </dialog>

    </main>
    


</body>
</html>
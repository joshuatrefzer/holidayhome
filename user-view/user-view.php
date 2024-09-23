<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../gallery.css">
    <link rel="stylesheet" type="text/css" href="user.css">
    <script src="../global.js"></script>
    <script src="user.js"></script>
    <title>Document</title>
</head>
<body>
    <!-- HEADER -->
    <?php include '../header/user-header.php'; ?>    

    <main class="component-wrapper">
        <h1>Welcome to HOLIDAYHOME</h1>
        <div id="house-container" class="container"></div>
        <input placeholder="search houses" class="search" onkeyup="search()" id="search" type="text">
    </main>
    
    <dialog class="feedback-dialog" id="feedback-dialog">
        <div id="feedback-container" class="feedback-container"></div>
    </dialog>


</body>
</html>
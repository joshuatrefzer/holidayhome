<?php 

require "../../db_connection.php";

$name = "";
$price_per_day = "";


$errorMessage = "";
$successMessage = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];

    do {
        if (empty($name)) {
            $errorMessage = 'All fields are required';
            break;
        }

        // Insert new House into the DB using prepared statements
        $stmt = $conn->prepare("INSERT INTO houses (name) VALUES (?)");
        if (!$stmt) {
            $errorMessage = "Prepare failed: " . $conn->error;
            break;
        }
        $stmt->bind_param("s", $name);
        $result = $stmt->execute();

        if ($result === false) {
            $errorMessage = "Execute failed: " . $stmt->error;
            break;
        }

        // Clear form and set success message
        $name = "";
        $successMessage = "House added successfully";

        // Redirect to another page
        header("Location: /holidayhome/admin-view/index-admin.php");
        exit;

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload House</title>
    <link rel="stylesheet" type="text/css" href="upload_house.css">
    <script src="upload-house.js"></script>
    
</head>
<body>

<!-- HEADER -->
    <?php include '../../header/header.php'; ?>    


<h1>New offering</h1>

<form class="column-center" method="post" id="upload-form">
    <!-- input Fields -->
    <div class="form-row">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input placeholder="price per day $" type="text" name="price_per_day" value="<?php echo htmlspecialchars($price_per_day); ?>">
        <input placeholder="country" type="text" name="country" value="<?php echo htmlspecialchars($country); ?>">
    </div>

    <div class="form-row">
        <input placeholder="street" type="text" name="street" value="<?php echo htmlspecialchars($street); ?>">
        <input placeholder="house number" type="text" name="house_number" value="<?php echo htmlspecialchars($house_number); ?>">
        <input placeholder="postal code" type="text" name="postal_code" value="<?php echo htmlspecialchars($postal_code); ?>">
    </div>

<!-- checkboxes -->
    <div class="checkbox-container">
        <h3>Activities:</h3>
        <div class="checkboxes d-f-ai-c " id="activities"></div>

    </div>

    <div class="checkbox-container">
        <h3>Facilities:</h3>
        <div class="d-f-ai-c" id="facilities"></div>
    </div>


<!-- TAgs -->
<div class="center-column full-width">
    <h3>Tags</h3>
    <div class="tag-component">
        <div class="tag-input-container">
            <input id="search-input" placeholder="search tags" class="tag-search" onkeyup="searchTags()" type="text">
            <div id="tag-dropdown" class="dropdown"></div>
        </div>
        <div id="tag-container" class="tag-container"></div>
    </div>
    
</div>

<!-- IMG UPLOADS -->
    <div class="form-row">
    <!-- Upload Main Image -->
     <div class="imgs-container">
        <label for="main-file-upload" class="custom-file-upload btn-2">
            Upload Main Image
        </label>
        <input id="main-file-upload" type="file" name="main_img" accept=".jpeg, .jpg, .png" style="display: none;" 
            onchange="previewImages(this, 'main-preview')" />
        <div class="preview-container" id="main-preview"></div>
     </div>
    

   <div class="imgs-container">
    <label for="indoor-file-upload" class="custom-file-upload btn-2">
            Indoor Images
        </label>
        <input id="indoor-file-upload" type="file" name="indoor_img" accept=".jpeg, .jpg, .png" style="display: none;" 
            multiple onchange="previewImages(this, 'indoor-preview')" />
        <div class="preview-container" id="indoor-preview"></div>
   </div>
    

    <div class="imgs-container">
        <label for="outdoor-file-upload" class="custom-file-upload btn-2">
            Outdoor Images 
        </label>
        <input id="outdoor-file-upload" type="file" name="outdoor_img" accept=".jpeg, .jpg, .png" style="display: none;" 
            multiple onchange="previewImages(this, 'outdoor-preview')" />
        <div class="preview-container" id="outdoor-preview"></div>
    </div>
    
</div>

    
    <?php 
    if (!empty($errorMessage)) {
        echo "<div style='color:red;'>$errorMessage</div>";
    } 

    if (!empty($successMessage)) {  
       echo "<div style='color:green;'>$successMessage</div>";
    }
?>
    

    <div class="row-space-around">
        <button class="btn-1" type="submit">Submit Form</button>
        <button class="btn-2" type="reset" onclick="clearForm()"> Clear</button>
    </div>
    
</form>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload House</title>
    <link rel="stylesheet" type="text/css" href="upload_house.css">
    <script src="../../global.js"></script>
    <script src="upload-house.js"></script>
    <script src="upload-imgs.js"></script>
    
</head>
<body>

<!-- HEADER -->
    <?php include '../../header/admin-header.php'; ?>    

<main class="component-wrapper">
<h1>New offering</h1>

<form class="column-center" onsubmit="uploadHouse(event)"  id="upload-form">
    <!-- input Fields -->
    <div class="form-row">
        <input required minlength="4" maxlength="15" placeholder="name of your house" type="text" name="name" id="name">
        <input required minlength="2" maxlength="10" placeholder="price per day $" type="text" name="price_per_day" id="price-per-day" pattern="^\d+(\.\d{1,2})?$" >
        <input required minlength="4" maxlength="15" placeholder="country" type="text" name="country" id="country">
    </div>

    <div class="form-row">
        <input required minlength="4" maxlength="15" placeholder="street" type="text" name="street" id="street">
        <input required minlength="1" maxlength="3" placeholder="house number" type="text" name="house_number" id="house-number">
        <input required minlength="5" maxlength="5" placeholder="postal code" type="text" name="postal_code" id="postal-code" pattern="^\d+$" >
    </div>




    
<!-- checkboxes -->
 <div class="checkbox-row">
    <div class="checkbox-container">
            <h3>select activities (min. 3)</h3>
            <div class="checkboxes d-f-ai-c " id="activities"></div>
    </div>

        <div class="checkbox-container">
            <h3>select facilities (min. 3)</h3>
            <div class="d-f-ai-c" id="facilities"></div>
        </div>
</div>
    

<!-- IMG UPLOADS -->
    <div style="margin-top:20px;" class="form-row">
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

<!-- Tags -->
<div class="center-column full-width tag-wrapper">
    <div class="tag-component">
        <div class="tag-input-container">
            <input id="search-input" placeholder="search tags" class="tag-search" onkeyup="searchTags()" type="text">
            <div id="tag-dropdown" class="dropdown"></div>
        </div>
        <div id="tag-container" class="tag-container"></div>
    </div>
</div>

    <div class="row-space-around">
        <button class="btn-1" type="submit">Submit Form</button>
        <button class="btn-2" type="reset" onclick="clearForm()"> Clear</button>
    </div>
    
</form>

    <dialog class="feedback-dialog" id="feedback-dialog">
        <div id="feedback-container" class="feedback-container"></div>
    </dialog>

</main>

</body>
</html>

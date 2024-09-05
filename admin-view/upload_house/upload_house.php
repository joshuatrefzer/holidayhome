<?php 

require "../../db_connection.php";

$name = "";
$errorMessage = "";
$successMessage = "";

// Check if the form is submitted
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
    
</head>
<body>

<!-- HEADER -->
    <?php include '../../header/header.php'; ?>    


<h1>New offering</h1>

<?php 
    if (!empty($errorMessage)) {
        echo "<h3 style='color:red;'>$errorMessage</h3>";
    } 

    if (!empty($successMessage)) {  
       echo "<div style='color:green;'>$successMessage</div>";
    }
?>

<form class="column-center" method="post">
    <div class="form-row">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    </div>

    <div class="form-row">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    </div>

    <div class="form-row">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input placeholder="name of your house" type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    </div>

    <div class="row-space-around">
        <button class="btn-1" type="submit">Submit Form</button>
        <button class="btn-2" type="reset">Clear</button>
    </div>
    
</form>
</body>
</html>

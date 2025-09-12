<?php
session_start();
include("../includes/config.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = fopen($_FILES['file']['tmp_name'], "r");
    while(($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        $name = $conn->real_escape_string($data[0]);
        $voter_id = $conn->real_escape_string($data[1]);
        $conn->query("INSERT IGNORE INTO voters(voter_id,name) VALUES('$voter_id','$name')");
    }
    fclose($file);
    echo "Voters uploaded successfully.";
}
?>
<!DOCTYPE html><html><head><title>Upload CSV</title></head><body>
<h2>Upload Voters CSV</h2>
<form method="post" enctype="multipart/form-data">
<input type="file" name="file" required>
<input type="submit" value="Upload">
</form></body></html>
<?php
$servername = "localhost"; // Change as needed
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "lms_db"; // Change as needed

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $title = $_POST["title"];
    $pdfName = $zipName = $videoFolderName = "";

    // Handle PDF Upload
    if ($_FILES["pdf"]["error"] == 0) {
        $pdfName = basename($_FILES["pdf"]["name"]);
        move_uploaded_file($_FILES["pdf"]["tmp_name"], "pdfs/" . $pdfName);
    }

    // Handle Video Folder Upload (Detect Folder Name)
    if (!empty($_FILES["video"]["name"][0])) {
        $videoFolderName = pathinfo($_FILES["video"]["name"][0], PATHINFO_DIRNAME);
        foreach ($_FILES["video"]["name"] as $key => $videoName) {
            move_uploaded_file($_FILES["video"]["tmp_name"][$key], "videos/" . $videoName);
        }
    }

    // Handle ZIP Upload
    if ($_FILES["zip"]["error"] == 0) {
        $zipName = basename($_FILES["zip"]["name"]);
        move_uploaded_file($_FILES["zip"]["tmp_name"], "zips/" . $zipName);
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO courses (title, pdf_name, video_folder_name, zip_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $pdfName, $videoFolderName, $zipName);
    
    if ($stmt->execute()) {
        echo "Files uploaded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>

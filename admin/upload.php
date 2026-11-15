<?php
// upload.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDir = "uploads/";
    // create folder if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($_FILES["audioFile"]["name"]);

    // check file type (only mp3/wav allowed)
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if ($fileType != "mp3" && $fileType != "wav") {
        echo "Only MP3 or WAV files are allowed.";
        exit;
    }

    if (move_uploaded_file($_FILES["audioFile"]["tmp_name"], $targetFile)) {
        echo "File uploaded successfully: " . $targetFile;

        // 👇 Example: Call Python Demucs script after upload
        $command = "python -m demucs --two-stems=vocals -n htdemucs " . escapeshellarg($targetFile);
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
    } else {
        echo "Error uploading file.";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>

<head>
    <title>Upload Song</title>
</head>

<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label>Select audio file (MP3/WAV):</label>
        <input type="file" name="audioFile" required>
        <input type="submit" value="Upload & Separate">
    </form>
</body>

</html>
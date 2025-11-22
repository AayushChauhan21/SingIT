<?php
// FILE: edit_languages.php
include('demo.php');
include('hhh.php');
include("connection.php");
require 'vendor/autoload.php';

// Define the fallback image path
$fallback_image = 'favicon_1.png';

// Fetch the Language ID from the URL
$lid = $_GET['lid'] ?? 0;
if (!$lid) {
    echo "<div class='alert alert-danger'>Invalid Language ID.</div>";
    include('fff.php');
    exit;
}

// Fetch language data
$qry = mysqli_query($con, "SELECT * FROM language WHERE lid=$lid");
$row = mysqli_fetch_array($qry);

// Check if language exists
if (!$row) {
    echo "<div class='alert alert-danger'>Language not found.</div>";
    include('fff.php');
    exit;
}

$name = $row['name'];

// Check for and set image fallback 🖼️
$image_url = $row['image'] ?? '';
$image = (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) ? $fallback_image : $image_url;

// Initialize error variables (Note: these would normally be populated by the session after redirection from editLanguage.php)
// Assuming session variables exist for language editing errors
// session_start();
$nameError = $_SESSION['langNameError'] ?? ""; // Changed variable name to reflect Language
$photoError = $_SESSION['langPhotoError'] ?? ""; // Changed variable name to reflect Language

// Clear temporary session errors after displaying them
if (isset($_SESSION['langNameError']))
    unset($_SESSION['langNameError']);
if (isset($_SESSION['langPhotoError']))
    unset($_SESSION['langPhotoError']);
// Ensure session is started if using session variables

// PHP POST processing is handled by the API: editLanguage.php
?>

<style>
    form {
        background-color: white;
        border-radius: 20px;
    }

    h1 {
        border-radius: 10px;
    }

    .input {
        border-radius: 10px;
        height: 40px;
        color: black;
    }

    #name {
        /* border-color: #edeef3; */
        /* background-color: white; */
    }

    #custom-button {
        padding: 10px;
        color: white;
        /* background-color: #8371fd; */
        background: linear-gradient(135deg, #6259ca, #ff6ec4);
        border: none;
        border-radius: 10px;
        cursor: pointer;

    }

    #custom-button:hover {
        background-color: #9080f4;
    }

    #custom-text {
        margin-left: 10px;
        font-family: sans-serif;
        color: #aaa;
    }

    .btn-lg {
        padding: 0rem 3rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }

    .error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Update Language</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update Language</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form id="form1" enctype="multipart/form-data" method="post"
        action="http://localhost/SIngIT/flutter_crud/editLanguage.php"
        class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">
        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Language Form</strong>
        </h1>
        <br />

        <input type="hidden" name="lid" value="<?= htmlspecialchars($lid) ?>">

        <h5>Enter Language Name:</h5>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>"
            placeholder="Enter Language Name..." class="form-control input">
        <span class="error" id="nameError"><?= $nameError ?></span>
        <br>

        <h5>Upload Language Image:</h5>

        <input type="file" id="image" name="image" hidden="hidden" />
        <button type="button" id="custom-button">CHOOSE A FILE</button>
        <span id="custom-text">No file chosen, yet.</span>
        <span class="error" id="photoError"><?= $photoError ?></span>

        <img src="<?= $image ?>" class="rounded-10 mt-3" height="70" width="100" alt="Current Language Image">

        <br>
        <br>
        <center>
            <input type="submit" value="Update" name="update" id="update"
                class="btn btn-outline-primary btn-lg input">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input"> -->
        </center>
    </form>
</div>

<script>
    const realFileBtn = document.getElementById("image");
    const customBtn = document.getElementById("custom-button");
    const customTxt = document.getElementById("custom-text");
    const fallbackImage = 'favicon_1.png';

    customBtn.addEventListener("click", function () {
        realFileBtn.click();
    });

    realFileBtn.addEventListener("change", function () {
        if (realFileBtn.value) {
            customTxt.innerHTML = realFileBtn.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            customTxt.innerHTML = "No file chosen, yet.";
        }
    });

    document.getElementById("form1").addEventListener("submit", function (e) {

        let nameField = document.getElementById("name");
        let photoField = document.getElementById("image");
        let nameError = document.getElementById("nameError");
        let photoError = document.getElementById("photoError");
        // Update selector to match the new image alt text
        let currentImageSrc = document.querySelector('img[alt="Current Language Image"]').getAttribute('src');

        let valid = true;

        nameError.textContent = "";
        photoError.textContent = "";

        if (e.submitter && e.submitter.name === "update") {
            // 1. Name is REQUIRED
            if (nameField.value.trim() === "") {
                nameError.textContent = "⚠️ Language Name is required.";
                valid = false;
            }

            // 2. Image: Required ONLY IF fallback is currently shown, OR validate format IF selected
            if (currentImageSrc.endsWith(fallbackImage) && !photoField.value) {
                photoError.textContent = "⚠️ Language Image is required for the initial entry.";
                valid = false;
            } else if (photoField.value) {
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const fileName = photoField.value.toLowerCase();
                const fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    photoError.textContent = "⚠️ Only JPG, JPEG, PNG, or WEBP formats are allowed.";
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        } else if (e.submitter && e.submitter.name === "view") {
            // Handle "View" button (Redirects to view_language.php)
            e.preventDefault();
            window.location.href = 'view_language.php';
        }
    });
</script>


<?php include('fff.php'); ?>
<?php
// FILE: edit_genres.php
include('demo.php');
include('hhh.php');
include("connection.php");
require 'vendor/autoload.php';

// Define the fallback image path
$fallback_image = 'favicon_1.png';

// Fetch the Genre ID from the URL
$gid = $_GET['gid'] ?? 0;
if (!$gid) {
    echo "<div class='alert alert-danger'>Invalid Genre ID.</div>";
    include('fff.php');
    exit;
}

// Fetch genre data
$qry = mysqli_query($con, "SELECT * FROM genre WHERE gid=$gid");
$row = mysqli_fetch_array($qry);

// Check if genre exists
if (!$row) {
    echo "<div class='alert alert-danger'>Genre not found.</div>";
    include('fff.php');
    exit;
}

$name = $row['name'];

// Check for and set image fallback 🖼️
$image_url = $row['image'] ?? '';
$image = (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) ? $fallback_image : $image_url;

// Initialize error variables (Note: these would normally be populated by the session after redirection from editGenre.php)
$nameError = $_SESSION['nameError'] ?? "";
$photoError = $_SESSION['photoError'] ?? "";

// Clear temporary session errors after displaying them
if (isset($_SESSION['nameError']))
    unset($_SESSION['nameError']);
if (isset($_SESSION['photoError']))
    unset($_SESSION['photoError']);

// PHP POST processing is handled by the API: editGenre.php
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
        <h2 class="main-content-title tx-24 mg-b-5">Update Genre</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update Genre</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form id="form1" enctype="multipart/form-data" method="post"
        action="http://localhost/SIngIT/flutter_crud/editGenre.php"
        class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">
        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Genre Form</strong>
        </h1>
        <br />

        <input type="hidden" name="gid" value="<?= htmlspecialchars($gid) ?>">

        <h5>Enter Genre Name:</h5>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>"
            placeholder="Enter Genre Name..." class="form-control input">
        <span class="error" id="nameError"><?= $nameError ?></span>
        <br>

        <h5>Upload Genre Image:</h5>

        <input type="file" id="image" name="image" hidden="hidden" />
        <button type="button" id="custom-button">CHOOSE A FILE</button>
        <span id="custom-text">No file chosen, yet.</span>
        <span class="error" id="photoError"><?= $photoError ?></span>

        <img src="<?= $image ?>" class="rounded-10 mt-3" height="100" width="100" alt="Current Genre Image">

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
        let currentImageSrc = document.querySelector('img[alt="Current Genre Image"]').getAttribute('src');

        let valid = true;

        nameError.textContent = "";
        photoError.textContent = "";

        if (e.submitter && e.submitter.name === "update") {
            // 1. Name is REQUIRED
            if (nameField.value.trim() === "") {
                nameError.textContent = "⚠️ Name is required.";
                valid = false;
            }

            // 2. Image: Required ONLY IF fallback is currently shown, OR validate format IF selected
            if (currentImageSrc.endsWith(fallbackImage) && !photoField.value) {
                photoError.textContent = "⚠️ Genre Image is required for the initial entry.";
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
            // Handle "View" button
            e.preventDefault();
            window.location.href = 'view_genres.php';
        }
    });
</script>


<?php include('fff.php'); ?>
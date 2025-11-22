<?php
include('demo.php');
include('hhh.php');
include("connection.php");

// Define the fallback image path
$fallback_image = 'favicon_1.png';

// Fetch the Artist ID from the URL
$arid = $_GET['arid'] ?? 0;
if (!$arid) {
    echo "<div class='alert alert-danger'>Invalid Artist ID.</div>";
    include('fff.php');
    exit;
}

// Fetch artist data, including the new 'description' and 'image_transparent' fields
$qry = mysqli_query($con, "SELECT * FROM artist WHERE arid=$arid");
$row = mysqli_fetch_array($qry);

// Check if artist exists
if (!$row) {
    echo "<div class='alert alert-danger'>Artist not found.</div>";
    include('fff.php');
    exit;
}

$name = $row['name'];
$description = $row['description'] ?? '';

// Check for and set image fallbacks 🖼️
$main_image_url = $row['photo'] ?? '';
// $image holds the actual URL or the fallback path
$image = (empty($main_image_url) || !filter_var($main_image_url, FILTER_VALIDATE_URL)) ? $fallback_image : $main_image_url;

$transparent_image_url = $row['image_transparent'] ?? '';
// $image_transparent holds the actual URL or the fallback path
$image_transparent = (empty($transparent_image_url) || !filter_var($transparent_image_url, FILTER_VALIDATE_URL)) ? $fallback_image : $transparent_image_url;

$nameError = $photoError = $descriptionError = $imageTranspError = "";

// PHP POST processing and Cloudinary logic removed, now handled by editArtist.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update artist</title>
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

        /* Styling for the new second file input button/text */
        #custom-button_v,
        #custom-button {
            padding: 10px;
            color: white;
            background: linear-gradient(135deg, #6259ca, #ff6ec4);
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        #custom-button_v:hover,
        #custom-button:hover {
            background-color: #9080f4;
        }

        #custom-text,
        #custom-text_v {
            margin-left: 10px;
            font-family: sans-serif;
            color: #aaa;
        }

        /* End of new file input styling */

        .btn-lg {
            padding: 0rem 3rem;
            font-size: 0.875rem;
            border-radius: 10px;
        }

        .setimg {
            display: flex;
            flex-direction: column;
            /* Stacks image and controls vertically */
            align-items: flex-start;
            /* Aligns content to the left */
            justify-content: flex-start;
            /* Aligns content to the top */
        }

        .tb {
            padding-left: 0;
            /* Remove left padding */
            margin-top: 10px;
            /* Add space between image and controls */
        }

        .setimg .img {
            display: block;
            width: 100%;
        }


        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div>
            <h2 class="main-content-title tx-24 mg-b-5">Update artist</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update artist</li>
            </ol>
        </div>
    </div>

    <div class="col-md-6 m-auto d-block">
        <form id="form1" enctype="multipart/form-data" method="post"
            action="http://localhost/SIngIT/flutter_crud/editArtist.php"
            class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">
            <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
                <strong>Artist Form</strong>
            </h1>
            <br />

            <input type="hidden" name="arid" value="<?= htmlspecialchars($arid) ?>">

            <h5>Enter Artist Name:</h5>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>"
                placeholder="Enter artist Name..." class="form-control input">
            <span class="error" id="nameError"></span>
            <br>

            <h5>Enter Artist Description:</h5>
            <textarea name="description" id="description" placeholder="Enter Artist Biography or Description..."
                class="form-control" rows="4"><?= htmlspecialchars($description) ?></textarea>
            <span id="descriptionError" class="error"></span>
            <br />

            <h5>Upload Artist Image:</h5>

            <input type="file" id="image" name="image" hidden="hidden" />
            <button type="button" id="custom-button">CHOOSE A FILE</button>
            <span id="custom-text">No file chosen, yet.</span>
            <span class="error" id="photoError"></span>

            <img src="<?= htmlspecialchars($image) ?>" style='height: 100px; width: 85px; object-fit: cover;'
                class='rounded' alt="Current Artist Image">

            <br>
            <br>

            <h5>Upload Artist Image (transparent):</h5>

            <input type="file" id="image_transparent" name="image_transparent" accept="image/png" hidden="hidden" />
            <button type="button" id="custom-button_v">CHOOSE A FILE</button>
            <span id="custom-text_v">No file chosen, yet.</span>
            <span class="error" id="imageTranspError"></span>

            <img src="<?= htmlspecialchars($image_transparent) ?>"
                style='height: 100px; width: 85px; object-fit: cover;' class='rounded' alt="Current Transparent Image">

            <br>

            <center>
                <input type="submit" value="Update" name="update" id="update"
                    class="btn btn-outline-primary btn-lg input">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!-- <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input"> -->
            </center>
        </form>
    </div>

    <script>
        // --- Setup Custom File Inputs ---

        // 1. Main Artist Image (id="image")
        const realFileBtn_Main = document.getElementById("image");
        const customBtn_Main = document.getElementById("custom-button");
        const customTxt_Main = document.getElementById("custom-text");

        customBtn_Main.addEventListener("click", function () {
            realFileBtn_Main.click();
        });

        realFileBtn_Main.addEventListener("change", function () {
            if (realFileBtn_Main.files.length > 0) {
                customTxt_Main.innerHTML = realFileBtn_Main.files[0].name;
            } else {
                customTxt_Main.innerHTML = "No file chosen, yet.";
            }
        });

        // 2. NEW: Second Artist Image (id="image_transparent")
        const realFileBtn_Transp = document.getElementById("image_transparent");
        const customBtn_Transp = document.getElementById("custom-button_v");
        const customTxt_Transp = document.getElementById("custom-text_v");

        customBtn_Transp.addEventListener("click", function () {
            realFileBtn_Transp.click();
        });

        realFileBtn_Transp.addEventListener("change", function () {
            if (realFileBtn_Transp.files.length > 0) {
                customTxt_Transp.innerHTML = realFileBtn_Transp.files[0].name;
            } else {
                customTxt_Transp.innerHTML = "No file chosen, yet.";
            }
        });


        // --- Form Submission Validation (CORRECTED FOR IMAGE OPTIONALITY) ---
        document.getElementById("form1").addEventListener("submit", function (e) {

            // Only validate on 'Update' button click
            if (e.submitter && e.submitter.name === "update") {
                let nameField = document.getElementById("name");
                let descriptionField = document.getElementById("description");
                let photoField = document.getElementById("image");
                let transpPhotoField = document.getElementById("image_transparent");

                // Check for fallback image to determine if an existing image link is present
                let currentMainImageSrc = document.querySelector('img[alt="Current Artist Image"]').getAttribute(
                    'src');
                let currentTranspImageSrc = document.querySelector('img[alt="Current Transparent Image"]')
                    .getAttribute('src');
                const fallbackImage = 'favicon_1.png'; // Must match the PHP definition

                let nameError = document.getElementById("nameError");
                let descriptionError = document.getElementById("descriptionError");
                let photoError = document.getElementById("photoError");
                let transpPhotoError = document.getElementById("imageTranspError");

                let valid = true;

                // Clear previous errors
                nameError.textContent = "";
                descriptionError.textContent = "";
                photoError.textContent = "";
                transpPhotoError.textContent = "";

                // 1. Name Validation (REQUIRED)
                if (nameField.value.trim() === "") {
                    nameError.textContent = "⚠️ Artist Name is required.";
                    valid = false;
                }

                // 2. Description Validation (REQUIRED)
                if (descriptionField.value.trim() === "") {
                    descriptionError.textContent = "⚠️ Description is required.";
                    valid = false;
                }

                // --- IMAGE LOGIC: Required ONLY IF the current image is the fallback. ---

                // 3. Main Photo Validation
                if (currentMainImageSrc.endsWith(fallbackImage) && !photoField.value) {
                    // Scenario: Database is empty (fallback is shown) AND user didn't select a new file -> REQUIRED
                    photoError.textContent = "⚠️ The Main Artist Image is required.";
                    valid = false;
                } else if (photoField.value) {
                    // Scenario: User selected a new file (must validate format)
                    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                    const fileName = photoField.value.toLowerCase();
                    const fileExtension = fileName.split('.').pop();

                    if (!allowedExtensions.includes(fileExtension)) {
                        photoError.textContent = "⚠️ Invalid format. Only JPG, JPEG, PNG, or WEBP allowed.";
                        valid = false;
                    }
                }

                // 4. Transparent Photo Validation
                if (currentTranspImageSrc.endsWith(fallbackImage) && !transpPhotoField.value) {
                    // Scenario: Database is empty (fallback is shown) AND user didn't select a new file -> REQUIRED
                    transpPhotoError.textContent = "⚠️ The Transparent Artist Image is required.";
                    valid = false;
                } else if (transpPhotoField.value) {
                    // Scenario: User selected a new file (must validate PNG format)
                    const allowedExtensions = ['png'];
                    const fileName = transpPhotoField.value.toLowerCase();
                    const fileExtension = fileName.split('.').pop();

                    if (!allowedExtensions.includes(fileExtension)) {
                        transpPhotoError.textContent =
                            "⚠️ Invalid format. Only PNG allowed for the transparent image.";
                        valid = false;
                    }
                }

                if (!valid) {
                    e.preventDefault(); // Stop form submission
                }
            } else if (e.submitter && e.submitter.name === "view") {
                // Handle "View" button submission (Client-side redirect)
                e.preventDefault();
                window.location.href = 'view_artist.php';
            }
        });
    </script>
</body>

</html>
<?php include('fff.php'); ?>
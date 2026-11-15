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
$image = (empty($main_image_url) || !filter_var($main_image_url, FILTER_VALIDATE_URL)) ? $fallback_image : $main_image_url;

$transparent_image_url = $row['image_transparent'] ?? '';
$image_transparent = (empty($transparent_image_url) || !filter_var($transparent_image_url, FILTER_VALIDATE_URL)) ? $fallback_image : $transparent_image_url;

$nameError = $photoError = $descriptionError = $imageTranspError = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Artist</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* --- PREMIUM DARK ULTRA CONTEMPORARY OTT MUSIC STREAMING UI WRAPPER --- */
    :root {
        --body-bg: #0b0f19;
        --form-surface: #131a26;
        --input-bg: #1c2536;
        --input-border: rgba(255, 255, 255, 0.05);
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --accent-neon: #AA62C7;
        --accent-neon-glow: #6259ca;
        --danger-neon: #ef4444;
        --radius-music: 16px;
        --transition-music: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .music-form-panel {
        border: 1px solid rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-music);
        padding: 2.5rem;
    }

    .music-heading {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }

    .field-title {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .music-input {
        border: 1px solid var(--input-border) !important;
        border-radius: 12px !important;
        color: var(--text-primary) !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.95rem !important;
        transition: var(--transition-music) !important;
        box-shadow: none !important;
        height: auto !important;
    }

    .music-input:focus {
        border-color: var(--accent-neon) !important;
    }

    .music-input::placeholder {
        color: rgba(255, 255, 255, 0.25) !important;
    }

    /* --- VERTICAL PORTRAIT RATIO (3:4) FOR ARTIST IMAGES --- */
    .music-media-uploader-frame {
        border: 2px dashed rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-music);
        aspect-ratio: 3 / 4;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1rem;
        position: relative;
        cursor: pointer;
        overflow: hidden;
        transition: var(--transition-music);
    }

    .music-media-uploader-frame:hover {
        border-color: var(--accent-neon);
        background-color: rgba(255, 255, 255, 0.01);
    }

    .uploader-placeholder-text {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
        pointer-events: none;
        z-index: 1;
    }

    .uploader-absolute-preview-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 2;
    }

    .btn-music-master-deploy {
        background: linear-gradient(135deg, #6259ca, #ff6ec4) !important;
        color: #fff !important;
        font-weight: 700 !important;
        font-size: 0.95rem !important;
        padding: 0.85rem 3.5rem !important;
        border-radius: 10px !important;
        border: none !important;
        transition: var(--transition-music) !important;
    }

    .btn-music-master-deploy:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(170, 98, 199, 0.4) !important;
    }

    .error {
        color: var(--danger-neon);
        font-size: 0.8rem;
        margin-top: 0.35rem;
        display: none;
        font-weight: 500;
    }

    .breadcrumb-item a {
        color: var(--text-secondary) !important;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: var(--accent-neon) !important;
    }
    </style>
</head>

<body>
    <div class="page-header">
        <div>
            <h2 class="main-content-title tx-24 mg-b-5">Update Artist</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Artist</li>
            </ol>
        </div>
    </div>

    <div class="music-container px-3 col-md-6 m-auto d-block">
        <form id="form1" enctype="multipart/form-data" method="post" action="../flutter_crud/editArtist.php"
            class="mb-4 border bg-white p-5 shadow music-form-panel">

            <div
                class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
                <h1 class="music-heading mb-0">Update Artist</h1>
            </div>

            <input type="hidden" name="arid" value="<?= htmlspecialchars($arid) ?>">

            <div class="row g-4">
                <!-- Artist Name -->
                <div class="col-12">
                    <div class="field-title">Enter Artist Name</div>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>"
                        placeholder="Enter artist Name..." class="form-control music-input">
                    <span class="error" id="nameError"></span>
                </div>

                <!-- Artist Description -->
                <div class="col-12">
                    <div class="field-title">Enter Artist Description</div>
                    <textarea name="description" id="description" placeholder="Enter Artist Biography or Description..."
                        class="form-control music-input" rows="4"><?= htmlspecialchars($description) ?></textarea>
                    <span id="descriptionError" class="error"></span>
                </div>

                <!-- Main Artist Image Uploader (Vertical 3:4) -->
                <div class="col-md-6">
                    <div class="field-title">Upload Artist Image</div>
                    <div class="music-media-uploader-frame" id="trigger_image">
                        <img id="photoPreview" src="<?= htmlspecialchars($image) ?>"
                            class="uploader-absolute-preview-img" alt="Current Artist Image">
                        <span class="uploader-placeholder-text" id="photoPlaceholderText" style="display: none;">
                            <i class="uil uil-image d-block fs-2 mb-2"></i>
                            Upload vertical image
                        </span>
                    </div>
                    <input type="file" id="image" name="image" accept="image/*" class="d-none" />
                    <span class="error" id="photoError"></span>
                </div>

                <!-- Transparent Image Uploader (Vertical 3:4) -->
                <div class="col-md-6">
                    <div class="field-title">Transparent Image (PNG)</div>
                    <div class="music-media-uploader-frame" id="trigger_image_transparent">
                        <img id="transpPreview" src="<?= htmlspecialchars($image_transparent) ?>"
                            class="uploader-absolute-preview-img" alt="Current Transparent Image">
                        <span class="uploader-placeholder-text" id="transpPlaceholderText" style="display: none;">
                            <i class="uil uil-image d-block fs-2 mb-2"></i>
                            Upload vertical PNG
                        </span>
                    </div>
                    <input type="file" id="image_transparent" name="image_transparent" accept="image/png"
                        class="d-none" />
                    <span class="error" id="imageTranspError"></span>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-center mt-4 pt-2">
                    <input type="submit" value="Update" name="update" id="update"
                        class="btn btn-music-master-deploy shadow w-100">
                </div>
            </div>
        </form>
    </div>

    <script>
    // --- Custom File Upload & Live Preview Logic ---
    function setupImageUpload(triggerId, inputId, previewId, placeholderId, errorId) {
        const frame = document.getElementById(triggerId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        const errorSpan = document.getElementById(errorId);

        frame.addEventListener('click', () => input.click());

        input.addEventListener('change', () => {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    errorSpan.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    }

    setupImageUpload('trigger_image', 'image', 'photoPreview', 'photoPlaceholderText', 'photoError');
    setupImageUpload('trigger_image_transparent', 'image_transparent', 'transpPreview', 'transpPlaceholderText',
        'imageTranspError');

    // --- Live Input Validation Clearing ---
    document.getElementById('name').addEventListener('input', function() {
        if (this.value.trim() !== "") document.getElementById('nameError').style.display = 'none';
    });

    document.getElementById('description').addEventListener('input', function() {
        if (this.value.trim() !== "") document.getElementById('descriptionError').style.display = 'none';
    });

    // --- Form Submission Validation ---
    document.getElementById("form1").addEventListener("submit", function(e) {
        if (e.submitter && e.submitter.name === "update") {
            let nameField = document.getElementById("name");
            let descriptionField = document.getElementById("description");
            let photoField = document.getElementById("image");
            let transpPhotoField = document.getElementById("image_transparent");

            let currentMainImageSrc = document.getElementById('photoPreview').getAttribute('src');
            let currentTranspImageSrc = document.getElementById('transpPreview').getAttribute('src');
            const fallbackImage = 'favicon_1.png';

            let nameError = document.getElementById("nameError");
            let descriptionError = document.getElementById("descriptionError");
            let photoError = document.getElementById("photoError");
            let transpPhotoError = document.getElementById("imageTranspError");

            let valid = true;

            // Reset errors
            nameError.style.display = "none";
            descriptionError.style.display = "none";
            photoError.style.display = "none";
            transpPhotoError.style.display = "none";

            nameError.textContent = "";
            descriptionError.textContent = "";
            photoError.textContent = "";
            transpPhotoError.textContent = "";

            // 1. Name Validation
            if (nameField.value.trim() === "") {
                nameError.textContent = "⚠️ Artist Name is required.";
                nameError.style.display = "block";
                valid = false;
            }

            // 2. Description Validation
            if (descriptionField.value.trim() === "") {
                descriptionError.textContent = "⚠️ Description is required.";
                descriptionError.style.display = "block";
                valid = false;
            }

            // 3. Main Photo Validation
            if (currentMainImageSrc.endsWith(fallbackImage) && !photoField.value) {
                photoError.textContent = "⚠️ The Main Artist Image is required.";
                photoError.style.display = "block";
                valid = false;
            } else if (photoField.value) {
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const fileName = photoField.value.toLowerCase();
                const fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    photoError.textContent = "⚠️ Invalid format. Only JPG, JPEG, PNG, or WEBP allowed.";
                    photoError.style.display = "block";
                    valid = false;
                }
            }

            // 4. Transparent Photo Validation
            if (currentTranspImageSrc.endsWith(fallbackImage) && !transpPhotoField.value) {
                transpPhotoError.textContent = "⚠️ The Transparent Artist Image is required.";
                transpPhotoError.style.display = "block";
                valid = false;
            } else if (transpPhotoField.value) {
                const allowedExtensions = ['png'];
                const fileName = transpPhotoField.value.toLowerCase();
                const fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    transpPhotoError.textContent =
                        "⚠️ Invalid format. Only PNG allowed for the transparent image.";
                    transpPhotoError.style.display = "block";
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        }
    });
    </script>
</body>

</html>
<?php include('fff.php'); ?>
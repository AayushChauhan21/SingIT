<?php
include('demo.php');
include('hhh.php');
include('connection.php');
// require 'vendor/autoload.php';

// use Cloudinary\Cloudinary;

// $cloudinary = new Cloudinary([
//     'cloud' => [
//         'cloud_name' => 'do3hihcwa',
//         'api_key' => '684953537186227',
//         'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
//     ]
// ]);

// if (isset($_POST["insert"])) {
//     $name = trim($_POST["name"]);
//     $photoTmp = $_FILES["photo"]["tmp_name"];

//     if (!$name && !$photoTmp) {
//         echo "<script>alert('Artist Name & Image Is Required');</script>";
//     } elseif (!$name) {
//         echo "<script>alert('Name Is Required');</script>";
//     } elseif (!$photoTmp) {
//         echo "<script>alert('Image Is Required');</script>";
//     } else {
//         try {
//             $uploadResult = $cloudinary->uploadApi()->upload($photoTmp);
//             $imageUrl = $uploadResult['secure_url'];

//             $qry = "INSERT INTO artist (name, photo) VALUES ('$name', '$imageUrl')";
//             mysqli_query($con, $qry);

//             echo "<script>alert('✅ Artist Inserted Successfully');</script>";
//             echo "<script>window.location.href='view_artist.php';</script>";
//         } catch (Exception $e) {
//             echo "<script>alert('❌ Upload failed: " . $e->getMessage() . "');</script>";
//         }
//     }
// }

// if (isset($_POST["view"])) {
//     echo "<script>window.location.href='view_artist.php';</script>";
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Artist</title>
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
            <h2 class="main-content-title tx-24 mg-b-5">Add Artist</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Artist</li>
            </ol>
        </div>
    </div>

    <div class="music-container px-3 col-md-6 m-auto d-block">
        <form action="../flutter_crud/addArtists.php" method="post" enctype="multipart/form-data" id="form1"
            class="mb-4 border bg-white p-5 shadow music-form-panel">

            <div
                class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
                <h1 class="music-heading mb-0">Add Artist</h1>
            </div>

            <div class="row g-4">

                <!-- Artist Name -->
                <div class="col-12">
                    <div class="field-title">Enter Artist Name</div>
                    <input type="text" name="name" id="name" placeholder="Enter Artist Name..."
                        class="form-control music-input">
                    <span class="error" id="nameError"></span>
                </div>

                <!-- Artist Description / Biography -->
                <div class="col-12">
                    <div class="field-title">Enter Artist Description</div>
                    <textarea name="description" id="description" placeholder="Enter Artist Biography or Description..."
                        class="form-control music-input" rows="4"></textarea>
                    <span class="error" id="descriptionError"></span>
                </div>

                <!-- Artist Main Image Uploader (Vertical 3:4) -->
                <div class="col-md-6">
                    <div class="field-title">Upload Artist Image</div>
                    <div class="music-media-uploader-frame" id="trigger_photo">
                        <img id="photoPreview" src="" class="uploader-absolute-preview-img" style="display: none;"
                            alt="Artist Image Preview">
                        <span class="uploader-placeholder-text" id="photoPlaceholderText">
                            <i class="uil uil-image d-block fs-2 mb-2"></i>
                            Upload vertical image
                        </span>
                    </div>
                    <input type="file" id="photo" name="photo" accept="image/*" class="d-none" />
                    <span class="error" id="photoError"></span>
                </div>

                <!-- Transparent Image Uploader (Vertical 3:4) -->
                <div class="col-md-6">
                    <div class="field-title">Transparent Image (PNG)</div>
                    <div class="music-media-uploader-frame" id="trigger_image">
                        <img id="imagePreview" src="" class="uploader-absolute-preview-img" style="display: none;"
                            alt="Transparent Image Preview">
                        <span class="uploader-placeholder-text" id="imagePlaceholderText">
                            <i class="uil uil-image d-block fs-2 mb-2"></i>
                            Upload vertical PNG
                        </span>
                    </div>
                    <input type="file" id="image" name="image" accept="image/png" class="d-none" />
                    <span class="error" id="imageError"></span>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-center mt-4 pt-2">
                    <input type="submit" value="Insert" name="insert" id="insert"
                        class="btn btn-music-master-deploy shadow w-100">
                </div>

            </div>

        </form>
    </div>

    <script>
    // --- Setup Custom Click-to-Upload Box Logic for Both Images ---
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

    setupImageUpload('trigger_photo', 'photo', 'photoPreview', 'photoPlaceholderText', 'photoError');
    setupImageUpload('trigger_image', 'image', 'imagePreview', 'imagePlaceholderText', 'imageError');

    // --- Live Input Validation clearing ---
    document.getElementById('name').addEventListener('input', function() {
        if (this.value.trim() !== "") document.getElementById('nameError').style.display = 'none';
    });

    document.getElementById('description').addEventListener('input', function() {
        if (this.value.trim() !== "") document.getElementById('descriptionError').style.display = 'none';
    });

    // --- Form Submit Validation ---
    document.getElementById("form1").addEventListener("submit", function(e) {
        const nameField = document.getElementById("name");
        const descriptionField = document.getElementById("description");
        const photoInput = document.getElementById("photo");
        const imageInput = document.getElementById("image");

        const nameError = document.getElementById("nameError");
        const descriptionError = document.getElementById("descriptionError");
        const photoError = document.getElementById("photoError");
        const imageError = document.getElementById("imageError");

        let valid = true;

        // Reset errors
        nameError.style.display = "none";
        descriptionError.style.display = "none";
        photoError.style.display = "none";
        imageError.style.display = "none";

        if (e.submitter && e.submitter.name === "insert") {
            // Name Check
            if (nameField.value.trim() === "") {
                nameError.textContent = "⚠️ Artist name is required.";
                nameError.style.display = "block";
                valid = false;
            }

            // Description Check
            if (descriptionField.value.trim() === "") {
                descriptionError.textContent = "⚠️ Artist description is required.";
                descriptionError.style.display = "block";
                valid = false;
            }

            // Main Photo Check
            if (photoInput.files.length === 0) {
                photoError.textContent = "⚠️ Main artist image is required.";
                photoError.style.display = "block";
                valid = false;
            } else {
                const ext = photoInput.value.toLowerCase().split('.').pop();
                if (!['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    photoError.textContent = "⚠️ Only JPG, JPEG, PNG, or WEBP allowed.";
                    photoError.style.display = "block";
                    valid = false;
                }
            }

            // Transparent Image Check (PNG only)
            if (imageInput.files.length === 0) {
                imageError.textContent = "⚠️ Transparent PNG image is required.";
                imageError.style.display = "block";
                valid = false;
            } else {
                const ext = imageInput.value.toLowerCase().split('.').pop();
                if (ext !== 'png') {
                    imageError.textContent = "⚠️ Only PNG format is allowed for transparent image.";
                    imageError.style.display = "block";
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
<?php
include('fff.php');
?>
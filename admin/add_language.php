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
    <title>Document</title>
    <!-- <link rel="stylesheet" href="style_adm.css"> -->
    <!-- BOOTSTRAP CSS -->
    <!-- <link id="style" href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
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
            /* font-size:19px; */
            height: 40px;
            color: black;

        }

        #name {
            /* border-color: #edeef3; */
            /* border-color: gray; */
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

        #custom-button {
            padding: 10px;
            color: white;
            background-color: #8371fd;
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

        .setimg {
            display: flex;
            align-items: center;
            justify-content: center
        }

        .tb {
            /* font-size: 20px; */
            padding-left: 10px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
    </style>

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


        @keyframes ottEntrance {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .music-form-panel {
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-music);
            padding: 2.5rem;
            /* box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5); */
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
            /* background-color: var(--input-bg) !important; */
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
            /* box-shadow: 0 0 15px var(--accent-neon-glow) !important; */
        }

        .music-input::placeholder {
            color: rgba(255, 255, 255, 0.25) !important;
        }

        /* --- PERFECT SQUARE 1:1 RATIO FOR MUSIC ALBUM COVERS --- */
        .music-media-uploader-frame {
            /* background-color: var(--input-bg); */
            border: 2px dashed rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-music);
            aspect-ratio: 1 / 1;
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

        /* --- COMPACT DROPDOWN SELECTIONS MATRIX --- */
        .music-dropdown-trigger {
            /* background-color: var(--input-bg); */
            border: 1px solid var(--input-border);
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-primary);
            transition: var(--transition-music);
        }

        .music-dropdown-trigger:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .custom-dropdown-list-style {
            background-color: #0e0e23;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-top: none;
            max-height: 220px;
            overflow-y: auto;
            border-radius: 0 0 12px 12px;
            padding: 0.75rem;
            display: none;
            position: absolute;
            /* width: 100%; */
            width: 88.5%;
            z-index: 10;
            /* box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5); */
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition-music);
            color: var(--text-silver);
        }

        .checkbox-item:hover {
            background-color: rgba(255, 255, 255, 0.03);
            color: var(--text-white);
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: var(--accent-neon);
            cursor: pointer;
        }

        .dropdown-img {
            margin-right: 12px;
            border-radius: 4px;
            object-fit: cover;
        }

        .artist-img {
            height: 36px;
            width: 28px;
        }

        .genre-lang-img {
            height: 28px;
            width: 36px;
        }

        /* --- STRIP UPLOADER BAR ARCHITECTURE --- */
        .music-strip-bar-uploader {
            /* background-color: var(--input-bg); */
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition-music);
        }

        .music-strip-bar-uploader:hover {
            border-color: rgba(255, 255, 255, 0.12);
        }

        .btn-music-action-trigger {
            background-color: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: var(--text-primary) !important;
            padding: 0.5rem 1.25rem !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: var(--transition-music) !important;
        }

        .btn-music-action-trigger:hover {
            background-color: var(--accent-neon) !important;
            border-color: var(--accent-neon) !important;
            /* box-shadow: 0 0 12px var(--accent-neon-glow) !important; */
        }

        .music-filename-status-string {
            font-size: 0.9rem;
            color: var(--text-secondary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70%;
        }

        .btn-music-master-deploy {
            background: linear-gradient(135deg, #6259ca, #ff6ec4) !important;
            color: var(--text-white) !important;
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


        .btn-music-autofill {
            background-color: transparent !important;
            border: 1px solid var(--accent-neon) !important;
            color: var(--text-primary) !important;
            font-weight: 600 !important;
            padding: 0.65rem 2.5rem !important;
            border-radius: 10px !important;
            transition: var(--transition-music) !important;
        }

        .btn-music-autofill:hover {
            background-color: var(--accent-neon) !important;
            /* box-shadow: 0 0 15px var(--accent-neon-glow) !important; */
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

        sweet-alert p {
            color: #a8afc700;
        }
    </style>
</head>

<body>

    <div class="page-header">
        <div>
            <h2 class="main-content-title tx-24 mg-b-5">Add Language</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Language</li>
            </ol>
        </div>
    </div>

    <div class="music-container px-3 col-md-6 m-auto d-block">
        <form action="../flutter_crud/addGenres.php" method="post" enctype="multipart/form-data" id="form1"
            class="mb-4 font-weight-bold border bg-white p-5 shadow music-form-panel">

            <div
                class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
                <h1 class="music-heading mb-0">Add Language</h1>
            </div>

            <!-- TWO-COLUMN MAIN WORKSPACE FRAME -->
            <div class="row g-4">


                <!-- Track Title Parameter Field -->
                <div class="col-12">
                    <div class="field-title">Enter Language Name</div>
                    <input type="text" name="name" id="name" placeholder="Enter Language Name..."
                        class="form-control music-input">
                    <span class="error" id="nameError"></span>
                </div>

                <!-- Marketing Distribution Poster Node Block (16:9 Landscape Layout) -->
                <div>
                    <div class="field-title">Distribution Poster Layer</div>
                    <div class="music-media-uploader-frame" id="trigger_posterImage" style="aspect-ratio: 16 / 9;">
                        <img id="posterImagePreview" src="" class="uploader-absolute-preview-img" style="display: none;"
                            alt="Poster Graphic Preview" crossorigin="anonymous">
                        <span class="uploader-placeholder-text" id="posterPlaceholderText">
                            <i class="uil uil-image d-block fs-2 mb-2"></i>
                            Upload landscape poster
                        </span>
                    </div>
                    <input type="file" id="posterImage" name="photo" accept="image/*" class="d-none" />
                    <span class="error" id="posterImageError"></span>
                </div>

                <div class="text-center mt-5 pt-2">
                    <input type="submit" value="Insert" name="insert" id="insert"
                        class="btn btn-music-master-deploy shadow w-100">
                </div>


                <!-- FINAL SUBMIT ACTION BLOCK -->

            </div>

        </form>
    </div>


    <script>
        const realFileBtn = document.getElementById("posterImage");
        const customBtn = document.getElementById("custom-button");
        const customTxt = document.getElementById("custom-text");

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
    </script>

    <script>
        // --- 1. Image Preview & Click Logic ---
        const frame = document.getElementById('trigger_posterImage');
        const input = document.getElementById('posterImage');
        const preview = document.getElementById('posterImagePreview');
        const placeholder = document.getElementById('posterPlaceholderText');
        const photoError = document.getElementById("posterImageError");
        const nameError = document.getElementById("nameError");

        // બોક્સ પર ક્લિક થાય ત્યારે ફાઈલ સિલેક્ટર ઓપન કરો
        frame.addEventListener('click', () => input.click());

        // ફાઈલ સિલેક્ટ થાય ત્યારે પ્રિવ્યુ બતાવો
        input.addEventListener('change', () => {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    photoError.style.display = 'none'; // ફોટો સિલેક્ટ કરતા જ એરર ગાયબ
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        // --- 2. Live Input Validation ---
        document.getElementById('name').addEventListener('input', function () {
            if (this.value.trim() !== "") {
                nameError.style.display = 'none'; // નામ લખતા જ એરર ગાયબ
            }
        });

        // --- 3. Form Submit Validation Logic ---
        document.getElementById("form1").addEventListener("submit", function (e) {
            const nameField = document.getElementById("name");

            let valid = true;

            // પહેલાની એરર હાઇડ કરો
            nameError.style.display = "none";
            photoError.style.display = "none";

            if (e.submitter && e.submitter.name === "insert") {

                // Name Check
                if (nameField.value.trim() === "") {
                    nameError.textContent = "⚠️ Genre name is required.";
                    nameError.style.display = "block"; // એરર બતાવો
                    valid = false;
                }

                // Image Check
                if (input.files.length === 0) {
                    photoError.textContent = "⚠️ Upload landscape poster image is required.";
                    photoError.style.display = "block"; // એરર બતાવો
                    valid = false;
                } else {
                    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                    const fileName = input.value.toLowerCase();
                    const fileExtension = fileName.split('.').pop();

                    if (!allowedExtensions.includes(fileExtension)) {
                        photoError.textContent = "⚠️ Only JPG, JPEG, PNG, or WEBP formats are allowed.";
                        photoError.style.display = "block";
                        valid = false;
                    }
                }

                // જો ભૂલ હશે તો ફોર્મ સબમિટ નહિ થાય
                if (!valid) {
                    e.preventDefault();
                }
            }
        });
    </script>

</html>
<?php
include('fff.php');
?>
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

// Initialize error variables
$nameError = $_SESSION['nameError'] ?? "";
$photoError = $_SESSION['photoError'] ?? "";

// Clear temporary session errors after displaying them
if (isset($_SESSION['nameError']))
    unset($_SESSION['nameError']);
if (isset($_SESSION['photoError']))
    unset($_SESSION['photoError']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Genre</title>
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
            background-color: var(--form-surface) !important;
            border: 1px solid var(--input-border) !important;
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
            background-color: transparent !important;
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

        /* --- RECTANGULAR 16:9 RATIO FOR GENRE BANNER --- */
        .music-media-uploader-frame {
            border: 2px dashed rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-music);
            aspect-ratio: 16 / 9;
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
            background-color: rgba(255, 255, 255, 0.01);
        }

        .music-media-uploader-frame:hover {
            border-color: var(--accent-neon);
            background-color: rgba(255, 255, 255, 0.03);
        }

        .uploader-placeholder-text {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
            pointer-events: none;
            z-index: 1;
            text-shadow: 0px 2px 10px rgba(0, 0, 0, 0.8);
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
            <h2 class="main-content-title tx-24 mg-b-5 text-light">Update Genre</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item"><a href="view_genres.php">View Genres</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Genre</li>
            </ol>
        </div>
    </div>


    <div class="music-container px-3 col-md-6 m-auto d-block">
        <!-- 🔴 એક્શન editGenre.php કરી દીધી છે -->
        <form action="../flutter_crud/editGenre.php" method="post" enctype="multipart/form-data" id="form1"
            class="mb-4 font-weight-bold border bg-white p-5 shadow music-form-panel">

            <div
                class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
                <h1 class="music-heading mb-0">Modify Genre Details</h1>
            </div>

            <!-- 🔴 છૂપો ડેટા (GID અને જૂનો ફોટો) પાસ કરવા માટે -->
            <input type="hidden" name="gid" value="<?= htmlspecialchars($gid) ?>">
            <img src="<?= $image ?>" alt="Current Genre Image" style="display: none;">

            <!-- TWO-COLUMN MAIN WORKSPACE FRAME -->
            <div class="row g-4">

                <!-- Track Title Parameter Field -->
                <div class="col-12">
                    <div class="field-title">Enter Genre Name</div>
                    <!-- 🔴 અહી value સેટ કરી છે જેથી ડેટાબેઝ વાળું નામ દેખાય -->
                    <input type="text" name="name" id="name" placeholder="Enter Genre Name..."
                        class="form-control music-input" value="<?= htmlspecialchars($name) ?>">
                    <span class="error" id="nameError"
                        style="display: <?= !empty($nameError) ? 'block' : 'none' ?>;"><?= $nameError ?></span>
                </div>

                <!-- Marketing Distribution Poster Node Block (16:9 Landscape Layout) -->
                <div>
                    <div class="field-title">Distribution Poster Layer</div>
                    <div class="music-media-uploader-frame" id="trigger_posterImage" style="aspect-ratio: 16 / 9;">

                        <!-- 🔴 ડેટાબેઝ વાળો ફોટો અહી બતાવવા માટે src સેટ કર્યું -->
                        <img id="posterImagePreview" src="<?= $image ?>" class="uploader-absolute-preview-img"
                            style="display: <?= ($image == $fallback_image) ? 'none' : 'block' ?>;"
                            alt="Poster Graphic Preview" crossorigin="anonymous">

                        <span class="uploader-placeholder-text" id="posterPlaceholderText"
                            style="display: <?= ($image == $fallback_image) ? 'block' : 'none' ?>;">
                            <i class="uil uil-image d-block fs-2 mb-2"></i>
                            Change landscape poster
                        </span>
                    </div>
                    <!-- 🛑 name "image" છે, એ તારા બેકએન્ડના હિસાબે સેટ છે -->
                    <input type="file" id="posterImage" name="image" accept="image/*" class="d-none" />
                    <span class="error" id="posterImageError"
                        style="display: <?= !empty($photoError) ? 'block' : 'none' ?>;"><?= $photoError ?></span>
                </div>

                <div class="text-center mt-5 pt-2">
                    <!-- 🔴 Insert ની જગ્યાએ Update સેટ કર્યું -->
                    <input type="submit" value="Update" name="update" id="update"
                        class="btn btn-music-master-deploy shadow w-100">
                </div>

            </div>

        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- 1. Image Preview & Click Logic ---
        const frame = document.getElementById('trigger_posterImage');
        const input = document.getElementById('posterImage');
        const preview = document.getElementById('posterImagePreview');
        const placeholder = document.getElementById('posterPlaceholderText');
        const photoError = document.getElementById("posterImageError");
        const nameError = document.getElementById("nameError");
        const nameField = document.getElementById("name");

        const fallbackImage = 'favicon_1.png';
        const currentImageSrc = document.querySelector('img[alt="Current Genre Image"]').getAttribute('src');

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
        nameField.addEventListener('input', function () {
            if (this.value.trim() !== "") {
                nameError.style.display = 'none'; // નામ લખતા જ એરર ગાયબ
            }
        });

        // --- 3. Form Submit Validation Logic ---
        document.getElementById("form1").addEventListener("submit", function (e) {

            let valid = true;

            // પહેલાની એરર હાઇડ કરો
            nameError.style.display = "none";
            photoError.style.display = "none";

            // 🔴 સબમિટ બટન update છે એટલે ચેક કરો
            if (e.submitter && e.submitter.name === "update") {

                // Name Check
                if (nameField.value.trim() === "") {
                    nameError.textContent = "⚠️ Genre name is required.";
                    nameError.style.display = "block"; // એરર બતાવો
                    valid = false;
                }

                // Image Check (જો જૂનો ફોટો ના હોય તો જ ફરજિયાત છે)
                if (currentImageSrc.endsWith(fallbackImage) && !input.value) {
                    photoError.textContent = "⚠️ Upload landscape poster image is required.";
                    photoError.style.display = "block";
                    valid = false;
                } else if (input.value) {
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
</body>

</html>

<?php include('fff.php'); ?>

<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script src="assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
<script src="assets/plugins/datatable/js/jszip.min.js"></script>
<script src="assets/plugins/datatable/pdfmake/pdfmake.min.js"></script>
<script src="assets/plugins/datatable/pdfmake/vfs_fonts.js"></script>
<script src="assets/plugins/datatable/js/buttons.html5.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.print.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.colVis.min.js"></script>
<script src="assets/plugins/datatable/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatable/responsive.bootstrap5.min.js"></script>
<script src="assets/js/table-data.js"></script>
<script src="assets/js/select2.js"></script>
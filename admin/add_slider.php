<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

$default_img_path = 'favicon_1.png';

// 1. ⚡ Fetch current SIDs from DB
$current_slider_sids = [];
$sliderQry = mysqli_query($con, "SELECT sid FROM slider");
if ($sliderQry) {
    while ($row = mysqli_fetch_assoc($sliderQry)) {
        $current_slider_sids[] = (string) $row['sid'];
    }
}

$current_sids_csv = implode(',', $current_slider_sids);
$current_sids_json = json_encode($current_slider_sids);

// 2. 🔹 Fetch ALL Songs from API using Relative Path Logic
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$songsApiUrl = $protocol . $host . "/SIngIT/flutter_crud/getSongs.php";

$all_songs = [];
$response = @file_get_contents($songsApiUrl);
if ($response !== FALSE) {
    $all_songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($all_songs)) {
        $all_songs = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Slider</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    /* --- CUSTOM DROPDOWN MATRIX --- */
    #custom-dropdown-header {
        border: 1px solid var(--input-border);
        background-color: var(--input-bg);
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

    #custom-dropdown-header:hover {
        border-color: rgba(255, 255, 255, 0.15);
    }

    #custom-dropdown-list {
        background-color: #0e0e23;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-top: none;
        max-height: 220px;
        overflow-y: auto;
        border-radius: 0 0 12px 12px;
        padding: 0.75rem;
        display: none;
        position: absolute;
        width: 88.5%;
        z-index: 10;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition-music);
        color: var(--text-secondary);
    }

    .checkbox-item:hover {
        background-color: rgba(255, 255, 255, 0.03);
        color: var(--text-primary);
    }

    .checkbox-item input[type="checkbox"] {
        margin-right: 12px;
        width: 18px;
        height: 18px;
        accent-color: var(--accent-neon);
        cursor: pointer;
    }

    .checkbox-item img {
        margin-right: 12px;
        border-radius: 4px;
        object-fit: cover;
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
            <h2 class="main-content-title tx-24 mg-b-5">Add/Edit Slider</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Slider</li>
            </ol>
        </div>
    </div>

    <div class="music-container px-3 col-md-6 m-auto d-block">
        <form action="../flutter_crud/addSlider.php" method="post" id="form1"
            class="mb-4 border bg-white p-5 shadow music-form-panel">

            <div
                class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
                <h1 class="music-heading mb-0">Slider Form</h1>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="field-title">Select Song(s)</div>

                    <input type="hidden" name="sid_list" id="selected_song_sid_list"
                        value="<?= htmlspecialchars($current_sids_csv) ?>" required>

                    <div id="custom-dropdown-header">
                        <span id="selectionSummary"><?= count($current_slider_sids) ?> Selected</span>
                        <i class="uil uil-angle-down"></i>
                    </div>

                    <div id="custom-dropdown-list">
                        <?php if (!empty($all_songs)): ?>
                        <?php foreach ($all_songs as $song):
                                $poster_src = htmlspecialchars($song['image'] ?? $default_img_path);
                                $song_id = htmlspecialchars($song['sid'] ?? '');
                                $song_name = htmlspecialchars($song['name'] ?? 'Unknown Song');
                                $is_checked = in_array($song_id, $current_slider_sids);
                                ?>
                        <label class="checkbox-item">
                            <input type="checkbox" value="<?= $song_id ?>" class="song-checkbox"
                                <?= $is_checked ? 'checked' : '' ?>>
                            <img src="<?= $poster_src ?>" height="30" width="30" alt="Poster">
                            <span><?= $song_name ?></span>
                        </label>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-danger">No songs found.</p>
                        <?php endif; ?>
                    </div>

                    <span class="error" id="selectionError">⚠️ Select at least one song.</span>
                </div>

                <div class="col-12 text-center mt-4 pt-2">
                    <input type="submit" value="Update Slider" name="insert" id="insert"
                        class="btn btn-music-master-deploy shadow w-100" />
                </div>
            </div>

        </form>
    </div>

    <script>
    const dropdownHeader = document.getElementById('custom-dropdown-header');
    const dropdownList = document.getElementById('custom-dropdown-list');
    const selectionSummary = document.getElementById('selectionSummary');
    const selectedSongSidListInput = document.getElementById('selected_song_sid_list');
    const selectionError = document.getElementById('selectionError');

    let selectedSids = <?= $current_sids_json ?>;

    $(document).ready(function() {
        selectionSummary.textContent = `${selectedSids.length} Selected`;
    });

    dropdownHeader.addEventListener('click', () => {
        $(dropdownList).slideToggle(200);
        selectionError.style.display = 'none';
    });

    $('#custom-dropdown-list').on('change', '.song-checkbox', function() {
        const sid = this.value;
        if (this.checked) {
            if (!selectedSids.includes(sid)) selectedSids.push(sid);
        } else {
            selectedSids = selectedSids.filter(s => s !== sid);
        }
        selectedSongSidListInput.value = selectedSids.join(',');
        selectionSummary.textContent = `${selectedSids.length} Selected`;
    });

    document.getElementById("form1").addEventListener("submit", function(e) {
        if (selectedSids.length === 0) {
            selectionError.style.display = 'block';
            e.preventDefault();
        }
    });
    </script>

</body>

</html>

<?php include('fff.php'); ?>
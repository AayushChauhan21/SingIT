<?php
// FILE: edit_slider.php
include('demo.php');
include('hhh.php');
include('connection.php'); 
error_reporting(1);

$default_img_path = 'favicon_1.png'; 
$current_slider_sids = []; 

// --- Dynamic URL Construction ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$songsApiUrl = $protocol . $host . "/SIngIT/flutter_crud/getSongs.php"; 

// 1. 🔹 Fetch ALL Songs from API
$all_songs = [];
$response = @file_get_contents($songsApiUrl);
if ($response !== FALSE) {
    $all_songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($all_songs)) {
        $all_songs = [];
    }
}

// 2. ⚡ Fetch CURRENT Slider Songs from DB
// Adjusted query to match your usage of 'sid' throughout the script
$sliderQry = mysqli_query($con, "SELECT sid FROM slider");
if ($sliderQry) {
    while ($row = mysqli_fetch_assoc($sliderQry)) {
        // Changed from 'song_id' to 'sid' to maintain consistency
        $current_slider_sids[] = (string) $row['sid'];
    }
}

$current_sids_csv = implode(',', $current_slider_sids);
$current_sids_json = json_encode($current_slider_sids);
?>

<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .checkbox-item input[type="checkbox"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        accent-color: #AA62C7;
    }
    form { background-color: white; border-radius: 20px; }
    h1 { border-radius: 10px; }
    .input { border-radius: 10px; height: 40px; color: black; }
    .btn-lg { padding: 0rem 3rem; font-size: 0.875rem; border-radius: 10px; }
    #gradient { background: linear-gradient(135deg, #6259ca, #ff6ec4); }
    .error { color: red; font-size: 14px; margin-top: 5px; display: none; }
    #custom-dropdown-header {
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #custom-dropdown-list {
        border: 1px solid #ddd;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        border-radius: 0 0 8px 8px;
        padding: 10px;
        display: none;
    }
    .checkbox-item { display: flex; align-items: center; padding: 5px 0; cursor: pointer; }
    .checkbox-item img { margin-right: 10px; border-radius: 3px; object-fit: cover; }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Edit Slider Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Slider</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form action="../flutter_crud/addSlider.php" method="post" id="form1"
        class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">

        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Edit Slider Songs</strong>
        </h1>
        <br />

        <h5>Select Song(s):</h5>

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
                        <input type="checkbox" value="<?= $song_id ?>" 
                            class="song-checkbox" <?= $is_checked ? 'checked' : '' ?>>
                        <img src="<?= $poster_src ?>" height="30" width="30" alt="Poster">
                        <span><?= $song_name ?></span>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-danger">No songs found.</p>
            <?php endif; ?>
        </div>

        <span class="error" id="selectionError">⚠️ Please select at least one song for the slider.</span>

        <br />
        <center>
            <input type="submit" value="Update" name="insert" id="update"
                class="btn btn-outline-primary btn-lg input" />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" value="View List" class="btn btn-outline-danger btn-lg input"
                onclick="window.location.href='view_slider.php'" />
        </center>
    </form>
</div>

<script>
    const dropdownHeader = document.getElementById('custom-dropdown-header');
    const dropdownList = document.getElementById('custom-dropdown-list');
    const selectionSummary = document.getElementById('selectionSummary');
    const selectedSongSidListInput = document.getElementById('selected_song_sid_list');
    const selectionError = document.getElementById("selectionError");

    let selectedSids = <?= $current_sids_json ?>;

    $(document).ready(function () {
        selectionSummary.textContent = `${selectedSids.length} Selected`;
    });

    dropdownHeader.addEventListener('click', () => {
        $(dropdownList).slideToggle(200);
        selectionError.style.display = 'none';
    });

    $('#custom-dropdown-list').on('change', '.song-checkbox', function () {
        const sid = this.value;
        if (this.checked) {
            if (!selectedSids.includes(sid)) selectedSids.push(sid);
        } else {
            selectedSids = selectedSids.filter(s => s !== sid);
        }

        selectedSongSidListInput.value = selectedSids.join(',');
        selectionSummary.textContent = `${selectedSids.length} Selected`;
    });

    document.getElementById("form1").addEventListener("submit", function (e) {
        if (selectedSids.length === 0) {
            selectionError.style.display = 'block';
            e.preventDefault();
        }
    });
</script>

<?php include('fff.php'); ?>
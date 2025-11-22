<?php
// FILE: edit_special.php (Now allows only one selection using radio buttons)

include('demo.php');
include('hhh.php');
include('connection.php'); // Database connection needed to fetch current special songs
error_reporting(1);

$default_img_path = 'favicon_1.png';
$current_special_sids = []; // Array to store SIDs currently in the special table

// 1. 🔹 Fetch ALL Songs from API (for selection list)
$songsApiUrl = "http://localhost/SIngIT/flutter_crud/getSongs.php";
$all_songs = [];

$response = @file_get_contents($songsApiUrl);
if ($response !== FALSE) {
    $all_songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($all_songs)) {
        $all_songs = [];
    }
}

// 2. ⚡ Fetch CURRENT special Songs from DB
$specialQry = mysqli_query($con, "SELECT sid FROM special");
if ($specialQry) {
    while ($row = mysqli_fetch_assoc($specialQry)) {
        // Store SIDs. Since only one is allowed, we only care about the first one.
        $current_special_sids[] = (string) $row['sid'];
    }
}

// For single selection, we only need the first element (or null)
$current_sid = count($current_special_sids) > 0 ? $current_special_sids[0] : null;
$current_sid_json = json_encode($current_sid);
?>


<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* Updated style for radio buttons */
    .checkbox-item input[type="radio"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        accent-color: #AA62C7;
    }

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

    .btn-lg {
        padding: 0rem 3rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }

    #gradient {
        background: linear-gradient(135deg, #6259ca, #ff6ec4);
    }

    .error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
        display: none;
    }

    /* --- CUSTOM SINGLE-SELECT STYLES --- */
    #custom-dropdown-header {
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #custom-dropdown-list {
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        border-radius: 0 0 8px 8px;
        padding: 10px;
        display: none;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        padding: 5px 0;
    }

    .checkbox-item img {
        margin-right: 10px;
        border-radius: 3px;
    }

    .selection-summary {
        font-size: 14px;
        color: #5b478d;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Edit special Song</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit special</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form action="http://localhost/SIngIT/flutter_crud/addSpecial.php" method="post" id="form1"
        class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">

        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Edit special Song</strong>
        </h1>
        <br />

        <h5>Select Song (Only one allowed):</h5>

        <input type="hidden" name="sid_list" id="selected_song_sid_list" required>

        <div id="custom-dropdown-header">
            <span id="selectionSummary">No Selected</span>
            <i class="uil uil-angle-down"></i>
        </div>

        <div id="custom-dropdown-list">
            <?php if (!empty($all_songs) && is_array($all_songs)): ?>

                <?php foreach ($all_songs as $song):
                    $poster_src = htmlspecialchars($song['image'] ?? $default_img_path);
                    $song_id = htmlspecialchars($song['sid'] ?? '');
                    $song_name = htmlspecialchars($song['name'] ?? 'Unknown Song');

                    // Check if this is the currently selected song 
                    $is_checked = ($song_id == $current_sid);
                    ?>
                    <label class="checkbox-item">
                        <input type="radio" name="selected_song" value="<?= $song_id ?>" data-name="<?= $song_name ?>"
                            class="song-radio" <?= $is_checked ? 'checked' : '' ?>>
                        <img src="<?= $poster_src ?>" height="30" width="30" alt="Poster">
                        <span><?= $song_name ?></span>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-danger">Error: Could not load songs or no songs found from API.</p>
            <?php endif; ?>

        </div>

        <span class="error" id="selectionError">⚠️ Please select exactly one song for the special.</span>

        <br />
        <center>
            <input type="submit" value="Update" name="update" id="update"
                class="btn btn-outline-primary btn-lg input" />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <input type="button" id="view" name="view" value="View List" class="btn btn-outline-danger btn-lg input"
                onclick="window.location.href='view_special.php'" /> -->
        </center>
    </form>
    <br>
</div>


<script>
    // --- JS for Single-Select Radio Logic ---
    const dropdownHeader = document.getElementById('custom-dropdown-header');
    const dropdownList = document.getElementById('custom-dropdown-list');
    const selectionSummary = document.getElementById('selectionSummary');
    const selectedSongSidListInput = document.getElementById('selected_song_sid_list');
    const selectionError = document.getElementById("selectionError");

    // ⚡ Get currently selected SID from PHP
    let selectedSid = <?= $current_sid_json ?>;

// --- INITIALIZATION & Initial Summary ---
$(document).ready(function () {
        selectionError.style.display = 'none';

        // Update the summary text based on the pre-selected radio button (if any)
        const checkedRadio = document.querySelector('.song-radio:checked');
        if (checkedRadio) {
            selectionSummary.textContent = checkedRadio.dataset.name + " Selected";
            selectedSongSidListInput.value = checkedRadio.value;
        } else {
            selectionSummary.textContent = "0 Selected";
            selectedSongSidListInput.value = "";
        }
    });

    // 2. Toggle dropdown visibility
    dropdownHeader.addEventListener('click', () => {
        $(dropdownList).slideToggle(200);

        // Clear error state when the user interacts with the dropdown
        selectionError.textContent = "";
        selectionError.style.display = 'none';
    });

    // 3. Handle radio button changes
    $('#custom-dropdown-list').on('change', '.song-radio', function () {
        if (this.checked) {
            selectedSid = this.value;
            // Crucial: The hidden input now only holds the single SID
            selectedSongSidListInput.value = selectedSid;
            selectionSummary.textContent = this.dataset.name + " Selected";

            // Clear error state immediately upon selection
            selectionError.style.display = 'none';
        }
    });


    // 4. Form Submission Validation (Runs ONLY on Update button click)
    document.getElementById("form1").addEventListener("submit", function (e) {
        let valid = true;

        // Reset errors visually before check
        selectionError.textContent = "";
        selectionError.style.display = 'none';

        if (e.submitter && e.submitter.name === "update") {
            // 1. Validate Song Selection (Must select exactly one song)
            if (!selectedSongSidListInput.value || selectedSongSidListInput.value.length === 0) {
                selectionError.textContent = "⚠️ Please select exactly one song for the special.";
                selectionError.style.display = 'block';
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        }
    });
</script>

<?php include('fff.php'); ?>
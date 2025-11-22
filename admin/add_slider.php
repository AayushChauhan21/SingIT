<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

$default_img_path = 'favicon_1.png'; // Default path assumption

// 🔹 Fetch ALL Songs from API (for selection list)
$songsApiUrl = "http://localhost/SIngIT/flutter_crud/getSongs.php";
$all_songs = [];

$response = @file_get_contents($songsApiUrl);
if ($response !== FALSE) {
    $all_songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($all_songs)) {
        $all_songs = [];
    }
}
?>


<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .checkbox-item input[type="checkbox"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        /* 🚀 FIX: Checkbox ka color badalne ke liye */
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
        /* font-size:19px; */
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
        /* FIX: Hidden by default */
        display: none;
    }

    /* --- CUSTOM MULTI-SELECT STYLES (NO RED BORDER CSS) --- */
    #custom-dropdown-header {
        /* Now uses default border */
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* Removed transition as the error class is removed */
    }

    /* 🔴 REMOVED: #custom-dropdown-header.dropdown-error CSS here */


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

    .checkbox-item input[type="checkbox"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
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
        <h2 class="main-content-title tx-24 mg-b-5">Add Slider</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Slider</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form action="http://localhost/SIngIT/flutter_crud/addSlider.php" method="post" id="form1"
        class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">

        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Slider Form</strong>
        </h1>
        <br />

        <h5>Select Song(s):</h5>

        <input type="hidden" name="sid_list" id="selected_song_sid_list" required>

        <div id="custom-dropdown-header">
            <span id="selectionSummary">0 Selected</span>
            <i class="uil uil-angle-down"></i>
        </div>

        <div id="custom-dropdown-list">
            <?php if (!empty($all_songs) && is_array($all_songs)): ?>

                <?php foreach ($all_songs as $song):
                    // NOTE: Assuming your API sends 'poster' or 'image' field
                    $poster_src = htmlspecialchars($song['image'] ?? $default_img_path);
                    $song_id = htmlspecialchars($song['sid'] ?? '');
                    $song_name = htmlspecialchars($song['name'] ?? 'Unknown Song');
                    ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="selected_songs[]" value="<?= $song_id ?>" data-name="<?= $song_name ?>"
                            class="song-checkbox">
                        <img src="<?= $poster_src ?>" height="30" width="30" alt="Poster">
                        <span><?= $song_name ?></span>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-danger">Error: Could not load songs or no songs found from API.</p>
            <?php endif; ?>

        </div>

        <span class="error" id="selectionError">⚠️ Select at least one song.</span>

        <br />
        <center>
            <input type="submit" value="Insert" name="insert" id="insert"
                class="btn btn-outline-primary btn-lg input" />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input" /> -->
        </center>
    </form>
    <br>
</div>


<script>
    // --- JS for Multi-Select Dropdown Logic ---
    const dropdownHeader = document.getElementById('custom-dropdown-header');
    const dropdownList = document.getElementById('custom-dropdown-list');
    const selectionSummary = document.getElementById('selectionSummary');
    const selectedSongSidListInput = document.getElementById('selected_song_sid_list');
    const selectionError = document.getElementById("selectionError");

    let selectedSids = [];

    // 1. Ensure elements are hidden on page load
    $(document).ready(function () {
        selectionError.style.display = 'none';
        // Removed: dropdownHeader.classList.remove('dropdown-error');
    });

    // 2. Toggle dropdown visibility
    dropdownHeader.addEventListener('click', () => {
        $(dropdownList).slideToggle(200);

        // Clear error state when the user interacts with the dropdown
        selectionError.textContent = "";
        selectionError.style.display = 'none'; // Hide error text
        // Removed: dropdownHeader.classList.remove('dropdown-error');
    });

    // 3. Handle checkbox changes
    $('#custom-dropdown-list').on('change', '.song-checkbox', function () {
        const sid = this.value;

        if (this.checked) {
            if (selectedSids.indexOf(sid) === -1) {
                selectedSids.push(sid);
            }
        } else {
            selectedSids = selectedSids.filter(s => s !== sid);
        }

        selectedSongSidListInput.value = selectedSids.join(',');
        selectionSummary.textContent = `${selectedSids.length} Selected`;

        // Clear error state immediately upon successful selection change
        selectionError.textContent = "";
        selectionError.style.display = 'none'; // Hide error text
        // Removed: dropdownHeader.classList.remove('dropdown-error');
    });


    // 4. Form Submission Validation (Runs ONLY on Insert button click)
    document.getElementById("form1").addEventListener("submit", function (e) {
        let valid = true;

        // Reset errors visually before check
        selectionError.textContent = "";
        selectionError.style.display = 'none';
        // Removed: dropdownHeader.classList.remove('dropdown-error');


        if (e.submitter && e.submitter.name === "insert") {
            // 1. Validate Song Selection (Must select at least one song)
            if (selectedSids.length === 0) {
                selectionError.textContent = "⚠️ Please select at least one song for the slider.";

                // 🚀 FIX: Make the error text VISIBLE
                selectionError.style.display = 'block';

                // REMOVED: dropdownHeader.classList.add('dropdown-error');

                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        }
    });
</script>

<?php include('fff.php'); ?>
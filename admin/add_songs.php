<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

// Define a default image path in case the DB doesn't have one
$default_img_path = 'favicon_1.png'; // Default path assumption

// 1. 🎵 Fetch Genre Options from API (PHP file_get_contents)
$genreApiUrl = "http://localhost/SIngIT/flutter_crud/getGenres.php";
$genreOptions = [];
$response = @file_get_contents($genreApiUrl);
if ($response !== FALSE) {
    $genreOptions = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($genreOptions)) {
        $genreOptions = [];
    }
}

// 2. 👤 Fetch Artist Options from API (PHP file_get_contents)
$artistApiUrl = "http://localhost/SIngIT/flutter_crud/getArtists.php";
$artistOptions = [];
$response = @file_get_contents($artistApiUrl);
if ($response !== FALSE) {
    $artistOptions = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($artistOptions)) {
        $artistOptions = [];
    }
}

// 3. 🌐 Fetch Language Options from API (PHP file_get_contents)
$languageApiUrl = "http://localhost/SIngIT/flutter_crud/getLanguage.php";
$languageOptions = [];
$response = @file_get_contents($languageApiUrl);
if ($response !== FALSE) {
    $languageOptions = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($languageOptions)) {
        $languageOptions = [];
    }
}
?>

<style>
    /* --- CUSTOM STYLES --- */
    /* Vocal, Instrumental અને Poster માટેના ID અપડેટ કર્યા */
    #custom-button_vocal,
    #custom-button_i,
    #custom-button_in,
    #custom-button_poster {
        padding: 10px;
        color: white;
        background: linear-gradient(135deg, #6259ca, #ff6ec4);
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }

    #custom-button_vocal:hover,
    #custom-button_i:hover,
    #custom-button_in:hover,
    #custom-button_poster:hover {
        background-color: #9080f4;
    }

    #custom-text_vocal,
    #custom-text_i,
    #custom-text_in,
    #custom-text_poster {
        margin-left: 10px;
        font-family: sans-serif;
        color: #aaa;
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

    #gradient {
        background: linear-gradient(135deg, #6259ca, #ff6ec4);
    }

    .btn-lg {
        padding: 0rem 3rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }

    .img-preview {
        display: none;
        margin-top: 10px;
        border-radius: 10px;
        box-shadow: 0 0 10px #ccc;
    }

    .error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
        display: none;
    }

    /* --- CUSTOM MULTI-SELECT STYLES (SHARED) */
    #custom-dropdown-header-genre,
    #custom-dropdown-header-artist,
    #custom-dropdown-header-language {
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }


    .custom-dropdown-list-style {
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
        /* Apply custom color to checkbox */
        accent-color: #AA62C7;
    }

    /* 🖼️ IMAGE DIMENSION FIX: Set universal styles for all images */
    .dropdown-img {
        margin-right: 10px;
        border-radius: 3px;
        object-fit: cover;
    }

    /* 👤 Artist Image: Vertical Rectangle (taller) */
    .artist-img {
        height: 40px;
        width: 30px;
    }

    /* 🎵 Genre / 🌐 Language Images: Horizontal Rectangle (wider) */
    .genre-lang-img {
        height: 30px;
        width: 40px;
    }

    .selection-summary {
        font-size: 14px;
        color: #5b478d;
    }
</style>


<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Add Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Songs</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form action="http://localhost/SIngIT/flutter_crud/addSongs.php" method="post" enctype="multipart/form-data"    
        id="form1" class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">

        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Song Form</strong>
        </h1>

        <br>
        <h5>Track Name:</h5>
        <input type="text" name="trackName" id="trackName" placeholder="Enter Track Name..."            
            class="form-control input">
        <span class="error" id="trackNameError"></span>

        <br>
        <h5>Genre(s):</h5>
        <input type="hidden" name="genreId[]" id="selected_genre_id_list">

        <div id="custom-dropdown-header-genre">
            <span id="selectionSummaryGenre">0 Selected</span>
            <i class="uil uil-angle-down"></i>
        </div>

        <div id="custom-dropdown-list-genre" class="custom-dropdown-list-style">


            <?php foreach ($genreOptions as $genre):
                // Genre API: expects 'gid', 'name', 'image'
                $image_src = htmlspecialchars($genre['image'] ?? $default_img_path);
                ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $genre['gid'] ?>" data-name="<?= htmlspecialchars($genre['name']) ?>"
                        class="genre-checkbox">
                    <img src="<?= $image_src ?>" alt="" title="<?= htmlspecialchars($genre['name']) ?>"    
                        class="dropdown-img genre-lang-img">
                    <span><?= htmlspecialchars($genre['name']) ?></span>
                </label>
            <?php endforeach; ?>



        </div>
        <span class="error" id="genreError"></span>

        <br>
        <h5>Artist(s):</h5>
        <input type="hidden" name="artistId[]" id="selected_artist_id_list">

        <div id="custom-dropdown-header-artist">
            <span id="selectionSummaryArtist">0 Selected</span>
            <i class="uil uil-angle-down"></i>
        </div>

        <div id="custom-dropdown-list-artist" class="custom-dropdown-list-style">

            <?php foreach ($artistOptions as $artist):
                // Artist API: expects 'arid', 'name', 'photo'
                $image_src = htmlspecialchars($artist['photo'] ?? $default_img_path); // Note: Assuming 'photo' key from API
                ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $artist['arid'] ?>"                    
                        data-name="<?= htmlspecialchars($artist['name']) ?>" class="artist-checkbox">

                    <img src="<?= $image_src ?>" alt="" title="<?= htmlspecialchars($artist['name']) ?>"    
                        class="dropdown-img artist-img">

                    <span><?= htmlspecialchars($artist['name']) ?></span>
                </label>
            <?php endforeach; ?>

        </div>
        <span class="error" id="artistError"></span>

        <br>
        <h5>Language(s):</h5>
        <input type="hidden" name="languageId[]" id="selected_language_id_list">

        <div id="custom-dropdown-header-language">
            <span id="selectionSummaryLanguage">0 Selected</span>
            <i class="uil uil-angle-down"></i>
        </div>

        <div id="custom-dropdown-list-language" class="custom-dropdown-list-style">

            <?php foreach ($languageOptions as $language):
                // Language API: expects 'lid', 'name', 'image'
                $image_src = htmlspecialchars($language['image'] ?? $default_img_path);
                ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $language['lid'] ?>"                    
                        data-name="<?= htmlspecialchars($language['name']) ?>" class="language-checkbox">
                    <img src="<?= $image_src ?>" alt="" title="<?= htmlspecialchars($language['name']) ?>"  
                        class="dropdown-img genre-lang-img">
                    <span><?= htmlspecialchars($language['name']) ?></span> </label>
            <?php endforeach; ?>

        </div>
        <span class="error" id="languageError"></span>

        <br />

        <center>
            <button type="button" class="btn btn-outline-success mt-2 btn-lg input"                
                onclick="fetchLyrics()">Auto-Fill</button>
        </center>

        <br>
        <h5>Album Name:</h5>
        <input type="text" name="albumName" id="albumName" placeholder="Enter Album Name..."            
            class="form-control input">
        <span class="error" id="albumError"></span>

        <br>
        <h5>Duration (in seconds):</h5>
        <input type="text" name="duration" id="duration" placeholder="Enter Duration..."            
            class="form-control input">
        <span class="error" id="durationError"></span>

        <br>
        <h5>Lyrics:</h5>
        <textarea name="syncedLyrics" id="syncedLyrics" rows="4" placeholder="Enter Lyrics..."            
            class="form-control input" style="padding-top: 10px;"></textarea>
        <span class="error" id="lyricsError"></span>

        <input type="hidden" name="albumImageUrl" id="albumImageUrl">
        <img id="albumImagePreview" src="" alt="Album Image Preview" class="img-fluid img-preview mt-3 mb-3"    
            height="100" width="100">

        <div id="manualImageUpload">
            <br>
            <h5>Upload Album Image (Manual):</h5>
            <input type="file" id="manualImage" name="manualImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_in">CHOOSE A FILE</button>
            <span id="custom-text_in">No file chosen, yet.</span>
            <span class="error" id="imageError"></span>
        </div>

        <h5 class="mt-3">Upload Poster Image:</h5>
        <input type="file" id="posterImage" name="posterImage" accept="image/*" hidden="hidden" />
        <button type="button" id="custom-button_poster">CHOOSE A FILE</button>
        <span id="custom-text_poster">No file chosen, yet.</span>
        <span class="error" id="posterImageError"></span>

        <h5 class="mt-3">Upload Vocal Audio:</h5>
        <input type="file" id="audio_vocal" name="audio_vocal" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_vocal">CHOOSE A FILE</button>
        <span id="custom-text_vocal">No file chosen, yet.</span>
        <span class="error" id="audioVocalError"></span>


        <h5 class="mt-3">Upload Instrumental Audio:</h5>
        <input type="file" id="audio_i" name="audio_i" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_i">CHOOSE A FILE</button>
        <span id="custom-text_i">No file chosen, yet.</span>
        <span class="error" id="audioIError"></span>
        <center class="mt-4">
            <input type="submit" value="Insert" name="insert" id="insert"                
                class="btn btn-outline-primary btn-lg input">
            &nbsp;&nbsp;&nbsp;
        </center>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // --- SweetAlert Helper Functions (Defined here for clarity) ---

    // FIX: Function to force scroll restoration
    function restoreScroll() {
        // Ensure the SweetAlert library doesn't leave the scroll lock on the body/html element
        if (document.body.classList.contains('swal-open')) {
            document.body.classList.remove('swal-open');
        }
        document.body.style.overflow = '';
        document.documentElement.style.overflowY = 'auto';
    }

    function showSuccessAlert(msg, callback = null) {
        swal({
            title: 'Well done!',
            text: msg,
            type: 'success',
            confirmButtonColor: '#57a94f',
            onClose: restoreScroll,
            allowOutsideClick: true,
            showConfirmButton: true
        }, callback);
    }

    function showErrorAlert(msg, callback = null) {
        swal({
            title: 'Oops!',
            text: msg,
            type: 'error',
            confirmButtonColor: '#ff0000',
            onClose: restoreScroll,
            allowOutsideClick: true,
            showConfirmButton: true
        }, callback);
    }

    // Placeholder for API fetching logic (Auto-Fill)
    async function fetchLyrics() {
        const artistSelect = document.getElementById("selected_artist_id_list").value;
        const track = document.getElementById("trackName").value;

        // 1. Pre-API Validation using SweetAlert
        if (artistSelect.length === 0 || !track) {
            showErrorAlert("Please select at least one artist and enter track name before using Auto-Fill.");
            return;
        }

        const firstArtistId = artistSelect.split(',')[0];
        const artistCheckbox = document.querySelector(`.artist-checkbox[value="${firstArtistId}"]`);

        if (!artistCheckbox) {
            showErrorAlert(
                "Could not determine artist name for auto-fill. Please ensure the selected artist is in the list.");
            return;
        }

        const firstArtistName = artistCheckbox.dataset.name.trim();

        if (!firstArtistName) {
            showErrorAlert("Could not determine artist name for auto-fill.");
            return;
        }

        // 🛑 REMOVED: Loading SweetAlert as per request.

        const res = await fetch(
            `lyrics_fetch.php?artist=${encodeURIComponent(firstArtistName)}&track=${encodeURIComponent(track)}`
        );
        const data = await res.json();

        // No swal.close() or restoreScroll() needed here as the loading modal was removed.


        if (data.status === "found") {
            // Set values
            document.getElementById("albumName").value = data.albumName;
            document.getElementById("duration").value = data.duration;
            document.getElementById("syncedLyrics").value = data.syncedLyrics;
            document.getElementById("albumImageUrl").value = data.albumImage; // Sets hidden field

            // Handle image display
            document.getElementById("albumImagePreview").src = data.albumImage;
            document.getElementById("albumImagePreview").style.display = "block";
            document.getElementById("manualImageUpload").style.display = "none"; // Hide manual upload section

            // ✅ Clear validation errors for auto-filled fields
            document.getElementById("trackNameError").style.display = 'none';
            document.getElementById("albumError").style.display = 'none';
            document.getElementById("durationError").style.display = 'none';
            document.getElementById("lyricsError").style.display = 'none';
            document.getElementById("imageError").style.display = 'none';

            // Show success alert and wait for user click
            showSuccessAlert("Track details auto-filled successfully!");

        } else {
            // If Auto-fill fails, ensure manual image upload section is visible
            document.getElementById("albumImagePreview").style.display = "none";
            document.getElementById("manualImageUpload").style.display = "block";

            // Clear hidden image URL field if auto-fill failed
            document.getElementById("albumImageUrl").value = "";

            // Show error alert and wait for user click
            showErrorAlert("No song data found! Please insert song details manually.");
        }
    }


    // --- JS for Custom Multi-Select Logic (Genre, Artist, Language) ---

    function setupCustomDropdown(headerId, listId, checkboxClass, summaryId, hiddenInputId, errorId) {
        const dropdownHeader = document.getElementById(headerId);
        const dropdownList = document.getElementById(listId);
        const selectionSummary = document.getElementById(summaryId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const selectionError = document.getElementById(errorId);

        let selectedIds = hiddenInput.value.split(',').filter(id => id.trim() !== '');

        dropdownHeader.addEventListener('click', () => {
            $(dropdownList).slideToggle(200);

            selectionError.textContent = "";
            selectionError.style.display = 'none';
        });

        $(dropdownList).on('change', `.${checkboxClass}`, function () {
            const id = this.value;

            if (this.checked) {
                if (selectedIds.indexOf(id) === -1) {
                    selectedIds.push(id);
                }
            } else {
                selectedIds = selectedIds.filter(s => s !== id);
            }

            hiddenInput.value = selectedIds.join(',');
            selectionSummary.textContent = `${selectedIds.length} Selected`;

            if (selectedIds.length > 0) {
                selectionError.textContent = "";
                selectionError.style.display = 'none';
            }
        });

        return hiddenInput;
    }

    // Initialize the dropdowns
    const genreInput = setupCustomDropdown(
        'custom-dropdown-header-genre',
        'custom-dropdown-list-genre',
        'genre-checkbox',
        'selectionSummaryGenre',
        'selected_genre_id_list',
        'genreError'
    );

    const artistInput = setupCustomDropdown(
        'custom-dropdown-header-artist',
        'custom-dropdown-list-artist',
        'artist-checkbox',
        'selectionSummaryArtist',
        'selected_artist_id_list',
        'artistError'
    );

    const languageInput = setupCustomDropdown(
        'custom-dropdown-header-language',
        'custom-dropdown-list-language',
        'language-checkbox',
        'selectionSummaryLanguage',
        'selected_language_id_list',
        'languageError'
    );


    // 4. Form Submission Validation (Checks if fields are empty or invalid)
    document.getElementById("form1").addEventListener("submit", function (e) {
        let valid = true;

        const trackName = document.getElementById("trackName");
        const albumName = document.getElementById("albumName");
        const duration = document.getElementById("duration");
        const lyrics = document.getElementById("syncedLyrics");
        // ID 'audio_v' ને 'audio_vocal' માં બદલવામાં આવ્યું છે
        const audioVocal = document.getElementById("audio_vocal");
        const audioI = document.getElementById("audio_i");
        const albumImageUrl = document.getElementById("albumImageUrl"); // Hidden field
        const manualImage = document.getElementById("manualImage"); // File input
        const posterImage = document.getElementById("posterImage"); // 🆕 નવું Poster Image
        const imageError = document.getElementById("imageError");
        const posterImageError = document.getElementById("posterImageError"); // 🆕 Poster Error
        const audioVocalError = document.getElementById("audioVocalError"); // Vocal Audio Error

        // Clear all errors from previous attempt
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".error").forEach(el => el.style.display = 'none');


        // 1. Validate Track Name
        if (trackName.value.trim() === "") {
            document.getElementById("trackNameError").textContent = "⚠️ Track name is required.";
            document.getElementById("trackNameError").style.display = "block";
            valid = false;
        }

        // 2. Validate Dropdowns (Genre, Artist, Language)
        if (genreInput.value.trim() === "") {
            document.getElementById("genreError").textContent = "⚠️ Select at least one genre.";
            document.getElementById("genreError").style.display = "block";
            valid = false;
        }
        if (artistInput.value.trim() === "") {
            document.getElementById("artistError").textContent = "⚠️ Select at least one artist.";
            document.getElementById("artistError").style.display = "block";
            valid = false;
        }
        if (languageInput.value.trim() === "") {
            document.getElementById("languageError").textContent = "⚠️ Select at least one language.";
            document.getElementById("languageError").style.display = "block";
            valid = false;
        }


        // 3. Validate Album Name
        if (albumName.value.trim() === "") {
            document.getElementById("albumError").textContent = "⚠️ Album name is required.";
            document.getElementById("albumError").style.display = "block";
        }

        // 4. Validate Duration
        if (duration.value.trim() === "" || isNaN(duration.value) || parseInt(duration.value) <= 0) {
            document.getElementById("durationError").textContent = "⚠️ Enter valid duration (in seconds).";
            document.getElementById("durationError").style.display = "block";
            valid = false;
        }

        // 5. Validate Lyrics
        if (lyrics.value.trim() === "") {
            document.getElementById("lyricsError").textContent = "⚠️ Lyrics are required.";
            document.getElementById("lyricsError").style.display = "block";
            valid = false;
        }

        // 6. 🖼️ CONDITIONAL ALBUM IMAGE VALIDATION
        if (albumImageUrl.value.trim() === "" && manualImage.files.length === 0) {
            imageError.textContent = "⚠️ Album Image is required (must be auto-filled or uploaded manually).";
            imageError.style.display = "block";
            valid = false;
        }

        // 7. 🖼️ POSTER IMAGE VALIDATION (Required)
        if (posterImage.files.length === 0) {
            posterImageError.textContent = "⚠️ Upload Poster Image is required.";
            posterImageError.style.display = "block";
            valid = false;
        }


        // 8. Validate Vocal Audio (ID અપડેટ કર્યું)
        if (audioVocal.files.length === 0) {
            audioVocalError.textContent = "⚠️ Upload vocal audio.";
            audioVocalError.style.display = "block";
            valid = false;
        }

        // 9. Validate Instrumental Audio
        if (audioI.files.length === 0) {
            document.getElementById("audioIError").textContent = "⚠️ Upload instrumental audio.";
            document.getElementById("audioIError").style.display = "block";
            valid = false;
        }

        if (!valid) e.preventDefault();
    });


    // 🧼 Clear error on input or change (Hides error immediately on any input)
    [{
        input: "trackName",
        error: "trackNameError"
    },
    {
        input: "albumName",
        error: "albumError"
    },
    {
        input: "duration",
        error: "durationError"
    },
    {
        input: "syncedLyrics",
        error: "lyricsError"
    },
    {
        input: "audio_vocal", // ID અપડેટ કર્યું
        error: "audioVocalError" // ID અપડેટ કર્યું
    },
    {
        input: "audio_i",
        error: "audioIError"
    },
    {
        input: "posterImage", // 🆕 નવું ફીલ્ડ
        error: "posterImageError" // 🆕 નવું ફીલ્ડ
    },
    {
        input: "manualImage",
        error: "imageError"
    }
    ].forEach(({
        input,
        error
    }) => {
        const el = document.getElementById(input);
        const err = document.getElementById(error);

        if (el) {
            if (el.type === 'text' || el.tagName === 'TEXTAREA') {
                el.addEventListener("input", () => {
                    if (el.value.trim() !== "") err.style.display = "none";
                });
            } else if (el.type === 'file') {
                el.addEventListener("change", () => {
                    if (el.files.length > 0) err.style.display = "none";
                    // Also clear the image error if the hidden URL field has data (from auto-fill)
                    const albumImageUrl = document.getElementById("albumImageUrl");
                    if (albumImageUrl.value.trim() !== "") err.style.display = "none";
                });
            }
        }
    });


    // --- Custom File Button Logic ---

    const customFileSetup = (fileId, buttonId, textId) => {
        const realFileBtn = document.getElementById(fileId);
        const customBtn = document.getElementById(buttonId);
        const customTxt = document.getElementById(textId);

        customBtn.addEventListener("click", function () {
            realFileBtn.click();
        });

        realFileBtn.addEventListener("change", function () {
            if (realFileBtn.files.length > 0) {
                customTxt.innerHTML = realFileBtn.files[0].name;
            } else {
                customTxt.innerHTML = "No file chosen, yet.";
            }
        });
    };

    // Vocal Audio ID અપડેટ કર્યું
    customFileSetup("audio_vocal", "custom-button_vocal", "custom-text_vocal");
    customFileSetup("audio_i", "custom-button_i", "custom-text_i");
    customFileSetup("manualImage", "custom-button_in", "custom-text_in");
    // 🆕 નવું Poster Image ફીલ્ડ
    customFileSetup("posterImage", "custom-button_poster", "custom-text_poster");
</script>

<?php
include('fff.php');
?>
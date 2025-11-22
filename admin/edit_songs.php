<?php

include("connection.php");
include('demo.php');
include('hhh.php'); // Assumed to contain necessary header HTML

// Default image path (if DB fields are null)
$default_img_path = 'favicon_1.png';

// --- Utility Functions (Only needed for initial data fetch) ---
function timeStringToSeconds($timeString)
{
 $parts = explode(':', $timeString);
 $parts = array_reverse($parts);

 $seconds = 0;
 if (isset($parts[0]))
  $seconds += (int) $parts[0];
 if (isset($parts[1]))
  $seconds += (int) $parts[1] * 60;
 if (isset($parts[2]))
  $seconds += (int) $parts[2] * 3600;

 return $seconds;
}

// --- GET ID AND FETCH SONG DATA (Pre-populate form) ---
$sid = $_GET['sid'] ?? 0;
if (!$sid) {
 echo "<script>alert('Invalid song ID'); window.location.href='view_songs.php';</script>";
 exit;
}

$songQuery = mysqli_query($con, "SELECT * FROM song WHERE sid = '$sid'");
$song = mysqli_fetch_assoc($songQuery);
if (!$song) {
 echo "<script>alert('Song not found'); window.location.href='view_songs.php';</script>";
 exit;
}

$displayDurationSeconds = timeStringToSeconds($song['length']);


// --- API DATA FETCHING (For Dropdowns) ---
$genreApiUrl = "http://localhost/SIngIT/flutter_crud/getGenres.php";
$artistApiUrl = "http://localhost/SIngIT/flutter_crud/getArtists.php";
$languageApiUrl = "http://localhost/SIngIT/flutter_crud/getLanguage.php";

$genreOptions = json_decode(@file_get_contents($genreApiUrl) ?: '[]', true);
$artistOptions = json_decode(@file_get_contents($artistApiUrl) ?: '[]', true);
$languageOptions = json_decode(@file_get_contents($languageApiUrl) ?: '[]', true);


// --- MAPPING FETCHING (Using direct DB queries to pre-select dropdowns) ---
$selectedArtists = [];
$selectedGenres = [];
$selectedLanguages = [];

$artistMap = mysqli_query($con, "SELECT artist_id FROM artist_song WHERE song_id = '$sid'");
while ($row = mysqli_fetch_assoc($artistMap)) {
 $selectedArtists[] = $row['artist_id'];
}

$genreMap = mysqli_query($con, "SELECT genre_id FROM genre_song WHERE song_id = '$sid'");
while ($row = mysqli_fetch_assoc($genreMap)) {
 $selectedGenres[] = $row['genre_id'];
}

$languageMap = mysqli_query($con, "SELECT language_id FROM language_song WHERE song_id = '$sid'");
while ($row = mysqli_fetch_assoc($languageMap)) {
 $selectedLanguages[] = $row['language_id'];
}
?>

<style>
/* --- CUSTOM STYLES (FROM ADD_SONG) --- */
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

.form-control,
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

/* Custom Dropdown Styles */
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
        <h2 class="main-content-title tx-24 mg-b-5">Edit Song</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Song</li>
        </ol>
    </div>
</div>

<div class="col-md-6 m-auto d-block">
    <form method="POST" action="http://localhost/SIngIT/flutter_crud/editSong.php" enctype="multipart/form-data"    
        id="form1" class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">

        <input type="hidden" name="sid" value="<?= $sid ?>">

        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Edit Song (ID: <?= $sid ?>)</strong>
        </h1>
        <br />

        <div class="form-group">
            <h5>Track Name:</h5>
            <input type="text" name="trackName" id="trackName" class="form-control input"    
                value="<?php echo htmlspecialchars($song['name']); ?>">
            <span class="error" id="trackNameError"></span>
        </div>

        <div class="form-group">
            <h5>Genre(s):</h5>
            <input type="hidden" name="genreIdList" id="selected_genre_id_list"    
                value="<?= implode(',', $selectedGenres) ?>">

            <div id="custom-dropdown-header-genre">
                <span id="selectionSummaryGenre"><?= count($selectedGenres) ?> Selected</span>
                <i class="uil uil-angle-down"></i>
            </div>
            <div id="custom-dropdown-list-genre" class="custom-dropdown-list-style">
                <?php foreach ($genreOptions as $genre):
     $image_src = htmlspecialchars($genre['image'] ?? $default_img_path);
     $checked = in_array($genre['gid'], $selectedGenres) ? 'checked' : '';
     ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $genre['gid'] ?>"    
                        data-name="<?= htmlspecialchars($genre['name']) ?>" class="genre-checkbox" <?= $checked ?>>
                    <img src="<?= $image_src ?>" alt="" title="<?= htmlspecialchars($genre['name']) ?>"    
                        class="dropdown-img genre-lang-img">
                    <span><?= htmlspecialchars($genre['name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <span class="error" id="genreError"></span>
        </div>

        <div class="form-group">
            <h5>Artist(s):</h5>
            <input type="hidden" name="artistIdList" id="selected_artist_id_list"    
                value="<?= implode(',', $selectedArtists) ?>">

            <div id="custom-dropdown-header-artist">
                <span id="selectionSummaryArtist"><?= count($selectedArtists) ?> Selected</span>
                <i class="uil uil-angle-down"></i>
            </div>
            <div id="custom-dropdown-list-artist" class="custom-dropdown-list-style">
                <?php foreach ($artistOptions as $artist):
     $image_src = htmlspecialchars($artist['photo'] ?? $default_img_path);
     $checked = in_array($artist['arid'], $selectedArtists) ? 'checked' : '';
     ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $artist['arid'] ?>"    
                        data-name="<?= htmlspecialchars($artist['name']) ?>" class="artist-checkbox" <?= $checked ?>>
                    <img src="<?= $image_src ?>" alt="" title="<?= htmlspecialchars($artist['name']) ?>"    
                        class="dropdown-img artist-img">
                    <span><?= htmlspecialchars($artist['name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <span class="error" id="artistError"></span>
        </div>

        <div class="form-group">
            <h5>Language(s):</h5>
            <input type="hidden" name="languageIdList" id="selected_language_id_list"    
                value="<?= implode(',', $selectedLanguages) ?>">

            <div id="custom-dropdown-header-language">
                <span id="selectionSummaryLanguage"><?= count($selectedLanguages) ?> Selected</span>
                <i class="uil u1il-angle-down"></i>
            </div>
            <div id="custom-dropdown-list-language" class="custom-dropdown-list-style">
                <?php foreach ($languageOptions as $language):
     $image_src = htmlspecialchars($language['image'] ?? $default_img_path);
     $checked = in_array($language['lid'], $selectedLanguages) ? 'checked' : '';
     ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $language['lid'] ?>"    
                        data-name="<?= htmlspecialchars($language['name']) ?>" class="language-checkbox"    
                        <?= $checked ?>>
                    <img src="<?= $image_src ?>" alt="" title="<?= htmlspecialchars($language['name']) ?>"    
                        class="dropdown-img genre-lang-img">
                    <span><?= htmlspecialchars($language['name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <span class="error" id="languageError"></span>
        </div>

        <center>
            <button type="button" class="btn btn-outline-success mt-2 btn-lg input"    
                onclick="fetchLyrics()">Auto-Fill</button>
        </center>

        <div class="form-group">
            <h5>Album Name:</h5>
            <input type="text" name="albumName" id="albumName" class="form-control input"    
                value="<?php echo htmlspecialchars($song['album']); ?>">
            <span class="error" id="albumError"></span>
        </div>

        <div class="form-group">
            <h5>Duration (seconds):</h5>
            <input type="text" name="duration" id="duration" class="form-control input"    
                value="<?= $displayDurationSeconds ?>">
            <span class="error" id="durationError"></span>
        </div>

        <div class="form-group">
            <h5>Lyrics:</h5>
            <textarea name="syncedLyrics" id="syncedLyrics" rows="4" class="form-control input"    
                style="padding-top: 10px;"><?= htmlspecialchars($song['lyrics']) ?></textarea>
            <span class="error" id="lyricsError"></span>
        </div>

        <div id="manualImageUpload">
            <h5>Upload Album Image (Manual):</h5>
            <input type="file" id="manualImage" name="manualImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_in">CHOOSE A FILE</button>
            <span id="custom-text_in">Change file?</span>
            <span class="error" id="imageError"></span>
            <br>
        </div>

        <div class="form-group">
            <input type="hidden" name="albumImageUrl" id="albumImageUrl"    
                value="<?php echo htmlspecialchars($song['image']); ?>">

            <img id="albumImagePreview" src="<?php echo htmlspecialchars($song['image']); ?>"    
                style="height:80px; width:80px; object-fit:cover; border-radius:8px; margin-top:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);">
        </div>

        <div class="form-group">
            <h5>Upload Poster Image:</h5>
            <input type="file" id="posterImage" name="posterImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_poster">CHOOSE A FILE</button>
            <span id="custom-text_poster">Change file?</span>
            <span class="error" id="posterImageError"></span>
        </div>

        <div class="form-group">
            <?php if (!empty($song['poster'])): ?>
            <img id="posterImagePreview" src="<?= htmlspecialchars($song['poster']) ?>"        
                style="height:80px; width:80px; object-fit:cover; border-radius:8px; margin-top:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);">
            <?php endif; ?>
        </div>

        <h5 class="mt-3">Upload Vocal Audio:</h5>
        <input type="file" id="audio_vocal" name="audio_vocal" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_vocal">CHOOSE A FILE</button>
        <span id="custom-text_vocal">Change file?</span>
        <span class="error" id="audioVocalError"></span>

        <audio controls class="mt-2" style="height:30px;">

               
            <source src="<?= $song['vocal'] ?>" type="audio/mpeg">

               
        </audio>
        <br />

        <h5 class="mt-3">Upload Instrumental Audio:</h5>
        <input type="file" id="audio_i" name="audio_i" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_i">CHOOSE A FILE</button>
        <span id="custom-text_i">Change file?</span>
        <span class="error" id="audioIError"></span>
        <audio controls class="mt-2" style="height:30px;">

               
            <source src="<?= $song['instrumental'] ?>" type="audio/mpeg">

               
        </audio>
        <br />
        <br />

        <center>
            <button type="submit" name="update" class="btn btn-outline-primary btn-lg input">Update</button>
        </center>
    </form>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// --- SweetAlert Helper Functions (Same as Insert form) ---

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

// --- Custom Multi-Select Logic (Same as before) ---

function setupCustomDropdown(headerId, listId, checkboxClass, summaryId, hiddenInputId, errorId) {
    const dropdownHeader = document.getElementById(headerId);
    const dropdownList = document.getElementById(listId);
    const selectionSummary = document.getElementById(summaryId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const selectionError = document.getElementById(errorId);

    let selectedIds = hiddenInput.value.split(',').filter(id => id.trim() !== '');
    selectionSummary.textContent = `${selectedIds.length} Selected`;

    dropdownHeader.addEventListener('click', () => {
        $(dropdownList).slideToggle(200);
        selectionError.style.display = 'none';
    });

    $(dropdownList).on('change', `.${checkboxClass}`, function() {
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
        if (selectedIds.length > 0) selectionError.style.display = 'none';
    });

    return hiddenInput;
}

const genreInput = setupCustomDropdown('custom-dropdown-header-genre', 'custom-dropdown-list-genre', 'genre-checkbox',
    'selectionSummaryGenre', 'selected_genre_id_list', 'genreError');
const artistInput = setupCustomDropdown('custom-dropdown-header-artist', 'custom-dropdown-list-artist',
    'artist-checkbox', 'selectionSummaryArtist', 'selected_artist_id_list', 'artistError');
const languageInput = setupCustomDropdown('custom-dropdown-header-language', 'custom-dropdown-list-language',
    'language-checkbox', 'selectionSummaryLanguage', 'selected_language_id_list', 'languageError');


// --- Custom File Button Logic (Same as before) ---

const customFileSetup = (fileId, buttonId, textId, previewId = null) => {
    const realFileBtn = document.getElementById(fileId);
    const customBtn = document.getElementById(buttonId);
    const customTxt = document.getElementById(textId);
    const previewEl = previewId ? document.getElementById(previewId) : null;

    const initialText = customTxt.innerHTML;

    customBtn.addEventListener("click", function() {
        realFileBtn.click();
    });

    realFileBtn.addEventListener("change", function() {
        if (realFileBtn.files.length > 0) {
            customTxt.innerHTML = realFileBtn.files[0].name;
            if (previewEl && fileId !== 'audio_vocal' && fileId !== 'audio_i') {
                previewEl.src = URL.createObjectURL(realFileBtn.files[0]);
                previewEl.style.display = "block";
            }
        } else {
            customTxt.innerHTML = initialText;

            if (fileId === 'manualImage') {
                const existingUrl = document.getElementById('albumImageUrl').value;
                document.getElementById("albumImagePreview").src = existingUrl;
            } else if (fileId === 'posterImage') {
                const existingUrl = '<?= $song['poster'] ?>';
                document.getElementById("posterImagePreview").src = existingUrl;
            }
        }
    });
};

customFileSetup("audio_vocal", "custom-button_vocal", "custom-text_vocal");
customFileSetup("audio_i", "custom-button_i", "custom-text_i");
customFileSetup("manualImage", "custom-button_in", "custom-text_in", "albumImagePreview");
customFileSetup("posterImage", "custom-button_poster", "custom-text_poster", "posterImagePreview");

// Auto-Fill Function (Calls external API: lyrics_fetch.php)
async function fetchLyrics() {
    const artistInput = document.getElementById("selected_artist_id_list");
    const track = document.getElementById("trackName").value;

    // 1. Pre-API Validation using SweetAlert
    if (artistInput.value.trim().length === 0 || !track) {
        showErrorAlert("Please select at least one artist and enter track name before using Auto-Fill.");
        return;
    }

    const artistIds = artistInput.value.split(',');
    const firstArtistId = artistIds[0];
    const artistElement = document.querySelector(`.artist-checkbox[value="${firstArtistId}"]`);
    const selectedArtist = artistElement ? artistElement.dataset.name.trim() : '';

    if (!selectedArtist) {
        showErrorAlert("Could not determine artist name for auto-fill.");
        return;
    }

    try {
        const res = await fetch(
            `lyrics_fetch.php?artist=${encodeURIComponent(selectedArtist)}&track=${encodeURIComponent(track)}`
        );
        const data = await res.json();

        if (data.status === "found") {
            // Set values, using || '' to ensure null values don't break the code
            document.getElementById("albumName").value = data.albumName || '';
            document.getElementById("duration").value = data.duration || '';

            // ✅ Set Lyrics
            document.getElementById("syncedLyrics").value = data.syncedLyrics || '';

            document.getElementById("albumImageUrl").value = data.albumImage || '';

            const preview = document.getElementById("albumImagePreview");
            preview.src = data.albumImage;
            preview.style.display = "block";

            // Manual Upload section છુપાવો
            const manualUploadSection = document.getElementById("manualImageUpload");
            if (manualUploadSection) {
                manualUploadSection.style.display = "none";
            }

            showSuccessAlert("Track details auto-filled successfully!");

        } else {
            // If Auto-fill fails, ensure manual image upload section is visible
            document.getElementById("albumImagePreview").style.display = "block";

            // Manual Upload section બતાવો
            const manualUploadSection = document.getElementById("manualImageUpload");
            if (manualUploadSection) {
                manualUploadSection.style.display = "block";
            }

            document.getElementById("albumImageUrl").value = "";

            showErrorAlert("No song data found! Please insert song details manually.");
        }
    } catch (err) {
        showErrorAlert("❌ Error fetching lyrics: " + err.message);
    }
}


// --- Form Submission Logic (Intercepts form submit and uses native POST) ---
document.getElementById("form1").addEventListener("submit", function(e) {
    // ⚠️ IMPORTANT: We remove e.preventDefault() here so the form submits normally if valid.
    let valid = true;

    // --- Element Definitions ---
    const trackName = document.getElementById("trackName");
    const albumName = document.getElementById("albumName");
    const duration = document.getElementById("duration");
    const lyrics = document.getElementById("syncedLyrics");
    const audioVocal = document.getElementById("audio_vocal");
    const audioI = document.getElementById("audio_i");
    const manualImage = document.getElementById("manualImage");
    const posterImage = document.getElementById("posterImage");
    const albumImageUrl = document.getElementById("albumImageUrl");

    // Existing URL variables injected via PHP
    const existingVocal = '<?= $song['vocal'] ?>';
    const existingInstrumental = '<?= $song['instrumental'] ?>';
    const existingPoster = '<?= $song['poster'] ?>';

    const trackNameError = document.getElementById("trackNameError");
    const genreError = document.getElementById("genreError");
    const artistError = document.getElementById("artistError");
    const languageError = document.getElementById("languageError");
    const albumError = document.getElementById("albumError");
    const durationError = document.getElementById("durationError");
    const lyricsError = document.getElementById("lyricsError");
    const imageError = document.getElementById("imageError");
    const audioVocalError = document.getElementById("audioVocalError");
    const audioIError = document.getElementById("audioIError");
    const posterImageError = document.getElementById("posterImageError");


    // Clear previous errors
    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".error").forEach(el => el.style.display = 'none');


    // 1. Validate Track Name
    if (trackName.value.trim() === "") {
        trackNameError.textContent = "⚠️ Track name is required.";
        trackNameError.style.display = 'block';
        valid = false;
    }

    // 2. Validate Dropdowns
    if (genreInput.value.trim() === "") {
        genreError.textContent = "⚠️ Select at least one genre.";
        genreError.style.display = 'block';
        valid = false;
    }
    if (artistInput.value.trim() === "") {
        artistError.textContent = "⚠️ Select at least one artist.";
        artistError.style.display = 'block';
        valid = false;
    }
    if (languageInput.value.trim() === "") {
        languageError.textContent = "⚠️ Select at least one language.";
        languageError.style.display = 'block';
        valid = false;
    }


    // 3. Validate Album Name
    if (albumName.value.trim() === "") {
        albumError.textContent = "⚠️ Album name is required.";
        albumError.style.display = 'block';
        valid = false;
    }

    // 4. Validate Duration
    const durValue = duration.value.trim();
    if (durValue === "" || isNaN(durValue) || parseInt(durValue) <= 0) {
        durationError.textContent = "⚠️ Enter valid duration (in seconds).";
        durationError.style.display = 'block';
        valid = false;
    }

    // 5. Validate Lyrics
    if (lyrics.value.trim() === "") {
        lyricsError.textContent = "⚠️ Lyrics are required.";
        lyricsError.style.display = 'block';
        valid = false;
    }

    // 6. 🖼️ ALBUM IMAGE: New file OR existing URL
    if (albumImageUrl.value.trim() === "" && manualImage.files.length === 0) {
        imageError.textContent = "⚠️ Album Image is required (must be auto-filled or uploaded manually).";
        imageError.style.display = 'block';
        valid = false;
    }

    // 7. 🖼️ POSTER IMAGE: New file OR existing URL (FIXED)
    if (posterImage.files.length === 0 && existingPoster.trim() === '') {
        posterImageError.textContent = "⚠️ Upload Poster Image is required.";
        posterImageError.style.display = 'block';
        valid = false;
    }

    // 8. VOCAL AUDIO: New file OR existing URL (FIXED)
    if (audioVocal.files.length === 0 && existingVocal.trim() === '') {
        audioVocalError.textContent = "⚠️ Upload vocal audio is required.";
        audioVocalError.style.display = 'block';
        valid = false;
    }

    // 9. INSTRUMENTAL AUDIO: New file OR existing URL (FIXED)
    if (audioI.files.length === 0 && existingInstrumental.trim() === '') {
        audioIError.textContent = "⚠️ Upload instrumental audio is required.";
        audioIError.style.display = 'block';
        valid = false;
    }


    // File Type Validation (Optional)
    const validateFile = (input, allowedTypes, errorId, label) => {
        if (input.files.length > 0) {
            const type = input.files[0].type;
            if (!allowedTypes.includes(type)) {
                document.getElementById(errorId).textContent = `⚠️ Invalid ${label} format.`;
                valid = false;
            }
        }
    };

    validateFile(audioVocal, ['audio/mpeg', 'audio/mp3', 'audio/wav'], "audioVocalError", "vocal audio");
    validateFile(audioI, ['audio/mpeg', 'audio/mp3', 'audio/wav'], "audioIError", "instrumental audio");
    validateFile(manualImage, ['image/jpeg', 'image/png', 'image/webp'], "imageError", "album image");
    validateFile(posterImage, ['image/jpeg', 'image/png', 'image/webp'], "posterImageError", "poster image");


    if (!valid) {
        // If validation fails, prevent default form submission
        e.preventDefault();
        return;
    }

    // If valid, the form submits normally to the PHP action URL.
});
</script>

<?php include('fff.php'); ?>
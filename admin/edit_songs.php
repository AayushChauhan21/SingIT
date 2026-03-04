<?php

include("connection.php");
include('demo.php');
include('hhh.php'); 

// Default image path (if DB fields are null)
$default_img_path = 'favicon_1.png';

// --- Utility Functions ---
function timeStringToSeconds($timeString)
{
    $parts = explode(':', $timeString);
    $parts = array_reverse($parts);

    $seconds = 0;
    if (isset($parts[0])) $seconds += (int) $parts[0];
    if (isset($parts[1])) $seconds += (int) $parts[1] * 60;
    if (isset($parts[2])) $seconds += (int) $parts[2] * 3600;

    return $seconds;
}

// --- GET ID AND FETCH SONG DATA ---
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


// --- API DATA FETCHING (Dynamic URL Update) ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SIngIT/flutter_crud/";

$genreApiUrl = $base_url . "getGenres.php";
$artistApiUrl = $base_url . "getArtists.php";
$languageApiUrl = $base_url . "getLanguage.php";

$genreOptions = json_decode(@file_get_contents($genreApiUrl) ?: '[]', true);
$artistOptions = json_decode(@file_get_contents($artistApiUrl) ?: '[]', true);
$languageOptions = json_decode(@file_get_contents($languageApiUrl) ?: '[]', true);


// --- MAPPING FETCHING ---
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
/* --- STYLES --- */
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

.error {
    color: red;
    font-size: 14px;
    margin-top: 5px;
    display: none;
}

#custom-dropdown-header-genre,
#custom-dropdown-header-artist,
#custom-dropdown-header-language {
    border: 1px solid rgba(0, 0, 0, 0.15);
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-dropdown-list-style {
    border: 1px solid rgba(0, 0, 0, 0.15);
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
    cursor: pointer;
}

.checkbox-item input[type="checkbox"] {
    margin-right: 10px;
    width: 18px;
    height: 18px;
    accent-color: #AA62C7;
}

.dropdown-img {
    margin-right: 10px;
    border-radius: 3px;
    object-fit: cover;
}

.artist-img { height: 40px; width: 30px; }
.genre-lang-img { height: 30px; width: 40px; }

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
    <form method="POST" action="../flutter_crud/editSong.php" enctype="multipart/form-data"    
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
                    <input type="checkbox" value="<?= $genre['gid'] ?>" class="genre-checkbox" <?= $checked ?>>
                    <img src="<?= $image_src ?>" class="dropdown-img genre-lang-img">
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
                    <input type="checkbox" value="<?= $artist['arid'] ?>" data-name="<?= htmlspecialchars($artist['name']) ?>" class="artist-checkbox" <?= $checked ?>>
                    <img src="<?= $image_src ?>" class="dropdown-img artist-img">
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
                <i class="uil uil-angle-down"></i>
            </div>
            <div id="custom-dropdown-list-language" class="custom-dropdown-list-style">
                <?php foreach ($languageOptions as $language):
                    $image_src = htmlspecialchars($language['image'] ?? $default_img_path);
                    $checked = in_array($language['lid'], $selectedLanguages) ? 'checked' : '';
                ?>
                <label class="checkbox-item">
                    <input type="checkbox" value="<?= $language['lid'] ?>" class="language-checkbox" <?= $checked ?>>
                    <img src="<?= $image_src ?>" class="dropdown-img genre-lang-img">
                    <span><?= htmlspecialchars($language['name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <span class="error" id="languageError"></span>
        </div>

        <center>
            <button type="button" class="btn btn-outline-success mt-2 btn-lg input" onclick="fetchLyrics()">Auto-Fill</button>
        </center>

        <div class="form-group">
            <h5>Album Name:</h5>
            <input type="text" name="albumName" id="albumName" class="form-control input" value="<?php echo htmlspecialchars($song['album']); ?>">
            <span class="error" id="albumError"></span>
        </div>

        <div class="form-group">
            <h5>Duration (seconds):</h5>
            <input type="text" name="duration" id="duration" class="form-control input" value="<?= $displayDurationSeconds ?>">
            <span class="error" id="durationError"></span>
        </div>

        <div class="form-group">
            <h5>Lyrics:</h5>
            <textarea name="syncedLyrics" id="syncedLyrics" rows="4" class="form-control input"><?= htmlspecialchars($song['lyrics']) ?></textarea>
            <span class="error" id="lyricsError"></span>
        </div>

        <div id="manualImageUpload">
            <h5>Upload Album Image (Manual):</h5>
            <input type="file" id="manualImage" name="manualImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_in">CHOOSE A FILE</button>
            <span id="custom-text_in">Change file?</span>
            <span class="error" id="imageError"></span>
        </div>

        <div class="form-group">
            <input type="hidden" name="albumImageUrl" id="albumImageUrl" value="<?php echo htmlspecialchars($song['image']); ?>">
            <img id="albumImagePreview" src="<?php echo htmlspecialchars($song['image']); ?>" style="height:80px; width:80px; object-fit:cover; border-radius:8px; margin-top:10px;">
        </div>

        <div class="form-group">
            <h5>Upload Poster Image:</h5>
            <input type="file" id="posterImage" name="posterImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_poster">CHOOSE A FILE</button>
            <span id="custom-text_poster">Change file?</span>
            <span class="error" id="posterImageError"></span>
        </div>

        <div class="form-group">
            <img id="posterImagePreview" src="<?= htmlspecialchars($song['poster']) ?>" style="height:80px; width:80px; object-fit:cover; border-radius:8px; margin-top:10px;">
        </div>

        <h5 class="mt-3">Upload Vocal Audio:</h5>
        <input type="file" id="audio_vocal" name="audio_vocal" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_vocal">CHOOSE A FILE</button>
        <span id="custom-text_vocal">Change file?</span>
        <span class="error" id="audioVocalError"></span>
        <audio controls class="mt-2" style="height:30px;"><source src="<?= $song['vocal'] ?>" type="audio/mpeg"></audio>

        <h5 class="mt-3">Upload Instrumental Audio:</h5>
        <input type="file" id="audio_i" name="audio_i" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_i">CHOOSE A FILE</button>
        <span id="custom-text_i">Change file?</span>
        <span class="error" id="audioIError"></span>
        <audio controls class="mt-2" style="height:30px;"><source src="<?= $song['instrumental'] ?>" type="audio/mpeg"></audio>
        <br><br>

        <center>
            <button type="submit" name="update" class="btn btn-outline-primary btn-lg input">Update</button>
        </center>
    </form>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/* JS functions for dropdowns and file previews go here (same logic as before) */
function setupCustomDropdown(headerId, listId, checkboxClass, summaryId, hiddenInputId, errorId) {
    const dropdownHeader = document.getElementById(headerId);
    const dropdownList = document.getElementById(listId);
    const selectionSummary = document.getElementById(summaryId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const selectionError = document.getElementById(errorId);

    let selectedIds = hiddenInput.value.split(',').filter(id => id.trim() !== '');

    dropdownHeader.addEventListener('click', () => { $(dropdownList).slideToggle(200); });

    $(dropdownList).on('change', `.${checkboxClass}`, function() {
        const id = this.value;
        if (this.checked) { if (!selectedIds.includes(id)) selectedIds.push(id); }
        else { selectedIds = selectedIds.filter(s => s !== id); }
        hiddenInput.value = selectedIds.join(',');
        selectionSummary.textContent = `${selectedIds.length} Selected`;
    });
}

setupCustomDropdown('custom-dropdown-header-genre', 'custom-dropdown-list-genre', 'genre-checkbox', 'selectionSummaryGenre', 'selected_genre_id_list', 'genreError');
setupCustomDropdown('custom-dropdown-header-artist', 'custom-dropdown-list-artist', 'artist-checkbox', 'selectionSummaryArtist', 'selected_artist_id_list', 'artistError');
setupCustomDropdown('custom-dropdown-header-language', 'custom-dropdown-list-language', 'language-checkbox', 'selectionSummaryLanguage', 'selected_language_id_list', 'languageError');

// File previews
const customFileSetup = (fileId, buttonId, textId, previewId = null) => {
    const realFileBtn = document.getElementById(fileId);
    const customBtn = document.getElementById(buttonId);
    const customTxt = document.getElementById(textId);
    customBtn.addEventListener("click", () => realFileBtn.click());
    realFileBtn.addEventListener("change", () => {
        if (realFileBtn.files.length > 0) {
            customTxt.innerHTML = realFileBtn.files[0].name;
            if (previewId) document.getElementById(previewId).src = URL.createObjectURL(realFileBtn.files[0]);
        }
    });
};

customFileSetup("audio_vocal", "custom-button_vocal", "custom-text_vocal");
customFileSetup("audio_i", "custom-button_i", "custom-text_i");
customFileSetup("manualImage", "custom-button_in", "custom-text_in", "albumImagePreview");
customFileSetup("posterImage", "custom-button_poster", "custom-text_poster", "posterImagePreview");
</script>

<?php include('fff.php'); ?>
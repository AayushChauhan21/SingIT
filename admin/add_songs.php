<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

$default_img_path = 'favicon_1.png';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseApiUrl = $protocol . $host . "/SingIT/flutter_crud/";

// 1. Fetch Genre Options
$genreApiUrl = $baseApiUrl . "getGenres.php";
$genreOptions = [];
$response = @file_get_contents($genreApiUrl);
if ($response !== FALSE) {
    $genreOptions = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($genreOptions)) {
        $genreOptions = [];
    }
}

// 2. Fetch Artist Options
$artistApiUrl = $baseApiUrl . "getArtists.php";
$artistOptions = [];
$response = @file_get_contents($artistApiUrl);
if ($response !== FALSE) {
    $artistOptions = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($artistOptions)) {
        $artistOptions = [];
    }
}

// 3. Fetch Language Options
$languageApiUrl = $baseApiUrl . "getLanguage.php";
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

/* --- MASTER SUBMIT CONTROL SYSTEM TERMINALS --- */
.btn-music-master-deploy {
    background-color: var(--accent-neon) !important;
    color: var(--text-white) !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    padding: 0.85rem 3.5rem !important;
    border-radius: 10px !important;
    border: none !important;
    /* box-shadow: 0 4px 20px var(--accent-neon-glow) !important; */
    transition: var(--transition-music) !important;
}

.btn-music-master-deploy:hover {
    transform: translateY(-2px);
    /* box-shadow: 0 8px 25px var(--accent-neon-glow) !important; */
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


<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Add Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Songs</li>
        </ol>
    </div>
</div>


<div class="music-container px-3">
    <form action="../flutter_crud/addSongs.php" method="post" enctype="multipart/form-data"     id="form1"
        class="mb-4 font-weight-bold border bg-white p-5 shadow music-form-panel">

        <div
            class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
            <h1 class="music-heading mb-0">Add new item</h1>
            <button type="button" class="btn btn-music-autofill" onclick="fetchLyrics()">
                <i class="uil uil-bolt me-1"></i> Auto-Fill
            </button>
        </div>

        <!-- TWO-COLUMN MAIN WORKSPACE FRAME -->
        <div class="row g-4">

            <!-- LEFT WORKSPACE AREA: VISUAL ALBUM IMAGES -->
            <div class="col-xl-4 col-lg-5 col-md-12">
                <div class="d-flex flex-column gap-4">

                    <!-- Album Cover Node (Perfect 1:1 Square) -->
                    <div>
                        <div class="field-title">Album Cover Matrix Preview</div>
                        <div class="music-media-uploader-frame" id="trigger_manualImage">
                            <img id="albumImagePreview" src="" class="uploader-absolute-preview-img"
                                style="display: none;" alt="Album Artwork Preview" crossorigin="anonymous">
                            <span class="uploader-placeholder-text" id="albumPlaceholderText">
                                <i class="uil uil-scenery d-block fs-2 mb-2"></i>
                                Upload cover art<br><small class="text-muted">(Auto-filled or Manual)</small>
                            </span>
                        </div>
                        <input type="hidden" name="albumImageUrl" id="albumImageUrl">
                        <input type="file" id="manualImage" name="manualImage" accept="image/*" class="d-none" />
                        <span class="error" id="imageError"></span>
                    </div>

                    <!-- Marketing Distribution Poster Node Block (16:9 Landscape Layout) -->
                    <div>
                        <div class="field-title">Distribution Poster Layer</div>
                        <div class="music-media-uploader-frame" id="trigger_posterImage" style="aspect-ratio: 16 / 9;">
                            <img id="posterImagePreview" src="" class="uploader-absolute-preview-img"
                                style="display: none;" alt="Poster Graphic Preview" crossorigin="anonymous">
                            <span class="uploader-placeholder-text" id="posterPlaceholderText">
                                <i class="uil uil-image d-block fs-2 mb-2"></i>
                                Upload landscape poster
                            </span>
                        </div>
                        <input type="file" id="posterImage" name="posterImage" accept="image/*" class="d-none" />
                        <span class="error" id="posterImageError"></span>
                    </div>

                </div>
            </div>

            <!-- RIGHT WORKSPACE AREA: INPUT FORM CONTROLS GRID FIELDS & AUDIO STEM UPLOADERS -->
            <div class="col-xl-8 col-lg-7 col-md-12">
                <div class="row g-3">

                    <!-- Track Title Parameter Field -->
                    <div class="col-12">
                        <div class="field-title">Track Title Context</div>
                        <input type="text" name="trackName" id="trackName" placeholder="Enter Track Name Title..."
                            class="form-control music-input">
                        <span class="error" id="trackNameError"></span>
                    </div>

                    <!-- Album Context Field -->
                    <div class="col-md-8 col-sm-12">
                        <div class="field-title">Collection Album Name</div>
                        <input type="text" name="albumName" id="albumName" placeholder="Enter Collection Album Title..."
                            class="form-control music-input">
                        <span class="error" id="albumError"></span>
                    </div>

                    <!-- Temporal Metric Parameter Input -->
                    <div class="col-md-4 col-sm-12">
                        <div class="field-title">Duration (Seconds)</div>
                        <input type="text" name="duration" id="duration" placeholder="e.g. 240"
                            class="form-control music-input">
                        <span class="error" id="durationError"></span>
                    </div>

                    <!-- Genre Selection Stack Menu -->
                    <div class="col-md-4 col-sm-12 position-relative">
                        <div class="field-title">Genres Category Matrix</div>
                        <input type="hidden" name="genreId[]" id="selected_genre_id_list">
                        <div class="music-dropdown-trigger" id="custom-dropdown-header-genre">
                            <span id="selectionSummaryGenre" class="text-truncate">0 Selected</span>
                            <i class="uil uil-angle-down"></i>
                        </div>
                        <div id="custom-dropdown-list-genre" class="custom-dropdown-list-style">
                            <?php foreach ($genreOptions as $genre):
                                $image_src = htmlspecialchars($genre['image'] ?? $default_img_path); ?>
                            <label class="checkbox-item">
                                <input type="checkbox" value="<?= $genre['gid'] ?>"
                                    data-name="<?= htmlspecialchars($genre['name']) ?>" class="genre-checkbox">
                                <img src="<?= $image_src ?>" class="dropdown-img genre-lang-img" alt="">
                                <span><?= htmlspecialchars($genre['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <span class="error" id="genreError"></span>
                    </div>

                    <!-- Artist Mapping Menu Stack Selector -->
                    <div class="col-md-4 col-sm-12 position-relative">
                        <div class="field-title">Artists Mapping Matrix</div>
                        <input type="hidden" name="artistId[]" id="selected_artist_id_list">
                        <div class="music-dropdown-trigger" id="custom-dropdown-header-artist">
                            <span id="selectionSummaryArtist" class="text-truncate">0 Selected</span>
                            <i class="uil uil-angle-down"></i>
                        </div>
                        <div id="custom-dropdown-list-artist" class="custom-dropdown-list-style">
                            <?php foreach ($artistOptions as $artist):
                                $image_src = htmlspecialchars($artist['photo'] ?? $default_img_path); ?>
                            <label class="checkbox-item">
                                <input type="checkbox" value="<?= $artist['arid'] ?>"
                                    data-name="<?= htmlspecialchars($artist['name']) ?>" class="artist-checkbox">
                                <img src="<?= $image_src ?>" class="dropdown-img artist-img" alt="">
                                <span><?= htmlspecialchars($artist['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <span class="error" id="artistError"></span>
                    </div>

                    <!-- Languages Regional Selector Menu -->
                    <div class="col-md-4 col-sm-12 position-relative">
                        <div class="field-title">Languages Matrix</div>
                        <input type="hidden" name="languageId[]" id="selected_language_id_list">
                        <div class="music-dropdown-trigger" id="custom-dropdown-header-language">
                            <span id="selectionSummaryLanguage" class="text-truncate">0 Selected</span>
                            <i class="uil uil-angle-down"></i>
                        </div>
                        <div id="custom-dropdown-list-language" class="custom-dropdown-list-style">
                            <?php foreach ($languageOptions as $language):
                                $image_src = htmlspecialchars($language['image'] ?? $default_img_path); ?>
                            <label class="checkbox-item">
                                <input type="checkbox" value="<?= $language['lid'] ?>"
                                    data-name="<?= htmlspecialchars($language['name']) ?>" class="language-checkbox">
                                <img src="<?= $image_src ?>" class="dropdown-img genre-lang-img" alt="">
                                <span><?= htmlspecialchars($language['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <span class="error" id="languageError"></span>
                    </div>

                    <!-- Lyrical Content Workspace Block -->
                    <div class="col-12">
                        <div class="field-title">Synced Lyrics Verbatim Transcripts</div>
                        <textarea name="syncedLyrics" id="syncedLyrics" rows="4"
                            placeholder="Paste or synchronize track lyrical transcripts workspace..."
                            class="form-control music-input" style="min-height: 120px; resize: vertical;"></textarea>
                        <span class="error" id="lyricsError"></span>
                    </div>

                    <!-- Vocal Tracking File Container Node -->
                    <div class="col-md-12 col-sm-12">
                        <div class="field-title">Acapella Vocal Waveform Stem</div>
                        <div class="music-strip-bar-uploader">
                            <span class="music-filename-status-string" id="custom-text_vocal">No vocal file attached
                                yet.</span>
                            <button type="button" class="btn btn-music-action-trigger" id="custom-button_vocal">SELECT
                                STEM</button>
                        </div>
                        <input type="file" id="audio_vocal" name="audio_vocal" accept="audio/*" class="d-none" />
                        <span class="error" id="audioVocalError"></span>
                    </div>

                    <!-- Instrumental Audio Track Stream Selector -->
                    <div class="col-md-12 col-sm-12">
                        <div class="field-title">Instrumental Waveform Stem</div>
                        <div class="music-strip-bar-uploader">
                            <span class="music-filename-status-string" id="custom-text_i">No instrumental file attached
                                yet.</span>
                            <button type="button" class="btn btn-music-action-trigger" id="custom-button_i">SELECT
                                STEM</button>
                        </div>
                        <input type="file" id="audio_i" name="audio_i" accept="audio/*" class="d-none" />
                        <span class="error" id="audioIError"></span>
                    </div>

                </div>
            </div>

        </div>

        <!-- FINAL SUBMIT ACTION BLOCK -->
        <div class="text-center mt-5 pt-2">
            <input type="submit" value="Insert" name="insert" id="insert" class="btn btn-music-master-deploy shadow">
        </div>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function restoreScroll() {
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
        confirmButtonColor: '#2563eb',
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
        confirmButtonColor: '#ef4444',
        onClose: restoreScroll,
        allowOutsideClick: true,
        showConfirmButton: true
    }, callback);
}

async function fetchLyrics() {
    const artistSelect = document.getElementById("selected_artist_id_list").value;
    const track = document.getElementById("trackName").value;

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

    const res = await fetch(
        `lyrics_fetch.php?artist=${encodeURIComponent(firstArtistName)}&track=${encodeURIComponent(track)}`);
    const data = await res.json();

    if (data.status === "found") {
        document.getElementById("albumName").value = data.albumName;
        document.getElementById("duration").value = data.duration;
        document.getElementById("syncedLyrics").value = data.syncedLyrics;
        document.getElementById("albumImageUrl").value = data.albumImage;

        const previewImg = document.getElementById("albumImagePreview");
        const placeholderText = document.getElementById("albumPlaceholderText");

        // Safely fallback to favicon_1.png if auto-fill didn't provide any artwork URL
        if (data.albumImage && data.albumImage.trim() !== "") {
            previewImg.src = data.albumImage;
            previewImg.style.display = "block";
            placeholderText.style.display = "none";
        } else {
            previewImg.src = '<?= $default_img_path ?>';
            previewImg.style.display = "block";
            placeholderText.style.display = "none";
            document.getElementById("albumImageUrl").value = '<?= $default_img_path ?>';
        }

        document.getElementById("trackNameError").style.display = 'none';
        document.getElementById("albumError").style.display = 'none';
        document.getElementById("durationError").style.display = 'none';
        document.getElementById("lyricsError").style.display = 'none';
        document.getElementById("imageError").style.display = 'none';

        showSuccessAlert("Track details auto-filled successfully!");
    } else {
        const previewImg = document.getElementById("albumImagePreview");
        const placeholderText = document.getElementById("albumPlaceholderText");

        // Auto-fill failed: Set to default layout path seamlessly
        previewImg.src = '<?= $default_img_path ?>';
        previewImg.style.display = "block";
        placeholderText.style.display = "none";
        document.getElementById("albumImageUrl").value = '<?= $default_img_path ?>';

        showErrorAlert("No song data found! Setting album artwork container to default layout.");
    }
}

function setupCustomDropdown(headerId, listId, checkboxClass, summaryId, hiddenInputId, errorId) {
    const dropdownHeader = document.getElementById(headerId);
    const dropdownList = document.getElementById(listId);
    const selectionSummary = document.getElementById(summaryId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const selectionError = document.getElementById(errorId);

    let selectedIds = hiddenInput.value.split(',').filter(id => id.trim() !== '');

    dropdownHeader.addEventListener('click', (e) => {
        e.stopPropagation();
        $('.custom-dropdown-list-style').not($(dropdownList)).slideUp(150);
        $(dropdownList).slideToggle(200);
        selectionError.textContent = "";
        selectionError.style.display = 'none';
    });

    $(dropdownList).on('change', `.${checkboxClass}`, function() {
        const id = this.value;
        if (this.checked) {
            if (selectedIds.indexOf(id) === -1) selectedIds.push(id);
        } else {
            selectedIds = selectedIds.filter(s => s !== id);
        }

        hiddenInput.value = selectedIds.join(',');
        selectionSummary.textContent = selectedIds.length > 0 ? `${selectedIds.length} Selected` : "0 Selected";

        if (selectedIds.length > 0) {
            selectionError.textContent = "";
            selectionError.style.display = 'none';
        }
    });
}

setupCustomDropdown('custom-dropdown-header-genre', 'custom-dropdown-list-genre', 'genre-checkbox',
    'selectionSummaryGenre', 'selected_genre_id_list', 'genreError');
setupCustomDropdown('custom-dropdown-header-artist', 'custom-dropdown-list-artist', 'artist-checkbox',
    'selectionSummaryArtist', 'selected_artist_id_list', 'artistError');
setupCustomDropdown('custom-dropdown-header-language', 'custom-dropdown-list-language', 'language-checkbox',
    'selectionSummaryLanguage', 'selected_language_id_list', 'languageError');

window.addEventListener('click', () => {
    $('.custom-dropdown-list-style').slideUp(150);
});
$('.custom-dropdown-list-style').on('click', (e) => e.stopPropagation());

const linkContainerToInput = (frameId, inputId, previewImgId, placeholderId) => {
    const frame = document.getElementById(frameId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewImgId);
    const placeholder = document.getElementById(placeholderId);

    frame.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
};

linkContainerToInput('trigger_manualImage', 'manualImage', 'albumImagePreview', 'albumPlaceholderText');
linkContainerToInput('trigger_posterImage', 'posterImage', 'posterImagePreview', 'posterPlaceholderText');

document.getElementById("form1").addEventListener("submit", function(e) {
    let valid = true;

    const trackName = document.getElementById("trackName");
    const albumName = document.getElementById("albumName");
    const duration = document.getElementById("duration");
    const lyrics = document.getElementById("syncedLyrics");
    const audioVocal = document.getElementById("audio_vocal");
    const audioI = document.getElementById("audio_i");
    const albumImageUrl = document.getElementById("albumImageUrl");
    const manualImage = document.getElementById("manualImage");
    const posterImage = document.getElementById("posterImage");
    const imageError = document.getElementById("imageError");
    const posterImageError = document.getElementById("posterImageError");
    const audioVocalError = document.getElementById("audioVocalError");

    document.querySelectorAll(".error").forEach(el => {
        el.textContent = "";
        el.style.display = 'none';
    });

    if (trackName.value.trim() === "") {
        document.getElementById("trackNameError").textContent = "⚠️ Track name is required.";
        document.getElementById("trackNameError").style.display = "block";
        valid = false;
    }
    if (document.getElementById("selected_genre_id_list").value.trim() === "") {
        document.getElementById("genreError").textContent = "⚠️ Select at least one genre.";
        document.getElementById("genreError").style.display = "block";
        valid = false;
    }
    if (document.getElementById("selected_artist_id_list").value.trim() === "") {
        document.getElementById("artistError").textContent = "⚠️ Select at least one artist.";
        document.getElementById("artistError").style.display = "block";
        valid = false;
    }
    if (document.getElementById("selected_language_id_list").value.trim() === "") {
        document.getElementById("languageError").textContent = "⚠️ Select at least one language.";
        document.getElementById("languageError").style.display = "block";
        valid = false;
    }
    if (albumName.value.trim() === "") {
        document.getElementById("albumError").textContent = "⚠️ Album name is required.";
        document.getElementById("albumError").style.display = "block";
        valid = false;
    }
    if (duration.value.trim() === "" || isNaN(duration.value) || parseInt(duration.value) <= 0) {
        document.getElementById("durationError").textContent = "⚠️ Enter valid duration (in seconds).";
        document.getElementById("durationError").style.display = "block";
        valid = false;
    }
    if (lyrics.value.trim() === "") {
        document.getElementById("lyricsError").textContent = "⚠️ Lyrics are required.";
        document.getElementById("lyricsError").style.display = "block";
        valid = false;
    }

    // FIXED fallback: If both text fields and file inputs are completely blank, inject default asset target path instead of blocking submission
    if (albumImageUrl.value.trim() === "" && manualImage.files.length === 0) {
        albumImageUrl.value = '<?= $default_img_path ?>';
    }
    if (posterImage.files.length === 0) {
        // Automatically injects default avatar value for database processing safety context rules
        const dataTransfer = new ClipboardEvent('').clipboardData || new DataTransfer();
        document.getElementById("posterImageError").textContent =
            "⚠️ Upload landscape poster image is required.";
        document.getElementById("posterImageError").style.display = "block";
        valid = false;
    }

    if (audioVocal.files.length === 0) {
        audioVocalError.textContent = "⚠️ Upload vocal audio waveform stream file.";
        audioVocalError.style.display = "block";
        valid = false;
    }
    if (audioI.files.length === 0) {
        document.getElementById("audioIError").textContent = "⚠️ Upload instrumental audio track asset.";
        document.getElementById("audioIError").style.display = "block";
        valid = false;
    }

    if (!valid) e.preventDefault();
});

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
        input: "audio_vocal",
        error: "audioVocalError"
    },
    {
        input: "audio_i",
        error: "audioIError"
    },
    {
        input: "posterImage",
        error: "posterImageError"
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
            });
        }
    }
});

const customFileSetup = (fileId, buttonId, textId) => {
    const realFileBtn = document.getElementById(fileId);
    const customBtn = document.getElementById(buttonId);
    const customTxt = document.getElementById(textId);

    customBtn.addEventListener("click", () => realFileBtn.click());
    realFileBtn.addEventListener("change", () => {
        customTxt.innerHTML = realFileBtn.files.length > 0 ? realFileBtn.files[0].name :
            "No file chosen, yet.";
    });
};

customFileSetup("audio_vocal", "custom-button_vocal", "custom-text_vocal");
customFileSetup("audio_i", "custom-button_i", "custom-text_i");
</script>

<?php
include('fff.php');
?>
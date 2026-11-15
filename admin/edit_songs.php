<?php
include("connection.php");
include('demo.php');
include('hhh.php');

$default_img_path = 'favicon_1.png';

// --- Utility Functions ---
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

// --- API DATA FETCHING ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SingIT/flutter_crud/";

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

    /* Music colors from player */
    --music-green: #1ed760;
    --music-coral: #ff6b6b;
    --music-violet: #8b5cf6;
    --music-blue: #38bdf8;
    --music-muted: #9aa4b2;
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

.music-input {
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

.music-input::placeholder {
    color: rgba(255, 255, 255, 0.25) !important;
}

/* --- PERFECT SQUARE 1:1 RATIO FOR MUSIC ALBUM COVERS --- */
.music-media-uploader-frame {
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
    background-color: var(--accent-neon) !important;
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

/* ==========================================
   --- CUSTOM AUDIO PLAYER STYLING ---
   ========================================== */
.custom-audio-player {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(10, 12, 18, 0.8);
    padding: 10px 18px;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    width: 100%;
    margin-top: 8px;
    backdrop-filter: blur(10px);
}

.play-pause-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
    /* color: #fff; */
}

.play-pause-btn:hover {
    transform: scale(1.1);
}

.audio-progress {
    flex-grow: 1;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
}

.audio-progress-bar {
    height: 100%;
    width: 0%;
    position: relative;
    border-radius: 3px;
}

.progress-knob {
    position: absolute;
    right: -6px;
    top: -3px;
    width: 12px;
    height: 12px;
    background: #ffffff;
    border-radius: 50%;
    box-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
    transition: transform 0.1s;
}

.vocal-gradient {
    background: linear-gradient(45deg, var(--music-coral), var(--music-violet)) !important;
}

.inst-gradient {
    background: linear-gradient(45deg, var(--music-green), var(--music-blue)) !important;
}

.audio-time {
    font-size: 0.7rem;
    color: var(--music-muted);
    font-weight: 600;
    min-width: 35px;
    text-align: center;
}
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Edit Song</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_songs.php">Songs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Song</li>
        </ol>
    </div>
</div>

<div class="music-container px-3">
    <form action="../flutter_crud/editSong.php" method="post" enctype="multipart/form-data" id="form1"
        class="mb-4 font-weight-bold border bg-white p-5 shadow music-form-panel">

        <input type="hidden" name="sid" value="<?= $sid ?>">

        <div
            class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white border-opacity-5">
            <h1 class="music-heading mb-0">Edit Track (ID: <?= $sid ?>)</h1>
            <button type="button" class="btn btn-music-autofill" onclick="fetchLyrics()">
                <i class="uil uil-bolt me-1"></i> Auto-Fill
            </button>
        </div>

        <!-- TWO-COLUMN MAIN WORKSPACE FRAME -->
        <div class="row g-4">

            <!-- LEFT WORKSPACE AREA: VISUAL ALBUM IMAGES -->
            <div class="col-xl-4 col-lg-5 col-md-12">
                <div class="d-flex flex-column gap-4">

                    <!-- Album Cover Node -->
                    <div>
                        <div class="field-title">Album Cover Matrix Preview</div>
                        <div class="music-media-uploader-frame" id="trigger_manualImage">
                            <?php $hasAlbumImg = !empty($song['image']); ?>
                            <img id="albumImagePreview" src="<?= htmlspecialchars($song['image'] ?? '') ?>"
                                class="uploader-absolute-preview-img"
                                style="<?= $hasAlbumImg ? 'display: block;' : 'display: none;' ?>"
                                alt="Album Artwork Preview" crossorigin="anonymous">
                            <span class="uploader-placeholder-text" id="albumPlaceholderText"
                                style="<?= $hasAlbumImg ? 'display: none;' : 'display: block;' ?>">
                                <i class="uil uil-scenery d-block fs-2 mb-2"></i>
                                Upload cover art<br><small class="text-muted">(Auto-filled or Manual)</small>
                            </span>
                        </div>
                        <input type="hidden" name="albumImageUrl" id="albumImageUrl"
                            value="<?= htmlspecialchars($song['image'] ?? '') ?>">
                        <input type="file" id="manualImage" name="manualImage" accept="image/*" class="d-none" />
                        <span class="error" id="imageError"></span>
                    </div>

                    <!-- Poster Node -->
                    <div>
                        <div class="field-title">Distribution Poster Layer</div>
                        <div class="music-media-uploader-frame" id="trigger_posterImage" style="aspect-ratio: 16 / 9;">
                            <?php $hasPosterImg = !empty($song['poster']); ?>
                            <img id="posterImagePreview" src="<?= htmlspecialchars($song['poster'] ?? '') ?>"
                                class="uploader-absolute-preview-img"
                                style="<?= $hasPosterImg ? 'display: block;' : 'display: none;' ?>"
                                alt="Poster Graphic Preview" crossorigin="anonymous">
                            <span class="uploader-placeholder-text" id="posterPlaceholderText"
                                style="<?= $hasPosterImg ? 'display: none;' : 'display: block;' ?>">
                                <i class="uil uil-image d-block fs-2 mb-2"></i>
                                Upload landscape poster
                            </span>
                        </div>
                        <input type="file" id="posterImage" name="posterImage" accept="image/*" class="d-none" />
                        <span class="error" id="posterImageError"></span>
                    </div>

                </div>
            </div>

            <!-- RIGHT WORKSPACE AREA: INPUT FORM CONTROLS GRID -->
            <div class="col-xl-8 col-lg-7 col-md-12">
                <div class="row g-3">

                    <!-- Track Title -->
                    <div class="col-12">
                        <div class="field-title">Track Title Context</div>
                        <input type="text" name="trackName" id="trackName" placeholder="Enter Track Name Title..."
                            class="form-control music-input" value="<?= htmlspecialchars($song['name'] ?? '') ?>">
                        <span class="error" id="trackNameError"></span>
                    </div>

                    <!-- Genres Selector -->
                    <div class="col-md-4 col-sm-12 position-relative">
                        <div class="field-title">Genres Category Matrix</div>
                        <input type="hidden" name="genreIdList" id="selected_genre_id_list"
                            value="<?= implode(',', $selectedGenres) ?>">
                        <div class="music-dropdown-trigger" id="custom-dropdown-header-genre">
                            <span id="selectionSummaryGenre" class="text-truncate"><?= count($selectedGenres) ?>
                                Selected</span>
                            <i class="uil uil-angle-down"></i>
                        </div>
                        <div id="custom-dropdown-list-genre" class="custom-dropdown-list-style">
                            <?php foreach ($genreOptions as $genre):
                                $image_src = htmlspecialchars($genre['image'] ?? $default_img_path);
                                $checked = in_array($genre['gid'], $selectedGenres) ? 'checked' : ''; ?>
                            <label class="checkbox-item">
                                <input type="checkbox" value="<?= $genre['gid'] ?>"
                                    data-name="<?= htmlspecialchars($genre['name']) ?>" class="genre-checkbox"
                                    <?= $checked ?>>
                                <img src="<?= $image_src ?>" class="dropdown-img genre-lang-img" alt="">
                                <span><?= htmlspecialchars($genre['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <span class="error" id="genreError"></span>
                    </div>

                    <!-- Artists Selector -->
                    <div class="col-md-4 col-sm-12 position-relative">
                        <div class="field-title">Artists Mapping Matrix</div>
                        <input type="hidden" name="artistIdList" id="selected_artist_id_list"
                            value="<?= implode(',', $selectedArtists) ?>">
                        <div class="music-dropdown-trigger" id="custom-dropdown-header-artist">
                            <span id="selectionSummaryArtist" class="text-truncate"><?= count($selectedArtists) ?>
                                Selected</span>
                            <i class="uil uil-angle-down"></i>
                        </div>
                        <div id="custom-dropdown-list-artist" class="custom-dropdown-list-style">
                            <?php foreach ($artistOptions as $artist):
                                $image_src = htmlspecialchars($artist['photo'] ?? $default_img_path);
                                $checked = in_array($artist['arid'], $selectedArtists) ? 'checked' : ''; ?>
                            <label class="checkbox-item">
                                <input type="checkbox" value="<?= $artist['arid'] ?>"
                                    data-name="<?= htmlspecialchars($artist['name']) ?>" class="artist-checkbox"
                                    <?= $checked ?>>
                                <img src="<?= $image_src ?>" class="dropdown-img artist-img" alt="">
                                <span><?= htmlspecialchars($artist['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <span class="error" id="artistError"></span>
                    </div>

                    <!-- Languages Selector -->
                    <div class="col-md-4 col-sm-12 position-relative">
                        <div class="field-title">Languages Matrix</div>
                        <input type="hidden" name="languageIdList" id="selected_language_id_list"
                            value="<?= implode(',', $selectedLanguages) ?>">
                        <div class="music-dropdown-trigger" id="custom-dropdown-header-language">
                            <span id="selectionSummaryLanguage" class="text-truncate"><?= count($selectedLanguages) ?>
                                Selected</span>
                            <i class="uil uil-angle-down"></i>
                        </div>
                        <div id="custom-dropdown-list-language" class="custom-dropdown-list-style">
                            <?php foreach ($languageOptions as $language):
                                $image_src = htmlspecialchars($language['image'] ?? $default_img_path);
                                $checked = in_array($language['lid'], $selectedLanguages) ? 'checked' : ''; ?>
                            <label class="checkbox-item">
                                <input type="checkbox" value="<?= $language['lid'] ?>"
                                    data-name="<?= htmlspecialchars($language['name']) ?>" class="language-checkbox"
                                    <?= $checked ?>>
                                <img src="<?= $image_src ?>" class="dropdown-img genre-lang-img" alt="">
                                <span><?= htmlspecialchars($language['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <span class="error" id="languageError"></span>
                    </div>

                    <!-- Album Context -->
                    <div class="col-md-8 col-sm-12">
                        <div class="field-title">Collection Album Name</div>
                        <input type="text" name="albumName" id="albumName" placeholder="Enter Collection Album Title..."
                            class="form-control music-input" value="<?= htmlspecialchars($song['album'] ?? '') ?>">
                        <span class="error" id="albumError"></span>
                    </div>

                    <!-- Duration -->
                    <div class="col-md-4 col-sm-12">
                        <div class="field-title">Duration (Seconds)</div>
                        <input type="text" name="duration" id="duration" placeholder="e.g. 240"
                            class="form-control music-input" value="<?= $displayDurationSeconds ?>">
                        <span class="error" id="durationError"></span>
                    </div>

                    <!-- Lyrics -->
                    <div class="col-12">
                        <div class="field-title">Synced Lyrics Verbatim Transcripts</div>
                        <textarea name="syncedLyrics" id="syncedLyrics" rows="4"
                            placeholder="Paste or synchronize track lyrical transcripts workspace..."
                            class="form-control music-input"
                            style="min-height: 120px; resize: vertical;"><?= htmlspecialchars($song['lyrics'] ?? '') ?></textarea>
                        <span class="error" id="lyricsError"></span>
                    </div>

                    <!-- Vocal File with Custom Gradient Player -->
                    <div class="col-md-6 col-sm-12">
                        <div class="field-title">Acapella Vocal Waveform Stem</div>
                        <div class="music-strip-bar-uploader">
                            <span class="music-filename-status-string" id="custom-text_vocal">
                                <?= !empty($song['vocal']) ? '(Click to change)' : 'No vocal file attached yet.' ?>
                            </span>
                            <button type="button" class="btn btn-music-action-trigger" id="custom-button_vocal">CHANGE
                                STEM</button>
                        </div>
                        <input type="file" id="audio_vocal" name="audio_vocal" accept="audio/*" class="d-none" />

                        <?php if (!empty($song['vocal'])): ?>
                        <div class="custom-audio-player">
                            <button type="button" class="play-pause-btn vocal-gradient"
                                onclick="togglePlay('vocalPlayer')">
                                <i class="uil uil-play"></i>
                            </button>
                            <span class="audio-time" id="currentTime-vocalPlayer">0:00</span>

                            <div class="audio-progress" onclick="seek(event, 'vocalPlayer')">
                                <div class="audio-progress-bar vocal-gradient" id="vocalBar">
                                    <div class="progress-knob"></div>
                                </div>
                            </div>

                            <span class="audio-time" id="totalTime-vocalPlayer">0:00</span>
                            <audio id="vocalPlayer" src="<?= htmlspecialchars($song['vocal']) ?>"></audio>
                        </div>
                        <?php endif; ?>

                        <span class="error" id="audioVocalError"></span>
                    </div>

                    <!-- Instrumental File with Custom Gradient Player -->
                    <div class="col-md-6 col-sm-12">
                        <div class="field-title">Instrumental Waveform Stem</div>
                        <div class="music-strip-bar-uploader">
                            <span class="music-filename-status-string" id="custom-text_i">
                                <?= !empty($song['instrumental']) ? '(Click to change)' : 'No instrumental file attached yet.' ?>
                            </span>
                            <button type="button" class="btn btn-music-action-trigger" id="custom-button_i">CHANGE
                                STEM</button>
                        </div>
                        <input type="file" id="audio_i" name="audio_i" accept="audio/*" class="d-none" />

                        <?php if (!empty($song['instrumental'])): ?>
                        <div class="custom-audio-player">
                            <button type="button" class="play-pause-btn inst-gradient"
                                onclick="togglePlay('instPlayer')">
                                <i class="uil uil-play"></i>
                            </button>
                            <span class="audio-time" id="currentTime-instPlayer">0:00</span>

                            <div class="audio-progress" onclick="seek(event, 'instPlayer')">
                                <div class="audio-progress-bar inst-gradient" id="instBar">
                                    <div class="progress-knob"></div>
                                </div>
                            </div>

                            <span class="audio-time" id="totalTime-instPlayer">0:00</span>
                            <audio id="instPlayer" src="<?= htmlspecialchars($song['instrumental']) ?>"></audio>
                        </div>
                        <?php endif; ?>

                        <span class="error" id="audioIError"></span>
                    </div>

                </div>
            </div>

        </div>

        <!-- SUBMIT -->
        <div class="text-center mt-5 pt-2">
            <input type="submit" value="Update" name="update" id="update" class="btn btn-music-master-deploy shadow">
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

        if (data.albumImage && data.albumImage.trim() !== "") {
            previewImg.src = data.albumImage;
            previewImg.style.display = "block";
            placeholderText.style.display = "none";
        }

        showSuccessAlert("Track details auto-filled successfully!");
    } else {
        showErrorAlert("No song data found! Keep existing track details manually.");
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
        selectionSummary.textContent = `${selectedIds.length} Selected`;

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

const customFileSetup = (fileId, buttonId, textId) => {
    const realFileBtn = document.getElementById(fileId);
    const customBtn = document.getElementById(buttonId);
    const customTxt = document.getElementById(textId);

    customBtn.addEventListener("click", () => realFileBtn.click());
    realFileBtn.addEventListener("change", () => {
        customTxt.innerHTML = realFileBtn.files.length > 0 ? realFileBtn.files[0].name :
            "No new file chosen.";
    });
};

customFileSetup("audio_vocal", "custom-button_vocal", "custom-text_vocal");
customFileSetup("audio_i", "custom-button_i", "custom-text_i");

/* ==========================================
   --- CUSTOM AUDIO PLAYER LOGIC ENGINE ---
   ========================================== */
document.querySelectorAll('audio').forEach(audio => {
    const id = audio.id;

    audio.addEventListener('timeupdate', function() {
        const bar = this.parentElement.querySelector('.audio-progress-bar');
        const currentEl = document.getElementById('currentTime-' + id);
        const totalEl = document.getElementById('totalTime-' + id);

        if (bar && this.duration > 0) {
            const percentage = (this.currentTime / this.duration) * 100;
            bar.style.width = percentage + '%';
        }

        if (currentEl) {
            currentEl.textContent = formatTime(this.currentTime);
        }

        if (totalEl && this.duration > 0 && !isNaN(this.duration)) {
            totalEl.textContent = formatTime(this.duration);
        }
    });
});

function formatTime(seconds) {
    if (!seconds || isNaN(seconds)) return "0:00";
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return mins + ':' + (secs < 10 ? '0' : '') + secs;
}

function togglePlay(id) {
    const audio = document.getElementById(id);
    if (!audio) return;
    const btn = audio.parentElement.querySelector('.play-pause-btn i');

    // પ્લે કરતાં પહેલાં બીજા બધા ચાલુ ઓડિયો પ્લેયર્સ પૉઝ કરવા
    document.querySelectorAll('audio').forEach(a => {
        if (a.id !== id) {
            a.pause();
            const otherBtn = a.parentElement.querySelector('.play-pause-btn i');
            if (otherBtn) otherBtn.className = 'uil uil-play';
        }
    });

    if (audio.paused) {
        audio.play();
        btn.className = 'uil uil-pause';
    } else {
        audio.pause();
        btn.className = 'uil uil-play';
    }
}

function seek(e, id) {
    const audio = document.getElementById(id);
    if (!audio) return;
    const progress = e.currentTarget;
    const clickPos = (e.pageX - progress.getBoundingClientRect().left) / progress.offsetWidth;
    if (audio.duration > 0) {
        audio.currentTime = clickPos * audio.duration;
    }
}
</script>

<?php
include('fff.php');
?>
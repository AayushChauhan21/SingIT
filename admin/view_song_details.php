<?php
include('demo.php');
include('hhh.php');

$sid = $_GET['sid'];

function fetchSongDetails($sid)
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $api_url = $protocol . "://" . $host . "/SIngIT/flutter_crud/getSongDetails.php?sid=" . urlencode($sid);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $json_output = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return ['error' => 'Failed to retrieve song details from API.'];
    }

    curl_close($ch);
    $data = json_decode($json_output, true);

    if (json_last_error() !== JSON_ERROR_NONE || (isset($data['error']))) {
        return $data;
    }

    return $data;
}

$songDetails = fetchSongDetails($sid);

if (isset($songDetails['error'])) {
    echo '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($songDetails['error']) . '</div>';
    include('fff.php');
    exit;
}

$row = $songDetails;
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
    --music-bg: #080a10;
    --music-panel: rgba(16, 20, 31, 0.74);
    --music-panel-strong: rgba(22, 27, 40, 0.9);
    --music-stroke: rgba(255, 255, 255, 0.12);
    --music-stroke-soft: rgba(255, 255, 255, 0.07);
    --music-text: #f8fafc;
    --music-muted: #9aa4b2;
    --music-soft: #cad2df;
    --music-green: #1ed760;
    --music-coral: #ff6b6b;
    --music-violet: #8b5cf6;
    --music-blue: #38bdf8;
    --music-amber: #fbbf24;
    --music-radius: 24px;
    --music-shadow: 0 28px 80px rgba(0, 0, 0, 0.42);
    --music-transition: 220ms ease;

    /* New Layout Form Block Integration Variables */
    --studio-panel: rgba(13, 17, 28, 0.85);
    --studio-inner: #04060a;
    --studio-stroke: rgba(255, 255, 255, 0.08);
}

.page-header {
    margin-bottom: 1.25rem;
}

.page-header .main-content-title {
    font-family: "Inter", sans-serif;
    font-weight: 800;
    color: #eef2ff;
}

.breadcrumb-item a {
    color: #8d98aa !important;
}

.breadcrumb-item.active {
    color: var(--music-green) !important;
}

.music-dashboard {
    position: relative;
    overflow: hidden;
    border-radius: 32px;
    padding: 1.25rem;
    font-family: "Inter", sans-serif;
    color: var(--music-text);
    /* background:
            radial-gradient(circle at 14% 10%, rgba(30, 215, 96, 0.22), transparent 28%),
            radial-gradient(circle at 92% 4%, rgba(56, 189, 248, 0.2), transparent 25%),
            radial-gradient(circle at 70% 76%, rgba(255, 107, 107, 0.14), transparent 32%),
            linear-gradient(145deg, #080a10 0%, #111624 48%, #090b12 100%); */
    background: var(--dynamic-gradient);
    transition: background 1s ease;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.music-dashboard::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 56px 56px;
    mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.72), transparent 72%);
    pointer-events: none;
}

.music-shell {
    position: relative;
    z-index: 1;
}

.music-glass {
    background: var(--music-panel);
    border: 1px solid var(--music-stroke);
    border-radius: var(--music-radius);
    box-shadow: var(--music-shadow);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    transition: transform var(--music-transition), border-color var(--music-transition), box-shadow var(--music-transition);
}

.music-glass:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 34px 95px rgba(0, 0, 0, 0.48);
}

.music-hero {
    position: relative;
    overflow: hidden;
    min-height: 430px;
    padding: clamp(1.25rem, 3vw, 3rem);
    background:
        linear-gradient(to right, rgba(8, 10, 16, 0.98) 0%, rgba(8, 10, 16, 0.85) 45%, rgba(8, 10, 16, 0.35) 100%),
        var(--hero-cover) center/cover no-repeat;
}

.music-hero::after {
    content: "";
    position: absolute;
    inset: auto 0 0 0;
    height: 42%;
    background: linear-gradient(to top, rgba(8, 10, 16, 0.96), transparent);
    pointer-events: none;
}

.music-hero-content {
    position: relative;
    z-index: 2;
}

.music-cover-frame {
    position: relative;
    width: min(100%, 320px);
    aspect-ratio: 1;
    padding: 10px;
    border-radius: 30px;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.03));
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
}

.music-cover-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 22px;
    transition: transform 380ms ease;
}

.music-cover-frame:hover img {
    transform: scale(1.035);
}

.music-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: rgba(30, 215, 96, 0.13);
    border: 1px solid rgba(30, 215, 96, 0.28);
    color: #a7f3d0;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.music-title {
    margin: 1rem 0 .5rem;
    color: var(--music-text);
    font-size: clamp(2.25rem, 5vw, 5.7rem);
    line-height: .94;
    font-weight: 900;
    letter-spacing: 0;
    text-wrap: balance;
}

.music-album {
    color: var(--music-soft);
    font-size: clamp(1rem, 1.4vw, 1.3rem);
    font-weight: 600;
}

.music-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin: 1.5rem 0 1.85rem;
}

.music-pill {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    min-height: 34px;
    padding: .55rem .85rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.07);
    color: var(--music-soft);
    font-size: .78rem;
    font-weight: 700;
    transition: transform var(--music-transition), background var(--music-transition), color var(--music-transition);
}

.music-pill:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
}

.music-pill-primary {
    background: linear-gradient(135deg, var(--music-green), #58e58b);
    color: #06130b;
    border-color: transparent;
    box-shadow: 0 14px 35px rgba(30, 215, 96, 0.28);
}

.music-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .85rem;
}

.music-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    min-height: 48px;
    padding: .85rem 1.15rem;
    border-radius: 999px;
    font-weight: 800;
    border: 1px solid transparent;
    transition: transform var(--music-transition), box-shadow var(--music-transition), background var(--music-transition);
}

.music-action-edit {
    background: #ffffff;
    color: #090b12 !important;
    box-shadow: 0 18px 45px rgba(255, 255, 255, 0.12);
}

.music-action-delete {
    background: rgba(255, 107, 107, 0.13);
    color: #ffd5d5 !important;
    border-color: rgba(255, 107, 107, 0.28);
}

.music-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.32);
}

.music-card {
    height: 100%;
    padding: 1.35rem;
}

.music-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .9rem;
    margin-bottom: 1.25rem;
}

.music-icon {
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid var(--music-stroke-soft);
    color: var(--music-green);
    font-size: 1.25rem;
}

.music-label {
    margin: 0;
    color: var(--music-muted);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.music-heading {
    margin: .15rem 0 0;
    color: #ffffff;
    font-size: 1.1rem;
    font-weight: 800;
}

.music-tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem;
}

/* =========================================================================
       --- NEW 2ND DESIGN ARCHITECTURE CONSOLE RE-STRUCTURE MAPPINGS ---
       ========================================================================= */
.studio-lyrics-deck {
    background: var(--studio-panel);
    border: 1px solid var(--studio-stroke);
    border-radius: 24px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.lyrics-container-premium {
    position: relative;
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.25) 100%);
    border: 1px solid var(--music-stroke-soft);
    overflow: hidden;
}

.lyrics-viewport-teleprompter {
    background: var(--studio-inner);
    border: 1px solid rgba(255, 255, 255, 0.04);
    border-radius: 16px;
    max-height: 305px;
    overflow-y: auto;
    padding: 2rem;
    scrollbar-width: thin;
    /* scrollbar-color: var(--music-green) transparent; */
}

.lyrics-viewport-teleprompter::-webkit-scrollbar {
    width: 4px;
}

.lyrics-viewport-teleprompter::-webkit-scrollbar-thumb {
    background: var(--music-green);
    border-radius: 10px;
}

.teleprompter-line {
    ont-size: 1rem;
    font-weight: 800;
    line-height: 2.2;
    color: rgba(255, 255, 255, 0.25);
    /* margin-bottom: 1.25rem; */
    letter-spacing: -0.3px;
    transition: all 300ms ease;
}

/* 
.teleprompter-line.line-active {
    color: #ffffff;
    text-shadow: 0 0 25px rgba(255, 255, 255, 0.6);
    transform: scale(1.02) translateX(4px);
} */

/* PREMIUM CINEMATIC DIGITAL FRAME POSTER LAYOUT */
.studio-poster-deck {
    background: var(--studio-panel);
    border: 1px solid var(--studio-stroke);
    border-radius: 24px;
    padding: 1.5rem;
    text-align: center;
    height: 100%;
}

.cinematic-poster-lens {
    width: 100%;
    aspect-ratio: 4 / 3;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background: #000;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
}

.cinematic-poster-lens img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.cinematic-poster-lens:hover img {
    transform: scale(1.05);
}

.poster-lens-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.75);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 800;
    color: var(--music-coral);
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-transform: uppercase;
    z-index: 3;
}

.poster-action-tray {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-tray-node {
    flex: 1;
    min-height: 42px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1px solid transparent;
    transition: all 250ms ease;
}

.btn-tray-fs {
    background: rgba(255, 255, 255, 0.05);
    border-color: var(--studio-stroke);
    color: #fff;
}

.btn-tray-fs:hover {
    background: #fff;
    color: #000 !important;
}

.btn-tray-dl {
    background: var(--studio-inner);
    border-color: var(--studio-stroke);
    color: var(--music-muted);
}

.btn-tray-dl:hover {
    border-color: var(--music-coral);
    color: #fff;
}

/* ==========================================
       --- STUDIO ORIGINAL AUDIO GRID PACKS ---
       ========================================== */
.daw-console-card {
    border-radius: var(--music-radius);
    padding: 2rem;
    margin-top: 1.5rem;
}

.daw-console-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.5rem;
}

@media (max-width: 991.98px) {
    .daw-console-grid {
        grid-template-columns: 1fr;
    }
}

.daw-channel-strip {
    background: rgba(4, 5, 9, 0.6);
    border: 1px solid var(--music-stroke-soft);
    border-radius: 20px;
    padding: 1.5rem;
    position: relative;
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
}

.daw-channel-strip:hover {
    border-color: rgba(30, 215, 96, 0.4);
    background: rgba(4, 5, 9, 0.8);
    transform: translateY(-5px);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.55);
}

.daw-strip-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.daw-identity-block {
    display: flex;
    align-items: center;
    gap: 14px;
}

.daw-avatar-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--music-stroke);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--music-green);
}

.daw-track-name {
    font-size: 1.05rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
}

.daw-track-meta {
    font-size: 0.78rem;
    color: var(--music-muted);
    font-weight: 600;
    display: block;
    margin-top: 3px;
}

.daw-equalizer-wave {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    height: 24px;
    padding-right: 5px;
}

.daw-wave-bar {
    width: 3.5px;
    height: 100%;
    background: var(--music-green);
    border-radius: 3px;
    transform-origin: bottom;
    animation: bounceWave 1s ease-in-out infinite alternate;
}

.daw-wave-bar:nth-child(2) {
    animation-delay: 0.25s;
    height: 65%;
    background: var(--music-blue);
}

.daw-wave-bar:nth-child(3) {
    animation-delay: 0.55s;
    height: 90%;
}

.daw-wave-bar:nth-child(4) {
    animation-delay: 0.15s;
    height: 45%;
    background: var(--music-blue);
}

@keyframes bounceWave {
    0% {
        transform: scaleY(0.25);
    }

    100% {
        transform: scaleY(1.15);
    }
}

.daw-hardware-wrapper {
    background: #040509;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 999px;
    padding: 0.75rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 14px;
}

.daw-quality-badge {
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.3);
    color: var(--music-blue);
    font-size: 0.7rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.music-expand-btn,
.music-modal-close {
    border: 1px solid var(--music-stroke-soft);
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
    border-radius: 14px;
    transition: var(--music-transition);
}

.music-expand-btn:hover,
.music-modal-close:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    transform: translateY(-2px);
}

.music-audio-wrap {
    padding: .55rem;
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.music-dashboard audio {
    width: 100%;
    height: 42px;
    display: block;
    outline: none;
    filter: saturate(1.1) invert(100%) hue-rotate(180deg) brightness(1.1);
}

.music-modal .modal-content {
    background: rgba(8, 10, 16, 0.98) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 28px !important;
    color: #ffffff !important;
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.75) !important;
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
}

.music-modal .modal-header,
.music-modal .modal-footer {
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.music-modal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
    box-shadow: none !important;
}

.sweet-alert p {
    color: #0f172a;
}

@media (max-width: 991.98px) {
    .music-hero {
        min-height: auto;
    }
}

@media (max-width: 575.98px) {
    .music-dashboard {
        padding: .75rem;
        border-radius: 22px;
    }

    .music-hero,
    .music-card {
        border-radius: 20px;
    }

    .music-actions .music-action-btn {
        width: 100%;
    }

    .daw-hardware-wrapper {
        border-radius: 20px;
        flex-direction: column;
        gap: 10px;
        padding: 1rem;
    }

    .premium-poster-tray {
        flex-direction: column;
    }
}

/* ૧. મોડલને બોડીના એકદમ ચાઇલ્ડ તરીકે ફોર્સ કરો */
body>.modal {
    position: fixed !important;
}

/* ૨. જો આ પણ ન કામ કરે તો, મોડલને ડાયરેક્ટ રિમૂવ કરીશું */
.music-modal {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 999999 !important;
}

/* ૩. ઓવરલેને સાવ ગાયબ કરવા માટે (જો તમારે ઓવરલે જોઈતો જ ના હોય) */
.modal-backdrop {
    /* z-index: 999998 !important; */
    display: none !important;
}

/* નવું પ્રીમિયમ ગ્રેડિયન્ટ પ્લેયર */
.custom-audio-player {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(10, 12, 18, 0.8);
    padding: 10px 18px;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    width: 100%;
    backdrop-filter: blur(10px);
}

/* પ્રોગ્રેસ બાર કન્ટેનર */
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

/* પ્રોગ્રેસ બાર ગ્રેડિયન્ટ સાથે */
.audio-progress-bar {
    height: 100%;
    width: 0%;
    position: relative;
    border-radius: 3px;
    /* આ લાઈન ખાસ ઉમેરવાની છે */
    background: inherit;
}

/* નોબ (સર્ક્યુલર બટન) */
.progress-knob {
    position: absolute;
    right: -6px;
    top: -3px;
    /* થોડું ઉપર સેટ કરવા */
    width: 12px;
    height: 12px;
    background: #ffffff;
    border-radius: 50%;
    box-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
    transition: transform 0.1s;
}

/* અલગ અલગ પ્લેયર માટે કલર */
.inst-gradient {
    background: linear-gradient(45deg, var(--music-green), var(--music-blue)) !important;
}

.vocal-gradient {
    background: linear-gradient(45deg, var(--music-coral), var(--music-violet)) !important;
}

.play-pause-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    /* background: var(--music-green); */
    border: none;
    /* color: white; */
    /* color: #000; */
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
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
    /* overflow: hidden; */
}

.audio-progress-bar {
    height: 100%;
    background: var(--music-green);
    width: 0%;
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
        <h2 class="main-content-title tx-24 mg-b-5">View Song Details</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_songs.php">View Songs</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Song Details</li>
        </ol>
    </div>
</div>

<?php
if ($row) {
    ?>
<div class="music-dashboard container-fluid mb-5">
    <div class="music-shell">

        <!-- ==========================================
            --- CORE MUSIC HERO COMPONENT RELEASE ---
            ========================================== -->
        <section class="music-hero music-glass mb-4">
            <div class="row g-4 align-items-end music-hero-content">
                <div class="col-xl-4 col-lg-5 col-md-5">
                    <div class="music-cover-frame mx-auto mx-md-0">
                        <img id="mainPoster" src="<?= htmlspecialchars($row['image']) ?>" alt="Album cover"
                            crossorigin="anonymous">
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7 col-md-7">
                    <span class="music-eyebrow">
                        <i class="uil uil-music"></i>
                        Song details
                    </span>
                    <h1 class="music-title"><?php echo htmlspecialchars($row['name']); ?></h1>
                    <p class="music-album mb-0">
                        <i class="uil uil-compact-disc me-2"></i>
                        <?php echo htmlspecialchars($row['album'] ?? 'Single Release'); ?>
                    </p>

                    <div class="music-meta-row">
                        <span class="music-pill music-pill-primary">
                            <i class="uil uil-clock"></i>
                            <?= htmlspecialchars($row['length'] ?? '00:00') ?>
                        </span>
                    </div>

                    <div class="music-actions">
                        <a href='edit_songs.php?sid=<?= htmlspecialchars($row['sid']) ?>'
                            class='btn music-action-btn music-action-edit'>
                            <i class='uil uil-pen'></i> Edit Song
                        </a>
                        <a href='delete.php?sid=<?= htmlspecialchars($row['sid']) ?>'
                            class="btn music-action-btn music-action-delete delete-song-alert"
                            data-sid="<?= htmlspecialchars($row['sid']) ?>">
                            <i class='uil uil-trash-alt'></i> Delete Song
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================
            --- DYNAMIC TAXONOMY CHIPS SECTION ---
            ========================================== -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="music-card music-glass">
                    <div class="music-card-header mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="music-icon">
                                <i class="uil uil-users-alt"></i>
                            </div>
                            <div>
                                <p class="music-label">Artists</p>
                                <h3 class="music-heading">Singer Credits</h3>
                            </div>
                        </div>
                    </div>
                    <div class="music-tag-cloud">
                        <?php
                            if (!empty($row['singers'])) {
                                foreach ($row['singers'] as $singer) {
                                    echo "<span class='music-pill'>" . htmlspecialchars($singer['name']) . "</span>";
                                }
                            } else {
                                echo "<span class='music-pill'>Independent Record</span>";
                            }
                            ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="music-card music-glass">
                    <div class="music-card-header mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="music-icon">
                                <i class="uil uil-user-square"></i>
                            </div>
                            <div>
                                <p class="music-label">Collection</p>
                                <h3 class="music-heading">Genres</h3>
                            </div>
                        </div>
                    </div>
                    <div class="music-tag-cloud">
                        <?php
                            if (!empty($row['genres'])) {
                                foreach ($row['genres'] as $genre) {
                                    echo "<span class='music-pill'>" . htmlspecialchars($genre['name']) . "</span>";
                                }
                            } else {
                                echo "<span class='music-pill'>Unassigned Genre</span>";
                            }
                            ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-12">
                <div class="music-card music-glass">
                    <div class="music-card-header mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="music-icon">
                                <i class="uil uil-english-to-chinese"></i>
                            </div>
                            <div>
                                <p class="music-label">Localization</p>
                                <h3 class="music-heading">Languages</h3>
                            </div>
                        </div>
                    </div>
                    <div class="music-tag-cloud">
                        <?php
                            $language_names = htmlspecialchars($row['languages'] ?? '');
                            if (!empty($language_names)) {
                                $languages = explode(', ', $language_names);
                                foreach ($languages as $lang_name) {
                                    echo "<span class='music-pill'>" . $lang_name . "</span>";
                                }
                            } else {
                                echo "<span class='music-pill'>Not specified</span>";
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
            --- INTEGRATED UPGRADED WORKSPACE FLOW (LYRICS TELEPROMPTER & GLOW POSTER) ---
            ========================================================================= -->
        <div class="row g-4 mb-4">

            <!-- 1. HIGH-END LYRICS ENGINE (RE-STRUCTURED FROM GLASS CARD TERMINAL) -->
            <div class="col-xl-8 col-lg-7">
                <div class="music-card music-glass">
                    <div class="music-card-header mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="music-icon"><i class="uil uil-align-left-justify"></i></div>
                            <h3 class="music-heading">Premium Lyric Reader</h3>
                        </div>
                    </div>
                    <div class="lyrics-container-premium">
                        <div class="lyrics-viewport-teleprompter">
                            <?php
                                if (!empty($row['lyrics'])) {
                                    $lines = explode("\n", $row['lyrics']);
                                    foreach ($lines as $index => $line) {
                                        if (trim($line) === '')
                                            continue;
                                        $activeClass = ($index === 0) ? 'line-active' : '';
                                        echo '<div class="teleprompter-line ' . $activeClass . '">' . htmlspecialchars($line) . '</div>';
                                    }
                                } else {
                                    echo '<div class="teleprompter-line text-center text-muted py-5">No lyric mappings synced.</div>';
                                }
                                ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. CINEMATIC HIGH-RES POSTER INTERFACE (RE-STRUCTURED WITH BADGE & TRAY) -->
            <div class="col-xl-4 col-lg-5">
                <div class="music-card music-glass">
                    <div class="music-card-header mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="music-icon"><i class="uil uil-image"></i>
                            </div>
                            <h3 class="music-heading">Cinematic Canvas</h3>
                        </div>
                    </div>
                    <div class="cinematic-poster-lens" data-bs-toggle="modal" data-bs-target="#posterModal"
                        style="cursor: pointer;">
                        <div class="poster-lens-badge">HQ Distribution</div>
                        <img src="<?= htmlspecialchars($row['poster'] ?? $row['image']) ?>" alt="Poster">
                    </div>
                    <div class="poster-action-tray">
                        <button type="button" class="btn btn-tray-node btn-tray-fs" data-bs-toggle="modal"
                            data-bs-target="#posterModal"><i class="uil uil-expand-arrows-alt text-success"></i>
                            Expand</button>
                        <!-- <a href="<?= htmlspecialchars($row['poster'] ?? $row['image']) ?>"
                            download="Poster_<?= $sid ?>.jpg" class="btn btn-tray-node btn-tray-dl" target="_blank"><i
                                class="uil uil-import"></i> Fetch Asset</a> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
            --- AUDIO MASTER CONSOLE STEMS DECK ---
            ========================================== -->
        <section class="music-card music-glass daw-console-card">
            <div class="music-card-header mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="music-icon">
                        <i class="uil uil-sliders-v-alt"></i>
                    </div>
                    <div>
                        <p class="music-label">DAW Master Mix Console</p>
                        <h3 class="music-heading">Isolated Engineering Audio Stems</h3>
                    </div>
                </div>
            </div>

            <div class="daw-console-grid">

                <!-- CHANNEL STRIP 1: INSTRUMENTAL MIX CHANNEL -->
                <div class="daw-channel-strip">
                    <div class="daw-strip-header">
                        <div class="daw-identity-block">
                            <div class="daw-avatar-icon">
                                <i class="uil uil-music-note"></i>
                            </div>
                            <div>
                                <h4 class="daw-track-name">Instrumental Core</h4>
                                <span class="daw-track-meta">Backing Track Mix • MP3 320kbps</span>
                            </div>
                        </div>

                        <div class="daw-equalizer-wave">
                            <div class="daw-wave-bar"></div>
                            <div class="daw-wave-bar"></div>
                            <div class="daw-wave-bar"></div>
                            <div class="daw-wave-bar"></div>
                        </div>
                    </div>

                    <div class="daw-hardware-wrapper">
                        <span class="daw-quality-badge">Stereo</span>

                        <div class="custom-audio-player flex-grow-1 mx-2">
                            <button class="play-pause-btn inst-gradient" onclick="togglePlay('instPlayer')"><i
                                    class="uil uil-play"></i></button>
                            <span class="audio-time" id="currentTime-instPlayer">0:00</span>

                            <div class="audio-progress" onclick="seek(event, 'instPlayer')">
                                <div class="audio-progress-bar inst-gradient" id="instBar">
                                    <div class="progress-knob"></div>
                                </div>
                            </div>

                            <span class="audio-time" id="totalTime-instPlayer">0:00</span>
                            <audio id="instPlayer" class="hidden-audio"
                                src="<?= htmlspecialchars($row['instrumental'] ?? '') ?>"></audio>
                        </div>

                        <!-- <button class="btn music-expand-btn p-2 d-inline-flex" data-bs-toggle="modal"
                            data-bs-target="#instrumentalModal" aria-label="Open instrumental player">
                            <i class="uil uil-expand-arrows-alt"></i>
                        </button> -->
                    </div>
                </div>

                <!-- CHANNEL STRIP 2: ACAPELLA VOCAL MIX CHANNEL -->
                <div class="daw-channel-strip">
                    <div class="daw-strip-header">
                        <div class="daw-identity-block">
                            <div class="daw-avatar-icon" style="color: var(--music-coral);">
                                <i class="uil uil-microphone"></i>
                            </div>
                            <div>
                                <h4 class="daw-track-name">Acapella Stem</h4>
                                <span class="daw-track-meta">Isolated Vocals Feed • HQ Audio</span>
                            </div>
                        </div>

                        <div class="daw-equalizer-wave">
                            <div class="daw-wave-bar" style="background: var(--music-coral);"></div>
                            <div class="daw-wave-bar" style="background: var(--music-violet);"></div>
                            <div class="daw-wave-bar" style="background: var(--music-coral);"></div>
                            <div class="daw-wave-bar" style="background: var(--music-violet);"></div>
                        </div>
                    </div>

                    <div class="daw-hardware-wrapper">
                        <span class="daw-quality-badge"
                            style="background: rgba(255,107,107,0.12); color: var(--music-coral); border-color: rgba(255,107,107,0.25);">Vocal</span>

                        <div class="custom-audio-player flex-grow-1 mx-2">
                            <button class="play-pause-btn vocal-gradient" onclick="togglePlay('vocalPlayer')"><i
                                    class="uil uil-play"></i></button>
                            <span class="audio-time" id="currentTime-vocalPlayer">0:00</span>

                            <div class="audio-progress" onclick="seek(event, 'vocalPlayer')">
                                <div class="audio-progress-bar vocal-gradient" id="vocalBar">
                                    <div class="progress-knob"></div>
                                </div>
                            </div>

                            <span class="audio-time" id="totalTime-vocalPlayer">0:00</span>
                            <audio id="vocalPlayer" class="hidden-audio"
                                src="<?= htmlspecialchars($row['vocal'] ?? '') ?>"></audio>
                        </div>

                        <!-- <button class="btn music-expand-btn p-2 d-inline-flex" data-bs-toggle="modal"
                            data-bs-target="#vocalModal" aria-label="Open vocal player">
                            <i class="uil uil-expand-arrows-alt"></i>
                        </button> -->
                    </div>
                </div>
            </div>
        </section>

        <!-- --- SYSTEM OVERLAY MODALS --- -->
        <!-- Poster Modal -->
        <div class="modal fade music-modal" id="posterModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="music-icon"><i class="uil uil-image"></i></div>
                            <h5 class="modal-title text-white fw-bold">Poster Canvas Render</h5>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <img src="<?= htmlspecialchars($row['poster'] ?? $row['image']) ?>" alt="Poster"
                            class="img-fluid rounded-4" style="max-height: 65vh; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Instrumental Modal -->
        <div class="modal fade music-modal" id="instrumentalModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="music-icon"><i class="uil uil-music"></i></div>
                            <h5 class="modal-title text-white fw-bold">Instrumental Output Feed</h5>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="music-audio-wrap">
                            <audio controls class="w-100">
                                <source src="<?= htmlspecialchars($row['instrumental'] ?? '') ?>" type="audio/mpeg">
                            </audio>
                        </div>
                    </div>
                    <div class="modal-footer py-3">
                        <button type="button" class="btn music-modal-close px-4 py-2" data-bs-dismiss="modal">
                            <i class="uil uil-times me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vocal Modal -->
        <div class="modal fade music-modal" id="vocalModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="music-icon" style="color: var(--music-coral);"><i
                                    class="uil uil-microphone"></i></div>
                            <h5 class="modal-title text-white fw-bold">Acapella Vocal Feed</h5>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="music-audio-wrap">
                            <audio controls class="w-100">
                                <source src="<?= htmlspecialchars($row['vocal'] ?? '') ?>" type="audio/mpeg">
                            </audio>
                        </div>
                    </div>
                    <div class="modal-footer py-3">
                        <button type="button" class="btn music-modal-close px-4 py-2" data-bs-dismiss="modal">
                            <i class="uil uil-times me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
}
?>

<?php include('fff.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    var baseUrl = window.location.origin + '/SIngIT/flutter_crud/';

    $('#instrumentalModal, #vocalModal').on('hidden.bs.modal', function() {
        $(this).find('audio')[0].pause();
    });

    function showErrorAlert(message) {
        swal({
            title: "Error",
            text: message,
            type: "error",
            confirmButtonClass: "btn btn-danger",
            confirmButtonText: "Ok"
        });
    }

    $('.delete-song-alert').on('click', function(e) {
        e.preventDefault();
        var sid_value = $(this).data('sid');

        if (!sid_value) {
            showErrorAlert("Song ID missing Identifier tag reference.");
            return;
        }

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this song entry record profile!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-danger",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: baseUrl + 'deleteSong.php',
                    type: 'POST',
                    data: {
                        sid: sid_value
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            swal({
                                title: "Deleted!",
                                text: response.message,
                                type: "success",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => {
                                window.location.href = 'view_songs.php';
                            }, 2000);
                        } else {
                            showErrorAlert(response.message);
                        }
                    },
                    error: function() {
                        showErrorAlert(
                            "Server endpoint communication transmission loop pipeline failure."
                        );
                    }
                });
            }
        });
    });
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.min.js"></script>
<script>
const colorThief = new ColorThief();
const img = document.getElementById('mainPoster');
const dashboard = document.querySelector('.music-dashboard');

function applyDynamicPalette() {
    if (!img.complete) return;

    const palette = colorThief.getPalette(img, 5);

    if (palette) {
        // કલરની ઘાટ્ટાશ (Opacity) 0.35 અને 0.3 પર સેટ કરી છે
        const color1 = palette[0].join(',');
        const color2 = palette[2].join(',');
        const color3 = palette[4].join(',');

        // અહીં આલ્ફા વેલ્યુ 0.35 અને 0.3 છે - જે પહેલા કરતા ઘાટા અને અત્યાર કરતા થોડા આછા છે
        const gradient = `
                radial-gradient(circle at 14% 10%, rgba(${color1}, 0.35), transparent 35%),
                radial-gradient(circle at 92% 4%, rgba(${color2}, 0.32), transparent 35%),
                radial-gradient(circle at 70% 76%, rgba(${color3}, 0.3), transparent 35%),
                linear-gradient(145deg, #080a10 0%, #111624 48%, #090b12 100%)
            `;

        dashboard.style.setProperty('--dynamic-gradient', gradient);
    }
}

img.addEventListener('load', applyDynamicPalette);
if (img.complete) applyDynamicPalette();
</script>

<script>
// બધા પ્લેયર્સને ઓટો-અપડેટ કરવા માટેનું ફંક્શન
document.querySelectorAll('audio').forEach(audio => {
    const id = audio.id;

    audio.addEventListener('timeupdate', function() {
        const bar = this.parentElement.querySelector('.audio-progress-bar');
        const currentEl = document.getElementById('currentTime-' + id);
        const totalEl = document.getElementById('totalTime-' + id);

        // પ્રોગ્રેસ બાર અપડેટ
        // timeupdate ની અંદર જ્યાં bar છે, ત્યાં આ મૂકવું:
        if (bar && this.duration > 0) {
            const percentage = (this.currentTime / this.duration) * 100;
            bar.style.width = percentage + '%';
            // જો તારે knob ને હલાવવું હોય તો તે bar ની અંદર જ રહેશે
        }

        // Current Time
        if (currentEl) {
            currentEl.textContent = formatTime(this.currentTime);
        }

        // Total Time (અહીં જ આપણે ડ્યુરેશન પકડી લઈશું)
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
    const btn = audio.parentElement.querySelector('.play-pause-btn i');
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
    const progress = e.currentTarget;
    const clickPos = (e.pageX - progress.getBoundingClientRect().left) / progress.offsetWidth;
    if (audio.duration > 0) {
        audio.currentTime = clickPos * audio.duration;
    }
}
</script>
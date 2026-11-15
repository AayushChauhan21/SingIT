<?php
// FILE: view_artist_details.php

include('demo.php');
include('hhh.php');

// 1. Get the Artist ID from the URL
$arid = $_GET['arid'] ?? 0;

// --- Dynamic URL Logic for server-side fetching ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SIngIT/flutter_crud/";

$apiUrl = $base_url . "getArtistDetails.php?arid=" . urlencode($arid);
$response = @file_get_contents($apiUrl);
$data = json_decode($response, true);

// Check if data is valid and has 'details'
if (!empty($data) && !empty($data['details'])) {
    $artist = $data['details'];
    $songs = $data['songs'] ?? [];
} else {
    $artist = null;
    $songs = [];
}

// --- Image Fallback Logic ---
$default_profile_image = 'assets/img/default_profile.png';

if ($artist && !empty($artist['photo'])) {
    $artist_photo_src = htmlspecialchars($artist['photo']);
} else {
    $artist_photo_src = $default_profile_image;
}

if ($artist && !empty($artist['image'])) {
    $artist_image_src = htmlspecialchars($artist['image']);
} else {
    $artist_image_src = 'assets/img/transparent_placeholder.png';
}

?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
    --music-bg: #080a10;
    --music-panel: rgba(16, 20, 31, 0.74);
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
    text-decoration: none;
}

.breadcrumb-item.active {
    color: var(--music-violet) !important;
}

.music-dashboard {
    position: relative;
    overflow: hidden;
    border-radius: 32px;
    padding: 1.25rem;
    font-family: "Inter", sans-serif;
    color: var(--music-text);
    background: var(--dynamic-gradient, #080a10);
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

/* --- HERO SECTION --- */
.music-hero {
    position: relative;
    overflow: hidden;
    min-height: 380px;
    padding: clamp(1.25rem, 3vw, 3rem);
    background: linear-gradient(to right, rgba(8, 10, 16, 0.98) 0%, rgba(8, 10, 16, 0.85) 45%, rgba(8, 10, 16, 0.35) 100%);
}

.music-hero-content {
    position: relative;
    z-index: 2;
}


.artist-cover-frame {
    position: relative;
    width: min(100%, 320px);
    aspect-ratio: 1;
    padding: 10px;
    border-radius: 30px;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.03));
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
}

.artist-cover-frame img {
    width: 100%;
    object-fit: cover;
    border-radius: 22px;
    transition: transform 380ms ease;
}

.artist-cover-frame:hover img {
    transform: scale(1.035);
}


.music-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: rgba(139, 92, 246, 0.13);
    border: 1px solid rgba(139, 92, 246, 0.28);
    color: #ddd6fe;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.music-title {
    margin: 1rem 0 .5rem;
    color: var(--music-text);
    font-size: clamp(2.25rem, 4.5vw, 5rem);
    line-height: .94;
    font-weight: 900;
}

.music-meta-row {
    margin: 1.5rem 0 1.85rem;
    display: flex;
    gap: .75rem;
}

.music-pill {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .55rem .85rem;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--music-violet), #a78bfa);
    color: #fff;
    font-size: .85rem;
    font-weight: 800;
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.25);
}

/* --- ACTION BUTTONS --- */
.music-actions {
    display: flex;
    gap: .85rem;
    flex-wrap: wrap;
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
    transition: all var(--music-transition);
    border: none;
}

.music-action-edit {
    background: #ffffff;
    color: #090b12 !important;
}

.music-action-edit:hover {
    background: #e2e8f0;
    transform: translateY(-3px);
}

.music-action-delete {
    background: rgba(255, 107, 107, 0.13);
    color: #ffd5d5 !important;
    border: 1px solid rgba(255, 107, 107, 0.28);
}

.music-action-delete:hover {
    background: rgba(255, 107, 107, 0.25);
    transform: translateY(-3px);
    color: #fff !important;
}

/* --- BIOGRAPHY & TRACKLIST SECTION --- */
.music-card {
    padding: 1.5rem;
    border-radius: var(--music-radius);
    margin-top: 1.5rem;
}

.music-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.music-icon {
    width: 46px;
    height: 46px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid var(--music-stroke-soft);
    color: var(--music-violet);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bio-text {
    color: var(--music-soft);
    font-size: 1.05rem;
    line-height: 1.8;
}

.tracklist-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid transparent;
    transition: all 0.2s ease;
    margin-bottom: 0.75rem;
}

.tracklist-item:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: var(--music-stroke-soft);
    transform: translateX(5px);
}

.track-img {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.track-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    margin: 0;
    text-decoration: none;
}

.track-name:hover {
    color: var(--music-violet);
}

.track-singer {
    font-size: 0.8rem;
    color: var(--music-muted);
    font-weight: 500;
    margin-top: 2px;
}

.track-time {
    font-size: 0.85rem;
    color: var(--music-soft);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.track-actions .btn {
    width: 38px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 1.1rem;
    transition: 0.2s ease;
}

.btn-view-song {
    background: rgba(139, 92, 246, 0.15);
    color: var(--music-violet);
    border: 1px solid rgba(139, 92, 246, 0.3);
}

.btn-view-song:hover {
    background: var(--music-violet);
    color: #fff;
}
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Artist Profile</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_artist.php">Artists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile Details</li>
        </ol>
    </div>
</div>

<?php if ($artist): ?>
<div class="music-dashboard mb-5">
    <div class="music-shell">

        <!-- ================= HERO SECTION ================= -->
        <section class="music-hero music-glass mb-4">
            <div class="row g-4 align-items-center music-hero-content">
                <div class="col-xl-3 col-lg-5 col-md-12 text-lg-start">
                    <div class="artist-cover-frame mx-auto mx-lg-0">
                        <img id="mainPoster" src="<?= $artist_photo_src ?>" alt="Artist Profile"
                            crossorigin="anonymous">
                    </div>
                </div>

                <div class="col-xl-9 col-lg-7 col-md-12 text-lg-start">
                    <span class="music-eyebrow">
                        <i class="uil uil-user-square"></i> Artist Details
                    </span>
                    <h1 class="music-title"><?= htmlspecialchars($artist['name']) ?></h1>

                    <div class="music-meta-row justify-content-center justify-content-lg-start">
                        <span class="music-pill">
                            <i class="uil uil-music-note"></i>
                            <?= htmlspecialchars($artist['song_count'] ?? 0) ?> Releases
                        </span>
                    </div>

                    <div class="music-actions justify-content-center justify-content-lg-start">
                        <a href='edit_artist.php?arid=<?= htmlspecialchars($arid) ?>'
                            class='btn music-action-btn music-action-edit'>
                            <i class='uil uil-pen'></i> Edit Profile
                        </a>
                        <a href='delete.php?arid=<?= htmlspecialchars($arid) ?>'
                            class="btn music-action-btn music-action-delete delete-artist-alert"
                            data-arid="<?= htmlspecialchars($arid) ?>">
                            <i class='uil uil-trash-alt'></i> Delete Artist
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= BIOGRAPHY SECTION ================= -->
        <section class="music-card music-glass mb-4">
            <div class="music-card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="music-icon"><i class="uil uil-book-open"></i></div>
                    <div>
                        <p class="mb-0"
                            style="color: var(--music-muted); font-size: .72rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;">
                            About</p>
                        <h3 class="music-heading" style="margin:0; font-size: 1.25rem;">Biography</h3>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="<?= $artist_image_src ?>" alt="Artist Transparent"
                        style="max-height: 220px; width: 100%; object-fit: contain; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.5));">
                </div>
                <div class="col-md-9">
                    <p class="bio-text m-0">
                        <?= nl2br(htmlspecialchars($artist['description'])) ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- ================= TRACKLIST SECTION ================= -->
        <section class="music-card music-glass">
            <div class="music-card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="music-icon"><i class="uil uil-headphones-alt"></i></div>
                    <div>
                        <p class="mb-0"
                            style="color: var(--music-muted); font-size: .72rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;">
                            Discography</p>
                        <h3 class="music-heading" style="margin:0; font-size: 1.25rem;">Popular Tracks</h3>
                    </div>
                </div>
            </div>

            <div class="tracklist-container">
                <?php if (!empty($songs)): ?>
                <?php foreach ($songs as $song):
                            $song_image_src = !empty($song['image']) ? htmlspecialchars($song['image']) : $default_profile_image;
                            ?>
                <div class="tracklist-item">
                    <!-- Image -->
                    <img src="<?= $song_image_src ?>" class="track-img me-3" alt="Song Cover">

                    <!-- Info -->
                    <div class="flex-grow-1">
                        <a href="view_song_details.php?sid=<?= $song['sid']; ?>" class="track-name">
                            <?= htmlspecialchars($song['name']) ?>
                        </a>
                        <div class="track-singer">
                            <i
                                class="uil uil-microphone me-1"></i><?= htmlspecialchars($song['artist_names'] ?? 'Unknown Artist') ?>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="track-time me-4 d-none d-sm-flex">
                        <i class="uil uil-clock"></i> <?= htmlspecialchars($song['length'] ?? '0:00') ?>
                    </div>

                    <!-- Actions -->
                    <div class="track-actions d-flex gap-2">
                        <a href="view_song_details.php?sid=<?= $song['sid']; ?>" class="btn btn-view-song"
                            title="Play / View">
                            <i class="uil uil-play"></i>
                        </a>
                        <a href="edit_songs.php?sid=<?= $song['sid']; ?>" class="btn btn-view-song"
                            style="background: rgba(255,255,255,0.1); color:#fff; border-color: rgba(255,255,255,0.2);"
                            title="Edit">
                            <i class="uil uil-pen"></i>
                        </a>
                        <a href="delete.php?sid=<?= $song['sid']; ?>" class="btn music-action-delete delete-song-alert"
                            data-sid="<?= $song['sid']; ?>" title="Delete">
                            <i class="uil uil-trash-alt"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="uil uil-music-slash d-block text-muted mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted fw-bold">No tracks available for this artist yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

    </div>
</div>
<?php else: ?>
<div class="alert alert-danger rounded-4 fw-bold p-4 text-center mt-4">
    <i class="uil uil-exclamation-triangle fs-1 d-block mb-2"></i>
    Artist not found or API returned empty data.
</div>
<?php endif; ?>

<?php include('fff.php'); ?>

<!-- COLOR THIEF & DYNAMIC GRADIENT -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.min.js"></script>
<script>
const colorThief = new ColorThief();
const img = document.getElementById('mainPoster');
const dashboard = document.querySelector('.music-dashboard');

function applyDynamicPalette() {
    if (!img || !img.complete) return;
    const palette = colorThief.getPalette(img, 5);

    if (palette) {
        const color1 = palette[0].join(',');
        const color2 = palette[2].join(',');
        const color3 = palette[4] ? palette[4].join(',') : color1;

        const gradient = `
                radial-gradient(circle at 14% 10%, rgba(${color1}, 0.30), transparent 40%),
                radial-gradient(circle at 92% 4%, rgba(${color2}, 0.25), transparent 40%),
                radial-gradient(circle at 70% 76%, rgba(${color3}, 0.22), transparent 40%),
                linear-gradient(145deg, #080a10 0%, #111624 48%, #090b12 100%)
            `;
        dashboard.style.setProperty('--dynamic-gradient', gradient);
    }
}

if (img) {
    img.addEventListener('load', applyDynamicPalette);
    if (img.complete) applyDynamicPalette();
}
</script>

<!-- DELETE LOGIC (SWEETALERT) -->
<script>
function showErrorAlert(message) {
    swal({
        title: "Error",
        text: message,
        type: "error",
        confirmButtonClass: "btn btn-danger",
        confirmButtonText: "Ok",
    });
}

function extractArtistId(url) {
    var match = url.match(/[?&]arid=(\d+)/);
    return match ? match[1] : null;
}

// --- 🎤 Artist Delete Logic ---
$('.delete-artist-alert').on('click', function(e) {
    e.preventDefault();
    var deleteUrl = $(this).attr('href');
    var arid_value = $(this).data('arid');

    if (!arid_value) {
        arid_value = extractArtistId(deleteUrl);
        if (!arid_value) {
            showErrorAlert("Artist ID not found for deletion.");
            return;
        }
    }

    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this artist and associated songs!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-danger",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel plx!",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            swal({
                title: "Deleting...",
                text: "Please wait.",
                type: "info",
                showConfirmButton: false
            });

            $.ajax({
                url: '../flutter_crud/deleteArtist.php',
                type: 'POST',
                data: {
                    arid: arid_value
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
                        setTimeout(function() {
                            window.location.href = 'view_artist.php';
                        }, 2000);
                    } else {
                        showErrorAlert(response.message || "Failed to delete artist.");
                    }
                },
                error: function() {
                    showErrorAlert("Server error or connection failed.");
                }
            });
        } else {
            swal({
                title: "Cancelled",
                text: "Your artist is safe :)",
                type: "error",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
});

// --- 🎶 Song Delete Logic ---
$('.delete-song-alert').on('click', function(e) {
    e.preventDefault();
    var sid_value = $(this).data('sid');

    if (!sid_value) {
        showErrorAlert("Song ID not found for deletion.");
        return;
    }

    swal({
        title: "Delete Song?",
        text: "Are you sure you want to delete this song?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-danger",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            swal({
                title: "Deleting...",
                text: "Please wait...",
                type: "info",
                showConfirmButton: false
            });

            $.ajax({
                url: '../flutter_crud/deleteSong.php',
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
                            timer: 1500
                        });
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showErrorAlert(response.message || "Failed to delete song.");
                    }
                },
                error: function() {
                    showErrorAlert("Server error or connection failed.");
                }
            });
        } else {
            swal({
                title: "Cancelled",
                text: "Song deletion cancelled.",
                type: "error",
                showConfirmButton: false,
                timer: 1000
            });
        }
    });
});
</script>
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
.btn-md,
.btn-lg {
    font-size: 17px;
    border-radius: 5px;
    font-weight: bold;
}

.delete-artist-alert {
    cursor: pointer;
}

.rounded-20 {
    border-radius: 20px;
}

.rounded4 {
    border-radius: 15%;
}

.artist-secondary-img {
    height: 250px;
    width: 100%;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-bottom: 15px;
}
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Artist Details</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_artist.php">View Artist</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Artist Details</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card productdesc">
            <div class="card-body h-100">
                <div class="row">

                    <?php if ($artist): ?>

                    <div class="col-xl-12" id="cnter">

                        <div class="row">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?= $artist_photo_src ?>" class="rounded-20 me-4"
                                    style="height: 210px; width: 170px; object-fit: cover; object-position: top; border: 2px solid #3B3B64; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);"
                                    alt="Artist Photo">

                                <div class="flex-grow-1">
                                    <h1 class="fw-bold mb-4" style="font-size: 48px;">
                                        <?= htmlspecialchars($artist['name']) ?>
                                    </h1>

                                    <div class="d-flex flex-wrap gap-3">
                                        <a href='edit_artist.php?arid=<?= htmlspecialchars($arid) ?>'
                                            class="btn btn-success btn-lg fw-bold"><i class="uil uil-pen"></i> Edit</a>

                                        <a href='delete.php?arid=<?= htmlspecialchars($arid) ?>'
                                            class="btn btn-danger btn-lg fw-bold delete-artist-alert"
                                            data-arid="<?= htmlspecialchars($arid) ?>">
                                            <i class="uil uil-trash-alt"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-4 mb-4">

                        <div class="row">
                            <div class="col-md-2">
                                <img src="<?= $artist_image_src ?>" class="rounded-20 me-4"
                                    style="height: 210px; width: 170px; object-fit: cover; object-position: top; border: 2px solid #3B3B64; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);"
                                    alt="Artist Photo(Transparent)">
                            </div>

                            <div class="col-md-8">
                                <h3 class="tx-24 fw-bold mb-3">Biography</h3>
                                <p class="text-muted tx-16 mx-2" style="line-height: 1.8;">
                                    <?= htmlspecialchars($artist['description']) ?>
                                </p>
                            </div>
                        </div>

                        <hr class="mt-4 mb-4">

                        <div class="mt-1">
                            <b class="tx-30">Songs (<?= htmlspecialchars($artist['song_count'] ?? 0) ?>)</b>
                            <div class="">
                                <div class="row">
                                    <div class="col-xl-12 mt-4">
                                        <div class="card">
                                            <div class="card-body p-0 mb-3 mt-3">

                                                <?php
                                                    if (!empty($songs)):
                                                        foreach ($songs as $song):
                                                            $song_image_src = !empty($song['image']) ? htmlspecialchars($song['image']) : $default_profile_image;
                                                ?>
                                                <div class="p-4 border-bottom border-top">
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= $song_image_src ?>" class="rounded4"
                                                            style="width: 80px; height: 80px; object-fit: cover; margin-right: 15px;"
                                                            alt="Song Image">

                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-1 tx-20">
                                                                    <a class="text-primary fw-bold"
                                                                        href="view_song_details.php?sid=<?= $song['sid']; ?>">
                                                                        <?= htmlspecialchars($song['name']) ?>
                                                                    </a>
                                                                </h5>
                                                                <span class="font-15 uil uil-clock-eight text-warning fw-bold">
                                                                    &nbsp;<?= htmlspecialchars($song['length'] ?? 'N/A') ?>
                                                                </span>
                                                            </div>

                                                            <span class="text-muted tx-15"><i class="uil uil-user-square"></i>
                                                                <?= htmlspecialchars($song['artist_names'] ?? 'N/A') ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between mt-3">
                                                        <div class="d-flex gap-2">
                                                            <a href="edit_songs.php?sid=<?= $song['sid']; ?>"
                                                                class="btn btn-success btn-lg fw-bold"><i
                                                                    class="uil uil-pen"></i> Edit</a>
                                                            &nbsp;
                                                            <a href="delete.php?sid=<?= $song['sid']; ?>"
                                                                class="btn btn-danger btn-lg fw-bold delete-song-alert"
                                                                data-sid="<?= $song['sid']; ?>"><i
                                                                    class="uil uil-trash-alt"></i> Delete</a>
                                                        </div>
                                                        <div>
                                                            <a href="view_song_details.php?sid=<?= $song['sid']; ?>"
                                                                class="btn btn-primary btn-lg fw-bold"><i
                                                                    class="uil uil-expand-from-corner"></i> View Song Details</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; else: ?>
                                                <div class="p-4 border-bottom border-top text-center text-muted">No songs found for this artist.</div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php else: ?>
                    <div class="alert alert-danger">Artist not found or API returned empty data.</div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('fff.php'); ?>

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
        },
        function(isConfirm) {
            if (isConfirm) {
                swal({
                    title: "Deleting...",
                    text: "Please wait while we delete the artist.",
                    type: "info",
                    showConfirmButton: false,
                });

                $.ajax({
                    // URL Updated to relative path
                    url: '../flutter_crud/deleteArtist.php',
                    type: 'POST',
                    data: { arid: arid_value },
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
                    error: function(xhr, status, error) {
                        showErrorAlert("Server error or connection failed. Please check the network and API URL.");
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

    var deleteUrl = $(this).attr('href');
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
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) {
                swal({
                    title: "Deleting...",
                    text: "Please wait...",
                    type: "info",
                    showConfirmButton: false,
                });

                $.ajax({
                    // URL Updated to relative path
                    url: '../flutter_crud/deleteSong.php',
                    type: 'POST',
                    data: { sid: sid_value },
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
                    error: function(xhr, status, error) {
                        showErrorAlert("Server error or connection failed (Song Delete).");
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
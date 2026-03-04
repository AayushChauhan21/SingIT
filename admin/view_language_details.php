<?php
// FILE: view_language_details.php

include('demo.php');
include('hhh.php');

// 1. Get the Language ID from the URL
$lid = $_GET['lid'] ?? 0;

// --- Dynamic URL Logic for server-side fetching ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SIngIT/flutter_crud/";

// FIXED API ENDPOINT
$apiUrl = $base_url . "getLanguageDetails.php?lid=" . urlencode($lid);

// Set default empty arrays
$language = null;
$songs = [];

// Use file_get_contents to fetch JSON data from the API
$response = @file_get_contents($apiUrl);

// Check for valid response and decode
if ($response !== FALSE) {
    $data = json_decode($response, true);

    // Check if data is valid and has 'language_info'
    if (!empty($data) && !empty($data['language_info'])) {
        $language = $data['language_info'];
        $songs = $data['songs'] ?? []; 
    }
}
?>

<style>
    /* Add a class for the delete button */
    .btn-md,
    .btn-lg {
        font-size: 17px;
        border-radius: 5px;
        font-weight: bold;
    }

    .delete-language-alert {
        cursor: pointer;
    }

    /* Styles adapted for Genre/Artist consistency */
    .rounded-20 {
        border-radius: 20px;
    }

    .rounded4 {
        border-radius: 15%;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Language Details</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_languages.php">View Languages</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Language Details</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card productdesc">
            <div class="card-body h-100">
                <div class="row">

                    <?php if ($language): ?>

                        <div class="col-xl-12" id="cnter">

                            <div class="row">
                                <div class="d-flex align-items-center mb-3">

                                    <img src="<?= htmlspecialchars($language['image'] ?? 'assets/img/transparent_placeholder.png') ?>" class="rounded-20 me-4"
                                        style="height: 170px; width: 270px; object-fit: cover; border: 2px solid #3B3B64; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);"
                                        alt="Language Image">

                                    <div class="flex-grow-1">
                                        <h1 class="fw-bold mb-4" style="font-size: 48px;">
                                            <?= htmlspecialchars($language['name']) ?>
                                        </h1>

                                        <div class="d-flex flex-wrap gap-3">
                                            <a href='edit_language.php?lid=<?= htmlspecialchars($lid) ?>'
                                                class="btn btn-success btn-lg fw-bold"><i class="uil uil-pen"></i> Edit</a>

                                            <a href='delete.php?lid=<?= htmlspecialchars($lid) ?>'
                                                class="btn btn-danger btn-lg fw-bold delete-language-alert"
                                                data-lid="<?= htmlspecialchars($lid) ?>">
                                                <i class="uil uil-trash-alt"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">

                            <div class="mt-1">
                                <b class="tx-30">Songs (<?= htmlspecialchars($language['song_count'] ?? 0) ?>)</b>
                                <div class="">
                                    <div class="row">

                                        <div class="col-xl-12 mt-4">
                                            <div class="card">
                                                <div class="card-body p-0 mb-3 mt-3">

                                                    <?php if (!empty($songs)):
                                                        foreach ($songs as $song):
                                                    ?>
                                                    <div class="p-4 border-bottom border-top">
                                                        <div class="d-flex align-items-center">

                                                            <img src="<?= htmlspecialchars($song['image'] ?? 'assets/img/transparent_placeholder.png') ?>"
                                                                class="rounded4"
                                                                style="border-radius: 15%; width: 80px; height: 80px; object-fit: cover; margin-right: 15px;"
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
                                                                    <?= htmlspecialchars($song['singer_name'] ?? 'N/A') ?>
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
                                                        <div class="p-4 border-bottom border-top text-center text-muted">No songs found for this language.</div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-danger">Language not found or API returned empty data.</div>
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

    function extractLanguageId(url) {
        var match = url.match(/[?&]lid=(\d+)/);
        return match ? match[1] : null;
    }

    // --- 🎶 Language Delete Logic ---
    $('.delete-language-alert').on('click', function (e) {
        e.preventDefault();

        var deleteUrl = $(this).attr('href');
        var lid_value = $(this).data('lid'); 

        if (!lid_value) {
            lid_value = extractLanguageId(deleteUrl);
            if (!lid_value) {
                showErrorAlert("Language ID not found for deletion.");
                return;
            }
        }

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this language and associated song links!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-danger",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function (isConfirm) {
            if (isConfirm) {
                swal({
                    title: "Deleting...",
                    text: "Please wait while we delete the language.",
                    type: "info",
                    showConfirmButton: false,
                });

                $.ajax({
                    // UPDATED URL to relative path
                    url: '../flutter_crud/deleteLanguage.php',
                    type: 'POST',
                    data: { lid: lid_value },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            swal({
                                title: "Deleted!",
                                text: response.message,
                                type: "success",
                                showConfirmButton: false,
                                timer: 2000
                            });

                            setTimeout(function () {
                                window.location.href = 'view_languages.php';
                            }, 2000);

                        } else {
                            showErrorAlert(response.message || "Failed to delete language.");
                        }
                    },
                    error: function (xhr, status, error) {
                        showErrorAlert("Server error or connection failed. Please check the network and API URL.");
                    }
                });

            } else {
                swal({
                    title: "Cancelled",
                    text: "Your language is safe :)",
                    type: "error",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });

    // --- 🎶 Song Delete Logic ---
    $('.delete-song-alert').on('click', function (e) {
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
        function (isConfirm) {
            if (isConfirm) {
                swal({
                    title: "Deleting...",
                    text: "Please wait...",
                    type: "info",
                    showConfirmButton: false,
                });

                $.ajax({
                    // UPDATED URL to relative path
                    url: '../flutter_crud/deleteSong.php',
                    type: 'POST',
                    data: { sid: sid_value },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            swal({
                                title: "Deleted!",
                                text: response.message,
                                type: "success",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showErrorAlert(response.message || "Failed to delete song.");
                        }
                    },
                    error: function (xhr, status, error) {
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
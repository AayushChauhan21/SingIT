<?php
// FILE: view_genre_details.php

include('demo.php');
include('hhh.php');
// include('connection.php'); // Not needed as we rely on the API

// 1. Get the Genre ID from the URL
$gid = $_GET['gid'] ?? 0;

// 2. Define API URL and fetch data
// FIXED API ENDPOINT: Now points to the correct getGenreDetails.php
$apiUrl = "http://localhost/SIngIT/flutter_crud/getSongsByCategory.php?gid=" . urlencode($gid);

// Set default empty arrays
$genre = null;
$songs = [];

// Use file_get_contents to fetch JSON data from the API
$response = @file_get_contents($apiUrl);

// Check for valid response and decode
if ($response !== FALSE) {
    $data = json_decode($response, true);

    // Check if data is valid and has 'genre_info'
    if (!empty($data) && !empty($data['genre_info'])) {
        $genre = $data['genre_info'];
        $songs = $data['songs'] ?? []; // Extract songs array, default to empty array
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

    .delete-genre-alert {
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
        <h2 class="main-content-title tx-24 mg-b-5">View Genre Details</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_genres.php">View Genres</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Genre Details</li>
        </ol>
    </div>

</div>
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card productdesc">
            <div class="card-body h-100">
                <div class="row">

                    <?php if ($genre): ?>

                        <div class="col-xl-12" id="cnter">

                            <div class="row">
                                <div class="d-flex align-items-center mb-3">

                                    <img src="<?= htmlspecialchars($genre['image']) ?>" class="rounded-20 me-4"
                                        style="height: 170px; width: 270px; object-fit: cover; border: 2px solid #3B3B64; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);"
                                        alt="Genre Image">


                                    <div class="flex-grow-1">
                                        <h1 class="fw-bold mb-4" style="font-size: 48px;">
                                            <?= htmlspecialchars($genre['name']) ?>
                                        </h1>

                                        <div class="d-flex flex-wrap gap-3">
                                            <a href='edit_genres.php?gid=<?= htmlspecialchars($gid) ?>'
                                                class="btn btn-success btn-lg fw-bold"><i class="uil uil-pen"></i> Edit</a>

                                            <a href='delete.php?gid=<?= htmlspecialchars($gid) ?>'
                                                class="btn btn-danger btn-lg fw-bold delete-genre-alert"
                                                data-gid="<?= htmlspecialchars($gid) ?>">
                                                <i class="uil uil-trash-alt"></i>
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">


                            <div class="mt-1">

                                <b class="tx-30">Songs (<?= htmlspecialchars($genre['song_count'] ?? 0) ?>)</b>
                                <div class="">
                                    <div class="row">

                                        <div class="col-xl-12 mt-4">
                                            <div class="card">
                                                <div class="card-body p-0 mb-3 mt-3">

                                                    <?php
                                                    if (!empty($songs)):
                                                        foreach ($songs as $song):
                                                            ?>
                                                            <div class="p-4 border-bottom border-top">

                                                                <div class="d-flex align-items-center">

                                                                    <img src="<?= htmlspecialchars($song['image']) ?>"
                                                                        class="rounded4"
                                                                        style="border-radius: 15%; width: 80px; height: 80px; object-fit: cover; margin-right: 15px;"
                                                                        alt="Song Image">


                                                                    <div class="flex-grow-1">

                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center">

                                                                            <h5 class="mb-1 tx-20">

                                                                                <a class="text-primary fw-bold"
                                                                                    href="view_song_details.php?sid=<?= $song['sid']; ?>">
                                                                                    <?= htmlspecialchars($song['name']) ?>
                                                                                </a>

                                                                            </h5>

                                                                            <span
                                                                                class="font-15 uil uil-clock-eight text-warning fw-bold">
                                                                                &nbsp;<?= htmlspecialchars($song['length'] ?? 'N/A') ?>
                                                                            </span>
                                                                        </div>


                                                                        <span class="text-muted tx-15"><i
                                                                                class="uil uil-user-square"></i>
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
                                                                                class="uil uil-trash-alt"></i>
                                                                            Delete</a>

                                                                    </div>

                                                                    <div>

                                                                        <a href="view_song_details.php?sid=<?= $song['sid']; ?>"
                                                                            class="btn btn-primary btn-lg fw-bold"><i
                                                                                class="uil uil-expand-from-corner"></i> View Song
                                                                            Details</a>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        <?php endforeach;
                                                    else: ?>
                                                        <div class="p-4 border-bottom border-top text-center text-muted">No
                                                            songs found for this genre.</div>
                                                        <?php
                                                    endif;
                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    <?php else: ?>
                        <div class="alert alert-danger">Genre not found or API returned empty data.
                        </div>
                    <?php endif; ?>


                </div>

            </div>
        </div>
    </div>
</div>
<?php
include('fff.php');
?>


<script>
    // Helper function to display error alerts
    function showErrorAlert(message) {
        swal({
            title: "Error",
            text: message,
            type: "error",
            confirmButtonClass: "btn btn-danger",
            confirmButtonText: "Ok",
        });
    }

    // Function to extract the Genre ID from the delete link's href
    function extractGenreId(url) {
        var match = url.match(/[?&]gid=(\d+)/);
        return match ? match[1] : null;
    }


    // --- 🎶 Genre Delete Logic ---
    $('.delete-genre-alert').on('click', function (e) {
        e.preventDefault();

        var deleteUrl = $(this).attr('href');
        var gid_value = $(this).data('gid');

        if (!gid_value) {
            gid_value = extractGenreId(deleteUrl);
            if (!gid_value) {
                showErrorAlert("Genre ID not found for deletion.");
                return;
            }
        }

        // Main Confirmation Pop-up
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this genre and associated song links!",
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
                        text: "Please wait while we delete the genre.",
                        type: "info",
                        showConfirmButton: false,
                    });

                    // AJAX Call to deleteGenre.php
                    $.ajax({
                        url: 'http://localhost/SIngIT/flutter_crud/deleteGenre.php',
                        type: 'POST',
                        data: {
                            gid: gid_value
                        },
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
                                    // Redirecting to the main view_genres list after successful deletion
                                    window.location.href = 'view_genres.php';
                                }, 2000);

                            } else {
                                showErrorAlert(response.message || "Failed to delete genre.");
                            }
                        },
                        error: function (xhr, status, error) {
                            showErrorAlert(
                                "Server error or connection failed. Please check the network and API URL."
                            );
                        }
                    });

                } else {
                    swal({
                        title: "Cancelled",
                        text: "Your genre is safe :)",
                        type: "error",
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
    });

    // --- 🎶 Song Delete Logic (Re-used for the songs list in this view) ---
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
                        url: 'http://localhost/SIngIT/flutter_crud/deleteSong.php',
                        type: 'POST',
                        data: {
                            sid: sid_value
                        },
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
                                // Simple reload to refresh the song list after deletion
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
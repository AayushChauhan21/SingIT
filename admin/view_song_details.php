<?php
include('demo.php');
include('hhh.php');
// include('connection.php'); // No longer needed for fetching song data directly via mysqli

$sid = $_GET['sid'];

// --- NEW: Function to fetch data from the API endpoint ---
function fetchSongDetails($sid)
{
    // Construct the URL to your getSongDetails.php API
    // IMPORTANT: Replace 'http://localhost/your_path/' with the actual base URL where your API is hosted
    $api_url = "http://localhost/SIngIT/flutter_crud/getSongDetails.php?sid=" . urlencode($sid);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the transfer as a string
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set a timeout

    // Execute the cURL request
    $json_output = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        // Handle error: e.g., connection issue, API file not found
        // In a real application, you'd log this or show a generic error.
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return ['error' => 'Failed to retrieve song details from API.'];
    }

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($json_output, true);

    // Check if JSON decoding failed or if the API returned an error
    if (json_last_error() !== JSON_ERROR_NONE || (isset($data['error']))) {
        // Handle API error or invalid JSON
        return $data;
    }

    return $data;
}

// Fetch the song details
$songDetails = fetchSongDetails($sid);

// Check if an error occurred during fetching
if (isset($songDetails['error'])) {
    echo '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($songDetails['error']) . '</div>';
    // Include footer and exit to stop rendering the rest of the page
    include('fff.php');
    exit;
}

// $row variable now holds all your song data from the API
$row = $songDetails;

?>

<style>
    /* ... your existing CSS styles remain here ... */
    .btn-md {
        font-size: 18.5px;
        font-weight: bold;
    }

    /* Custom style for dark modals' close button */
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* UPDATED: Modal content background and shadow */
    .modal-content.bg-dark {
        background-color: var(--custom-modal-bg) !important;
        border: none;
        box-shadow: 0 5px 15px rgba(var(--custom-modal-shadow), 0.7);
        /* Custom Shadow */
    }


    /* Ensuring standard elements are not broken */
    sweet-alert p {
        color: #a8afc700;
    }

    #border {
        border: 1px solid #e8e8f7;
        border-radius: 10px;
        padding-left: 30px;
        padding-top: 20px;
        padding-bottom: 20px;
        padding-right: 30px;
    }

    .card-body {
        padding-right: 80px;
        padding-left: 80px;
        padding-top: 50px;
    }

    .media-body {
        margin: 10px;
        margin-left: 20px;
    }

    .container1 {
        display: flex;
        align-items: center;
        background-color: #ffffff;
        padding: 0px;
        border-radius: 10px;
    }

    .logo1 {
        width: 20%;
        height: auto;
        margin-right: 10px;
        border-radius: 10px;
    }

    .text-content {
        /* text-align: left; */
    }

    .title {
        font-size: 16px;
        font-weight: bold;
    }

    .email {
        color: #8F8FB1;
        font-size: 14px;
    }

    .img-rd-15 {
        border-radius: 15px;
        width: 200%;
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
// Since we checked for errors and exit-ed above, $row is guaranteed to have the song data.
// We keep the while loop structure simple, but technically it's now an array from the API, not a mysqli result.
// Using an IF block is more semantically correct, but keeping the loop structure for minimal changes:
if ($row) {
    ?>
    <div class="my-5">
        <div class="card shadow-lg border-0" style="border-radius: 12px;">
            <div class="container card-body px-4 py-5">

                <div class="row">

                    <div class="content-column col-lg-8 col-md-12 col-sm-12">
                        <div class="product-carousel">
                            <div id="carousel" class="carousel slide" data-bs-ride="false">
                                <div class="carousel-inner">
                                    <div class="container my-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src=" <?= htmlspecialchars($row['image']) ?>" class=" rounded-10"
                                                height="100"                                             width="100"
                                                alt="Song Image">

                                            <div class="ms-3">
                                                <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">

                                                    <?php echo htmlspecialchars($row['name']); ?>

                                                </h2>
                                                <p class="text-muted mb-0" style="font-size: 1.0rem;">
                                                    <i class="uil uil-compact-disc text-primary"></i>

                                                    <?php echo htmlspecialchars($row['album']); // Assuming 'album' is still available or will be added to the API ?>

                                                </p>

                                            </div>

                                        </div>

                                        <div class="container my-3">
                                            <div class="d-flex flex-wrap gap-3 mt-3">

                                                <?php
                                                // Iterate over the 'genres' array from the API
                                                foreach ($row['genres'] as $genre) {
                                                    echo "<div class='d-flex align-items-center px-3 py-2 rounded-pill bg-light text-dark shadow-sm'
                                                            style='font-weight: 500;'>
                                                            " . htmlspecialchars($genre['name']) . "
                                                        </div>";
                                                }
                                                ?>

                                            </div>
                                        </div>


                                        <hr>

                                        <div class="bg-light text-dark" style=" max-height:320px; overflow-y:auto; padding:10px; border:1px solid #ccc; font-weight:500;
border-radius:10px; line-height:1.8;">

                                            <?php echo nl2br(htmlspecialchars($row['lyrics'])); ?>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="sidebar-column col-lg-4 col-md-12 col-sm-12">

                        <div class="d-flex justify-content-center gap-3 pt-2 ">
                            <a href='edit_songs.php?sid=<?= htmlspecialchars($row['sid']) ?>' class='btn btn-md btn-success'
                                style="padding: 10px 25px; border-radius: 8px;">
                                <i class='uil uil-pen me-1'></i> Edit Song
                            </a>

                            <a href='delete.php?sid=<?= htmlspecialchars($row['sid']) ?>'          
                                class="btn btn-md btn-danger delete-song-alert"                          
                                data-sid="<?= htmlspecialchars($row['sid']) ?>"                            
                                style="padding: 10px 25px; border-radius: 8px;">
                                <i class='uil uil-trash-alt me-1'></i> Delete
                            </a>
                        </div>
                        <br>
                        <br>
                        <div class="container">
                            <div class="d-flex flex-column gap-4 bg-light p-4 rounded-10 shadow-sm">

                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 45px; height: 45px;">
                                        <b class="uil uil-music text-primary" style="font-size: 1.35rem;"></b>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.10rem;" class="text-dark">Song Name</strong>
                                        <small class="text-muted d-block" style="font-size: 1rem;">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 45px; height: 45px;">
                                        <i class="uil uil-user-square text-primary" style="font-size: 1.35rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.10rem;" class="text-dark">Artists</strong>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3">

                                    <?php
                                    // Iterate over the 'singers' array from the API
                                    foreach ($row['singers'] as $singer) {
                                        echo "<div class='d-flex align-items-center px-3 py-2 rounded-pill bg-white text-dark shadow-sm'
                                            style='font-weight: 500;'>
                                            " . htmlspecialchars($singer['name']) . "
                                        </div>";
                                    }
                                    ?>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 45px; height: 45px;">
                                        <i class="uil uil-language text-primary" style="font-size: 1.35rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.10rem;" class="text-dark">Languages</strong>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3">

                                    <?php
                                    // Use the comma-separated string from the API
                                    $language_names = htmlspecialchars($row['languages']);
                                    if (!empty($language_names)) {
                                        $languages = explode(', ', $language_names);
                                        foreach ($languages as $lang_name) {
                                            echo "<div class='d-flex align-items-center px-3 py-2 rounded-pill bg-white text-dark shadow-sm'
                                                style='font-weight: 500;'>
                                                " . $lang_name . "
                                            </div>";
                                        }
                                    } else {
                                        echo "<div class='text-muted'>No language tagged.</div>";
                                    }
                                    ?>
                                </div>

                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 45px; height: 45px;">
                                        <i class="uil uil-clock-eight text-primary" style="font-size: 1.35rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.10rem;" class="text-dark">Duration</strong>
                                        <small class="text-muted d-block" style="font-size: 1rem;">
                                            <?= htmlspecialchars($row['length']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-12 mt-4">
                        <div class="d-flex flex-wrap gap-4 bg-light p-4 rounded-10 shadow-sm">

                            <div class="flex-grow-1 d-flex flex-column align-items-center">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 40px; height: 40px;">
                                        <i class="uil uil-image text-primary" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.0rem;" class="text-dark">Poster</strong>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"                  
                                    data-bs-target="#posterModal">
                                    <i class="uil uil-expand-arrows-alt"></i> View Poster
                                </button>
                            </div>

                            <div class="flex-grow-1 d-flex flex-column align-items-center">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 40px; height: 40px;">
                                        <i class="uil uil-music-note text-primary" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.0rem;" class="text-dark">Instrumental Track</strong>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"                  
                                    data-bs-target="#instrumentalModal">
                                    <i class="uil uil-expand-arrows-alt"></i> Listen
                                    Instrumental Track
                                </button>
                            </div>

                            <div class="flex-grow-1 d-flex flex-column align-items-center">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"  
                                        style="width: 40px; height: 40px;">
                                        <i class="uil uil-microphone text-primary" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.0rem;" class="text-dark">Vocal Track</strong>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"                  
                                    data-bs-target="#vocalModal">
                                    <i class="uil uil-expand-arrows-alt"></i> Listen Vocal
                                    Track
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="modal fade" id="posterModal" tabindex="-1" aria-labelledby="posterModalLabel"              
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="card modal-content shadow border-0" style="border-radius: 15px;">
                                <div class="modal-header border-0 pb-0">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="uil uil-image text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 1.0rem;" class="text-dark">Poster</strong>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"        
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center pt-2">
                                    <img src="<?= htmlspecialchars($row['poster']) ?>" alt="Song Poster"                    
                                        class="img-fluid rounded" style="max-height: 80vh; object-fit: contain;">
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><b><i          
                                                class="uil uil-times-square"></i>
                                            Close</b></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="instrumentalModal" tabindex="-1" aria-labelledby="instrumentalModalLabel"  
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="card modal-content text-white shadow border-0" style="border-radius: 15px;">
                                <div class="modal-header border-0 pb-0">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="uil uil-music-note text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 1.0rem;" class="text-dark">Instrumental Track</strong>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"        
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center pt-2">
                                    <p class="text-muted mb-3">Now playing:
                                        **<?= htmlspecialchars($row['name']) ?>**
                                        (Instrumental)</p>
                                    <audio controls style="width: 100%;">

                                        <source src="<?= htmlspecialchars($row['instrumental']) ?>" type="audio/mpeg">
                                        Your browser does not support the audio element.

                                    </audio>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><b><i          
                                                class="uil uil-times-square"></i>
                                            Close</b></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="vocalModal" tabindex="-1" aria-labelledby="vocalModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="card modal-content text-white shadow border-0" style="border-radius: 15px;">
                                <div class="modal-header border-0 pb-0">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="uil uil-microphone text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size: 1.0rem;" class="text-dark">Vocal Track</strong>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"        
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center pt-2">
                                    <p class="text-muted mb-3">Now playing:
                                        **<?= htmlspecialchars($row['name']) ?>**
                                        (Vocal)</p>
                                    <audio controls style="width: 100%;">

                                        <source src="<?= htmlspecialchars($row['vocal']) ?>" type="audio/mpeg">
                                        Your browser does not support the audio element.

                                    </audio>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><b><i              
                                                class="uil uil-times-square"></i>
                                            Close</b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <?php
}
// Removed the '}' of the while loop, replaced with if statement check.
?>


<?php
include('fff.php');
?>
<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script src="assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
<script src="assets/plugins/datatable/js/jszip.min.js"></script>
<script src="assets/plugins/datatable/pdfmake/pdfmake.min.js"></script>
<script src="assets/plugins/datatable/pdfmake/vfs_fonts.js"></script>
<script src="assets/plugins/datatable/js/buttons.html5.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.print.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.colVis.min.js"></script>
<script src="assets/plugins/datatable/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatable/responsive.bootstrap5.min.js"></script>
<script src="assets/js/table-data.js"></script>
<script src="assets/js/select2.js"></script>

<script>
    // ખાતરી કરો કે DOM સંપૂર્ણપણે લોડ થઈ ગયો છે
    document.addEventListener('DOMContentLoaded', () => {



        // Instrumental Track Modal: Pauses audio when modal is closed
        $('#instrumentalModal').on('hidden.bs.modal', function () {
            // Find the audio element within the modal and pause it
            $(this).find('audio')[0].pause();
        });

        // Vocal Track Modal: Pauses audio when modal is closed
        $('#vocalModal').on('hidden.bs.modal', function () {
            // Find the audio element within the modal and pause it
            $(this).find('audio')[0].pause();
        });

    });

    // --- SweetAlert Logic for Delete ---

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

    // Function to extract the Song ID from the delete link's href
    function extractSongId(url) {
        var match = url.match(/[?&]sid=(\d+)/);
        return match ? match[1] : null;
    }


    // Handler for the delete button click
    $('.delete-song-alert').on('click', function (e) {
        e.preventDefault();

        var deleteUrl = $(this).attr('href');
        var sid_value = $(this).data('sid'); // Use the data-sid attribute for robustness

        if (!sid_value) {
            // Fallback to extract from URL if data-sid is missing
            sid_value = extractSongId(deleteUrl);
            if (!sid_value) {
                showErrorAlert("Song ID not found for deletion.");
                return;
            }
        }

        // Main Confirmation Pop-up
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this song!",
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
                        text: "Please wait while we delete the song.",
                        type: "info",
                        showConfirmButton: false,
                    });

                    // AJAX Call to deleteSong.php
                    $.ajax({
                        url: 'http://localhost/SIngIT/flutter_crud/deleteSong.php', // Song Delete API
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
                                    timer: 2000
                                });

                                setTimeout(function () {
                                    // Reloading the page or redirecting to the view_songs list
                                    window.location.href = 'view_songs.php';
                                }, 2000);

                            } else {
                                showErrorAlert(response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            showErrorAlert(
                                "Server error or connection failed. Please check the network.");
                        }
                    });

                } else {
                    swal({
                        title: "Cancelled",
                        text: "Your song is safe :)",
                        type: "error",
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
    });
</script>
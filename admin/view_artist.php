<?php
include('demo.php');
include('hhh.php');
include('connection.php');

// --- Dynamic URL Logic ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SIngIT/flutter_crud/";

$apiUrl = $base_url . "getArtists.php"; 

// Use @ to suppress warnings if the API is down, and default to an empty array
$response = @file_get_contents($apiUrl);
$artists = $response ? json_decode($response, true) : [];
if (!is_array($artists)) {
    $artists = [];
}
?>

<style>
    .btn-md {
        font-size: 20px;
    }
    .artist-delete-btn {
        cursor: pointer;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Artists</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Artists</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Artist</h6><br>
                </div>
                <div class="table-responsive">
                    <table id="exportexample"
                        class="table table-bordered border-t0 key-buttons text-nowrap w-100 artist_table">
                        <thead>
                            <tr class="text text-center">
                                <th></th>
                                <th><b>Name</b></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($artists as $row): 
                                // Image fallback logic
                                $photo_src = !empty($row['photo']) ? htmlspecialchars($row['photo']) : 'assets/img/default_profile.png';
                            ?>
                                <tr class="text text-center">
                                    <td>
                                        <img src='<?= $photo_src ?>'
                                            style='height: 60px; width: 45px; object-fit: cover;' class='rounded'>
                                    </td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($row['name'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="align-middle">
                                        <a href='view_artist_details.php?arid=<?= $row['arid'] ?>'><i class='uil uil-eye btn btn-md btn-primary'></i></a>
                                    </td>
                                    <td class="align-middle">
                                        <a href='edit_artist.php?arid=<?= $row['arid'] ?>'><i class='uil uil-pen btn btn-md btn-success'></i></a>
                                    </td>
                                    <td class="align-middle">
                                        <a href='delete.php?arid=<?= $row['arid'] ?>' class="artist-delete-btn" data-arid="<?= $row['arid'] ?>"> 
                                            <i class='uil uil-trash-alt btn btn-md btn-danger'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
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
$(document).ready(function() {
    function showErrorAlert(message) {
        swal({
            title: "Error",
            text: message,
            type: "error",
            confirmButtonClass: "btn btn-danger",
            confirmButtonText: "Ok",
        });
    }

    $('.artist-delete-btn').on('click', function(e) {
        e.preventDefault();
        
        var arid_value = $(this).data('arid'); 

        if (!arid_value) {
            showErrorAlert("Artist ID not found for deletion.");
            return;
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
                    // Dynamic relative path to your delete API
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
                                timer: 1500
                            });

                            // Reload the page to refresh the datatable
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);

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
                    timer: 1500
                });
            }
        });
    });
});
</script>
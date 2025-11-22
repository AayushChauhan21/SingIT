<?php
// FILE: view_genres.php

include('demo.php');
include('hhh.php');
include('connection.php'); // Used for session management or other internal logic

error_reporting(1);

$apiUrl = "http://localhost/SIngIT/flutter_crud/getGenres.php";
$genres = [];

$response = @file_get_contents($apiUrl);

if ($response !== FALSE) {
    $genres = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($genres)) {
        $genres = [];
    }
}

?>

<style>
    .btn-md {
        font-size: 20px;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Genres</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Genres</li>
        </ol>
    </div>

</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Genres</h6><br>
                </div>
                <div class="table-responsive">
                    <table id="exportexample"
                        class="table table-bordered border-t0 key-buttons text-nowrap w-100 genre_table">
                        <thead>
                            <tr class="text text-center">
                                <th></th>
                                <th><b>Genres Name</b></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($genres) && count($genres) > 0): ?>
                                <?php foreach ($genres as $row): ?>
                                    <tr class="text text-center">
                                        <td>
                                            <img src='<?= htmlspecialchars($row['image']) ?>' class='rounded' height="38"
                                                width="60" alt="Genre Image">
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['name']) ?>
                                        </td>
                                        <td>
                                            <a href='view_genre_details.php?gid=<?= htmlspecialchars($row['gid']) ?>'><i
                                                    class='uil uil-eye btn btn-md btn-primary'></i></a>
                                        </td>
                                        <td>
                                            <a href="edit_genres.php?gid=<?= htmlspecialchars($row['gid']) ?>"><i
                                                    class="uil uil-pen btn btn-md btn-success"></i></a>
                                        </td>
                                        <td>
                                            <a href='delete.php?gid=<?= htmlspecialchars($row['gid']) ?>'
                                                class="genre-delete-btn" data-gid="<?= htmlspecialchars($row['gid']) ?>">
                                                <i class='uil uil-trash-alt btn btn-md btn-danger'></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No genres found.</td>
                                </tr>
                            <?php endif; ?>
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

<script src="assets/plugins/sweet-alert/sweetalert.min.js"></script>
<script src="assets/plugins/sweet-alert/jquery.sweet-alert.js"></script>


<script>
    // --- SweetAlert Trigger Functions ---
    function showSuccessAlert(msg) {
        swal({
            title: 'Well done!',
            text: msg,
            type: 'success',
            confirmButtonColor: '#57a94f'
        });
    }

    function showErrorAlert(msg) {
        swal({
            title: 'Oops!',
            text: msg,
            type: 'error',
            confirmButtonColor: '#ff0000'
        });
    }

    $(document).ready(function () {

        // --- Session Status Check Logic ---
        <?php if (isset($_SESSION['status'])): ?>

            var status = '<?php echo $_SESSION['status']; ?>';
            var message = '<?php echo addslashes($_SESSION['message']); ?>';

            if (status === 'success') {
                showSuccessAlert(message);
            } else {
                showErrorAlert(message);
            }

            <?php unset($_SESSION['status']); ?>
            <?php unset($_SESSION['message']); ?>

        <?php endif; ?>

        // --- 🗑️ GENRE DELETE SWEETALERT LOGIC (List View) ---
        $('.genre-delete-btn').on('click', function (e) {
            e.preventDefault();

            var gid_value = $(this).data('gid');

            if (!gid_value) {
                showErrorAlert("Genre ID not found for deletion.");
                return;
            }

            // 1. Confirmation Pop-up
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this genre! All associated songs will lose their genre tag.",
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
                        // Show "Deleting" message
                        swal({
                            title: "Deleting...",
                            text: "Please wait while we delete the genre.",
                            type: "info",
                            showConfirmButton: false,
                        });

                        // 2. AJAX Call to deleteGenre.php
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
                                        text: response.message ||
                                            "Genre deleted successfully.",
                                        type: "success",
                                        showConfirmButton: false,
                                        timer: 2000
                                    });

                                    // 3. Reload the page after successful deletion
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);

                                } else {
                                    showErrorAlert(response.message ||
                                        "Failed to delete genre. Please try again.");
                                }
                            },
                            error: function (xhr, status, error) {
                                showErrorAlert(
                                    "Server error or connection failed. Deletion failed."
                                );
                            }
                        });

                    } else {
                        // Deletion cancelled
                        swal({
                            title: "Cancelled",
                            text: "Your genre is safe :)",
                            type: "error",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
        });
    });
</script>
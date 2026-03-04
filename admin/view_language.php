<?php
include('demo.php');
include('hhh.php');
include('connection.php');

// --- Dynamic URL Logic ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SIngIT/flutter_crud/";

$apiUrl = $base_url . "getLanguage.php"; 

// Fetch data safely
$response = @file_get_contents($apiUrl);
$Language = $response ? json_decode($response, true) : [];
if (!is_array($Language)) {
    $Language = [];
}
?>

<style>
    .btn-md {
        font-size: 20px;
    }
    .language-delete-btn {
        cursor: pointer;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Language</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Language</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Language</h6><br>
                </div>
                <div class="table-responsive">
                    <table id="exportexample"
                        class="table table-bordered border-t0 key-buttons text-nowrap w-100 language_table">
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
                            <?php if (!empty($Language)): ?>
                                <?php foreach ($Language as $row): 
                                    // Image fallback logic
                                    $img_src = !empty($row['image']) ? htmlspecialchars($row['image']) : 'assets/img/transparent_placeholder.png';
                                ?>
                                    <tr class="text text-center">
                                        <td class="align-middle">
                                            <img src='<?= $img_src ?>'
                                                style='height: 45px; width: 60px; object-fit: cover;' class='rounded' alt="Language Image">
                                        </td>
                                        <td class="align-middle">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </td>
                                        <td class="align-middle">
                                            <a href='view_language_details.php?lid=<?= $row['lid'] ?>'><i class='uil uil-eye btn btn-md btn-primary'></i></a>
                                        </td>
                                        <td class="align-middle">
                                            <a href='edit_language.php?lid=<?= $row['lid'] ?>'><i class='uil uil-pen btn btn-md btn-success'></i></a>
                                        </td>
                                        <td class="align-middle">
                                            <a href='delete.php?lid=<?= htmlspecialchars($row['lid']) ?>'
                                                class="language-delete-btn" data-lid="<?= htmlspecialchars($row['lid']) ?>"> 
                                                <i class='uil uil-trash-alt btn btn-md btn-danger'></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No languages found.</td>
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

    $('.language-delete-btn').on('click', function(e) {
        e.preventDefault();
        
        var lid_value = $(this).data('lid'); 

        if (!lid_value) {
            showErrorAlert("Language ID not found for deletion.");
            return;
        }

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this language and its associated song tags!",
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
                    text: "Please wait while we delete the language.",
                    type: "info",
                    showConfirmButton: false,
                });

                $.ajax({
                    // Dynamic relative path to your delete API
                    url: '../flutter_crud/deleteLanguage.php',
                    type: 'POST',
                    data: { lid: lid_value },
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
                            showErrorAlert(response.message || "Failed to delete language.");
                        }
                    },
                    error: function(xhr, status, error) {
                        showErrorAlert("Server error or connection failed. Please check the network and API URL.");
                    }
                });

            } else {
                swal({
                    title: "Cancelled",
                    text: "Your language is safe :)",
                    type: "error",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
});
</script>
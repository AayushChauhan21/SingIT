<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

// Set a valid default image path
$default_img_path = 'assets/img/transparent_placeholder.png';

// --- Dynamic URL Logic ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . "/SIngIT/flutter_crud/";

// 🔹 Fetch slider data from API
$apiUrl = $base_url . "Slider.php";
$sliders = [];

$response = @file_get_contents($apiUrl);
if ($response !== FALSE) {
    $sliders = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($sliders)) {
        $sliders = [];
    }
}
?>

<style>
    .btn-md {
        font-size: 20px;
    }
    .slider-delete-btn {
        cursor: pointer;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Sliders</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Sliders</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Sliders</h6><br>
                </div>
                <div class="table-responsive slider_table">
                    <table id="exportexample"
                        class="table table-bordered border-t0 key-buttons text-nowrap w-100 genre_table">
                        <thead>
                            <tr class="text text-center">
                                <th></th>
                                <th>Song Name</th>
                                <th>Singer(s)</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sliders)): ?>
                                <?php foreach ($sliders as $row):
                                    // 🟢 Default Image Logic
                                    $img_src = !empty($row['image']) ? htmlspecialchars($row['image']) : $default_img_path;

                                    // 🟢 Split names and IDs
                                    $singers = explode(', ', $row['singer_name'] ?? '');
                                    $artist_ids = explode(', ', $row['artist_ids'] ?? '');
                                    ?>
                                    <tr class="text text-center">
                                        <td class="align-middle">
                                            <img src="<?= $img_src ?>" class="rounded" height="38" width="60" style="object-fit: cover;" alt="Song Poster">
                                        </td>
                                        <td class="align-middle"><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>

                                        <td class="align-middle">
                                            <?php
                                            foreach ($singers as $index => $singer):
                                                // Get the corresponding ID
                                                $artist_id = $artist_ids[$index] ?? '';
                                                // Link uses arid parameter
                                                $link_href = "view_artist_details.php?arid=" . urlencode($artist_id);
                                                ?>
                                                <a href="<?= $link_href ?>" style="text-decoration: none;">
                                                    <span class="badge rounded-pill text-light bg-primary"
                                                        style="font-weight: normal; font-size: 13px; padding: 5px 15px; margin: 2px;">
                                                        <?= htmlspecialchars($singer) ?>
                                                    </span>
                                                </a>
                                            <?php endforeach; ?>
                                        </td>

                                        <td class="align-middle">
                                            <a href="edit_slider.php">
                                                <i class="uil uil-pen btn btn-md btn-success"></i>
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <a href="delete.php?id=<?= htmlspecialchars($row['id'] ?? '') ?>"
                                                class="slider-delete-btn"> <i
                                                    class="uil uil-trash-alt btn btn-md btn-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No slider entries found.</td>
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
    // --- SweetAlert Helper Functions ---
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

    // Function to extract ID from URL
    function extractId(url, paramName) {
        if (!url || !url.includes('?')) return 0;
        var urlParams = new URLSearchParams(url.split('?')[1]);
        return urlParams.get(paramName) || 0;
    }

    function extractSliderId(url) {
        return extractId(url, 'id');
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

        // --- 🗑️ SLIDER DELETE SWEETALERT LOGIC ---
        $('.slider-delete-btn').on('click', function (e) {
            e.preventDefault();

            var deleteUrl = $(this).attr('href');
            var slider_id = extractSliderId(deleteUrl);

            if (!slider_id || slider_id == 0) {
                showErrorAlert("Slider ID not found for deletion.");
                return;
            }

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this slider entry!",
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
                        text: "Please wait while we remove it.",
                        type: "info",
                        showConfirmButton: false,
                    });

                    // AJAX Call to deleteSlider.php
                    $.ajax({
                        // RELATIVE PATH FIX
                        url: '../flutter_crud/deleteSlider.php',
                        type: 'POST',
                        data: { id: slider_id },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                swal({
                                    title: "Deleted!",
                                    text: response.message || "Slider deleted successfully.",
                                    type: "success",
                                    showConfirmButton: false,
                                    timer: 1500
                                });

                                setTimeout(function () {
                                    window.location.reload();
                                }, 1500);

                            } else {
                                showErrorAlert(response.message || "Failed to delete slider. Please try again.");
                            }
                        },
                        error: function (xhr, status, error) {
                            showErrorAlert("Server error or connection failed. Deletion failed.");
                        }
                    });

                } else {
                    swal({
                        title: "Cancelled",
                        text: "Your slider is safe :)",
                        type: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    });
</script>
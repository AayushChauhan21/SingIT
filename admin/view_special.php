<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

// Default image path defined at the top
// $default_img_path = 'favicon_1.png'; // Default path assumption

// 🔹 Fetch special song data from API
// NOTE: Assuming your Special.php file contains the optimized SQL query you provided:
// (SELECT sl.id, s.sid, s.name, s.poster AS image, GROUP_CONCAT(a.name) AS singer_name, GROUP_CONCAT(DISTINCT a.arid) AS artist_ids FROM special sl JOIN song s...)
$apiUrl = "http://localhost/SIngIT/flutter_crud/getSpecial.php";
$special_songs = [];

$response = @file_get_contents($apiUrl);
if ($response !== FALSE) {
    $special_songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($special_songs)) {
        $special_songs = [];
    }
}
?>
<!-- <link rel="stylesheet" href="style_adm.css"> -->
<style>
    .btn-md {
        font-size: 20px;
    }
</style>


<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Special Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Special Songs</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Special Songs</h6><br>
                </div>
                <div class="table-responsive special_table">
                    <table id="exportexample" class="table table-bordered border-t0 key-buttons text-nowrap w-100">
                        <thead>
                            <tr class="text text-center">
                                <th></th>
                                <th>Song Name</th>
                                <th>Singer(s)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($special_songs)): ?>
                                <?php foreach ($special_songs as $row):
                                    // Image Logic
                                    $img_src = !empty($row['image']) ? htmlspecialchars($row['image']) : $default_img_path;

                                    // Split names and IDs (Requires API to send 'artist_ids')
                                    $singers = explode(', ', $row['singer_name'] ?? '');
                                    $artist_ids = explode(', ', $row['artist_ids'] ?? '');
                                    ?>
                                    <tr class="text text-center">
                                        <td>
                                            <img src="<?= $img_src ?>" class="rounded" height="38" width="60" alt="Song Poster">
                                        </td>
                                        <td><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>

                                        <td>
                                            <?php
                                            foreach ($singers as $index => $singer):
                                                $artist_id = $artist_ids[$index] ?? '';
                                                // Link uses arid parameter for redirection
                                                $link_href = "view_artist_details.php?arid=" . urlencode($artist_id);
                                                ?>
                                                <a href="<?= $link_href ?>" style="text-decoration: none;">
                                                    <span class="badge rounded-pill text-light bg-primary"
                                                        style="font-weight: normal; font-size: 13px; padding: 5px 15px;">
                                                        <?= htmlspecialchars($singer) ?>
                                                    </span>
                                                </a>
                                            <?php endforeach; ?>
                                        </td>

                                        <td>
                                            <a href="edit_special.php">
                                                <i class="uil uil-pen btn btn-md btn-success"></i>
                                            </a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No special entries found.</td>
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
<!-- END MAIN-CONTENT -->

<!-- RIGHT-SIDEBAR -->

<?php
include('fff.php');
?>




<!-- INTERNAL DATA TABLE JS -->
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

<!-- INTERNAL SWEET-ALERT JS -->
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

    // Function to extract ID from URL (essential for the delete logic)
    function extractId(url, paramName) {
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

    });
</script>
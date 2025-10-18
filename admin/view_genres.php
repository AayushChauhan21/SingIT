<?php
include('demo.php');
include('hhh.php');
include('connection.php');

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

<!-- <link rel="stylesheet" href="style_adm.css"> -->
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

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Genres</h6><br>
                    <!-- <p class="text-muted card-sub-title">Exporting data from a table can often be a
                        key part of a complex application. The Buttons extension for DataTables
                        provides three plug-ins that provide overlapping functionality for data
                        export:</p> -->
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($genres) && count($genres) > 0): ?>
                                <?php foreach ($genres as $row): ?>
                                    <tr class="text text-center">
                                        <!-- echo "<td><image src='./images/" . $row['image'] . "'class='rounded' height=50 width=50></td>"; -->

                                        <td>
                                            <img src='<?= htmlspecialchars($row['image']) ?>' class='rounded' height="50"
                                                width="50" alt="Genre Image">
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['name']) ?>
                                        </td>
                                        <td>
                                            <a href="edit_genres.php?gid=<?= htmlspecialchars($row['gid']) ?>"><i
                                                    class="uil uil-pen btn btn-md btn-success"></i></a>
                                        </td>
                                        <td>
                                            <a href='delete.php?gid=<?= htmlspecialchars($row['gid']) ?>'
                                                class="genre-delete-btn"> <i
                                                    class='uil uil-trash-alt btn btn-md btn-danger'></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->


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
    // --- SweetAlert Trigger Functions ---
    // Success Trigger Function
    function showSuccessAlert(msg) {
        swal({
            title: 'Well done!',
            text: msg,
            type: 'success',
            confirmButtonColor: '#57a94f'
        });
    }

    // Error Trigger Function (Failure ke liye)
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

            // Status hisabe fun. call thase
            if (status === 'success') {
                // jQuery event trigger thai
                showSuccessAlert(message);
            } else {
                showErrorAlert(message);
            }

            // Message dekhaya pachi session ne hataidev
            <?php unset($_SESSION['status']); ?>
            <?php unset($_SESSION['message']); ?>

        <?php endif; ?>

    });
</script>
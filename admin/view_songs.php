<?php

include('demo.php');
include('hhh.php');
include('connection.php');

error_reporting(1);

$apiUrl = "http://localhost/SIngIT/flutter_crud/getSongs.php";
$songs = [];

$response = @file_get_contents($apiUrl);

if ($response !== FALSE) {
    $songs = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($songs)) {
        $songs = [];
    }
}
?>

<style>
    /* Custom CSS */
    .btn-md {
        font-size: 18.5px;
    }

    .rounded {
        border-radius: 50%;
    }

    sweet-alert p {
        color: #a8afc700;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View All Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Songs</li>
        </ol>
    </div>
</div>

<div class="row row-sm mb-2">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-3">Song List</h6>
                </div>

                <div class="table-responsive">
                    <table id="exportexample"
                        class="table table-bordered border-t0 key-buttons text-nowrap w-100 song_table">
                        <thead>
                            <tr class="text text-center">
                                <th></th>
                                <th><b>Name</b></th>
                                <th><b>Length</b></th>
                                <th><b>Album</b></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($songs) && count($songs) > 0): ?>
                                <?php foreach ($songs as $row): ?>
                                    <tr class="text text-center">
                                        <td>
                                            <img src='<?= htmlspecialchars($row['image']) ?>' class='rounded' height=50 width=50
                                                alt="Song Image">
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['name']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['length']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['album']) ?>
                                        </td>
                                        <td>
                                            <a href='view_song_details.php?sid=<?= htmlspecialchars($row['sid']) ?>'><i
                                                    class='uil uil-eye btn btn-md btn-primary'></i></a>
                                        </td>
                                        <td>
                                            <a href='edit_songs.php?sid=<?= htmlspecialchars($row['sid']) ?>'><i
                                                    class='uil uil-pen btn btn-md btn-success'></i></a>
                                        </td>
                                        <td>
                                            <a href='delete.php?sid=<?= htmlspecialchars($row['sid']) ?>'
                                                class="song-delete-btn">
                                                <i class='uil uil-trash-alt btn btn-md btn-danger'></i>
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
<?php
include('fff.php');
// Yahaan DataTables, jQuery, aur SweetAlert ki files include honi chahiye
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
<!-- <script src="assets/plugins/sweet-alert/sweetalert.min.js"></script>
<script src="assets/plugins/sweet-alert/jquery.sweet-alert.js"></script>
 -->

<script>

</script>


<!-- <script src="assets/js_sweet_alert/deleteSong.js"></script> -->
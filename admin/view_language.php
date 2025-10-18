<?php
include('demo.php');
include('hhh.php');
include('connection.php');

$apiUrl = "http://localhost/flutter_crud/getLanguage.php"; // API
$response = file_get_contents($apiUrl);
$Language = json_decode($response, true);

?>

<!-- <link rel="stylesheet" href="style_adm.css"> -->

<style>
    .btn-md {
        font-size: 20px;
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

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Language</h6><br>
                    <!-- <p class="text-muted card-sub-title">Exporting data from a table can often be a
                        key part of a complex application. The Buttons extension for DataTables
                        provides three plug-ins that provide overlapping functionality for data
                        export:</p> -->
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
                            <?php foreach ($Language as $row): ?>
                                <tr class="text text-center">
                                    <!-- echo "<td><image src='./images/" . $row['image'] . "'class='rounded' height=50 width=50></td>"; -->

                                    <td>
                                        <img src='<?= htmlspecialchars($row['image']) ?>'
                                            style='height: 45px; width: 60px; object-fit: cover;' class='rounded'>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>
                                    <td>
                                        <?php echo "<a href='view_language_details.php?lid=$row[lid]'><i class='uil uil-eye btn btn-md btn-primary'></i></a>"; ?>
                                    </td>
                                    <td>
                                        <a href='edit_language.php?lid=<?= $row['lid'] ?>'><i
                                                class='uil uil-pen btn btn-md btn-success'></i></a>
                                    </td>
                                    <td>
                                        <a href='delete.php?lid=<?= htmlspecialchars($row['lid']) ?>'
                                            class="language-delete-btn"> <i
                                                class='uil uil-trash-alt btn btn-md btn-danger'></i>
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
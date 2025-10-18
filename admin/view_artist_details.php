<?php
include('demo.php');
include('hhh.php');
include('connection.php');

$arid = $_GET['arid'];

$apiUrl = "http://localhost/flutter_crud/getArtistDetails.php?arid=$arid"; // Replace with dynamic arid
$response = file_get_contents($apiUrl);
$artist = json_decode($response, true);

?>


<style>
    .status-g {
        display: inline-block;
        padding: 10px 20px;
        background-color: #e6f4ea;
        color: #34a853;
        border-radius: 20px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: bold;
    }

    .btn-success1 {
        display: inline-block;
        padding: 10px 20px;
        background-color: #e6f4ea;
        color: #34a853;
        border-radius: 20px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: bold;
        transition: 0.3s;
        margin-left: 10px;
    }

    .btn-success1:hover {
        background-color: #34a853;
        color: white;
    }

    .btn-danger1 {
        display: inline-block;
        padding: 10px 20px;
        background-color: rgba(217, 48, 37, 0.15);
        color: #D93025;
        border-radius: 20px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: bold;
        margin-left: 10px;
    }

    .btn-danger1:hover {
        background-color: #D93025;
        color: white;
    }

    .status-r {
        display: inline-block;
        padding: 10px 20px;
        background-color: rgba(217, 48, 37, 0.15);
        color: #D93025;
        border-radius: 20px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: bold;
    }

    .status-y {
        display: inline-block;
        padding: 10px 20px;
        background-color: rgba(249, 171, 0, 0.15);
        color: #F9AB00;
        border-radius: 20px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: bold;
    }

    .status-p {
        display: inline-block;
        padding: 10px 20px;
        background: rgba(241, 126, 217, 0.15);
        color: #F186CE;
        border-radius: 20px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        font-weight: bold;
    }

    .btn-md {
        font-size: 17px;
        border-radius: 5px;
        font-weight: bold;
    }

    #border {
        border: 1px solid lightgray;
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

    .rounded4 {
        max-width: 12.5%;
        /* padding-right: 0px; */
        /* margin: 10px; */
        /* border-radius: 100px; */

    }
</style>


<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">View Artist Details</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="view_artist.php">View Artist</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Artist Details</li>
        </ol>
    </div>

</div>
<!-- End Page Header -->

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card productdesc">
            <div class="card-body h-100">
                <div class="row">

                    <?php if (!empty($artist) && empty($artist['error'])): ?>


                        <div class="col-xl-12" id="cnter">
                            <div class="row">

                                <div class="col-md-12 col-sm-9 col-8">
                                    <!-- <div class="col-md-10 offset-md-1 col-sm-9 col-8"> -->
                                    <div class="product-carousel">
                                        <div id="carousel" class="carousel slide" data-bs-ride="false">
                                            <div class="carousel-inner">
                                                <div class="setimg">
                                                    <div class="img">
                                                        <!-- <img src="https://i.pinimg.com/originals/26/ea/fc/26eafc0b14488fea03fa8fa9751203ff.jpg"> -->
                                                        <img src="<?= htmlspecialchars($artist['photo']) ?>"
                                                            class="rounded-20"
                                                            style="height: 210px; width:170px; object-fit: cover; object-position: top;">
                                                        <b class="tx-40 mx-3"><?= htmlspecialchars($artist['name']) ?></b>
                                                        <!-- <b class="tx-28 mx-3">HR Name</b> -->
                                                        <!-- <?php
                                                        if ($status == 1) {
                                                            echo "<div class='status-g'>Approved</div>";
                                                        } elseif ($status == 2) {
                                                            echo "<div class='status-r'>Rejected</div>";
                                                        } else {
                                                            echo "<div class='status-y'>Pending</div>";
                                                        }
                                                        ?> -->

                                                        <td>
                                                            <a href='edit_artist.php?arid=<?= $row['arid'] ?>'><button
                                                                    class="btn btn-md btn-success1" onclick="not7()"><i
                                                                        class='uil uil-pen'></i> Edit</button></a>
                                                        </td>
                                                        <td>
                                                            <a href='delete.php?arid=<?= $row['arid'] ?>'><button
                                                                    class="btn btn-md btn-danger1" onclick="not7()">
                                                                    <i class='uil uil-trash-alt'></i> Delete</button></a>
                                                        </td>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- man thai to chnaage kar j  -->

                                            <!-- <div class="text-center mt-4 mb-4 btn-list">
                                            <a href="ecommerce-cart.html" class="btn ripple btn-primary"><i
                                                    class="fe fe-shopping-cart"> </i> Add to
                                                cart</a>
                                            <a href="javascript:void(0);" class="btn ripple btn-secondary me-2"><i
                                                    class="fe fe-credit-card"> </i> Buy Now</a>
                                        </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <b class="tx-26">Songs</b>
                                <div class="">
                                    <div class="row">
                                        <!-- table che... -->
                                        <!-- <div class="col-xl-12">
                                <div class="table-responsive">
                                    <table class="table mb-0 border-top table-bordered text-nowrap">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Category</th>
                                                <td>Watches</td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->

                                        <div class="col-xl-12 mt-4">
                                            <div class="card">
                                                <div class="card-body p-0 mb-3 mt-3">
                                                    <?php foreach ($artist['songs'] as $song): ?>
                                                        <div class="p-4 border-bottom border-top">
                                                            <!-- Section 1: Image + Info -->
                                                            <div class="d-flex align-items-center">
                                                                <img src="<?= htmlspecialchars($song['image']) ?>"
                                                                    class="rounded4"
                                                                    style="border-radius: 15%; width: 80px; height: 80px; object-fit: cover; margin-right: 15px;">

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
                                                                            &nbsp;<?= htmlspecialchars($song['length']) ?>
                                                                        </span>
                                                                    </div>

                                                                    <span class="text-muted tx-15"><i class="uil uil-okta"></i>
                                                                        <?= htmlspecialchars($song['album']) ?>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <!-- Section 2: Buttons -->
                                                            <div class="d-flex justify-content-between mt-3">
                                                                <!-- Section 1: Edit + Block -->
                                                                <div class="d-flex gap-2">
                                                                    <a href="edit_job.php?sid=<?= $song['sid']; ?>"
                                                                        class="btn btn-success btn-md"><i
                                                                            class="uil uil-pen"></i> Edit</a> &nbsp;
                                                                    <a href="block_job.php?sid=<?= $song['sid']; ?>"
                                                                        class="btn btn-danger btn-md"><i
                                                                            class="uil uil-trash-alt"></i> Delete</a>
                                                                </div>

                                                                <!-- Section 2: View Details -->
                                                                <div>
                                                                    <a href="view_song_details.php?sid=<?= $song['sid']; ?>"
                                                                        class="btn btn-primary btn-md"><i
                                                                            class="fal fa-long-arrow-right ms-3"></i> View Job
                                                                        Details</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-danger">Artist not found.</div>
                    <?php endif; ?>


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


</div>
<!-- END PAGE -->

<!-- SCRIPTS -->

<?php
include('fff.php');
?>


<script>
    function not7() {
        notif({
            msg: "<b>Approved : </b>Please Wait... Sending A Mail...",
            type: "success"
        });
    }
</script>


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
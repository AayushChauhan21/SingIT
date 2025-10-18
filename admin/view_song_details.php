<?php
include('demo.php');
include('hhh.php');
include('connection.php');


$sid = $_GET['sid'];


// $qry = mysqli_query($con, "select * from job_master where sid=$ssid");
// while ($row = mysqli_fetch_array($qry)) {
//     $jname = $row['jname'];
//     $jdetails = $row['jdetails'];
//     $req_exp = $row['req_exp'];
//     $req_lang = $row['req_lang'];
//     $req_qual = $row['req_qual'];
//     $job_type = $row['job_type'];
//     $timing = $row['timing'];
//     $min_salary = $row["min_salary"];
//     $max_salary = $row["max_salary"];
//     $status = $row['status'];
//     $photo = $row['photo'];
// }

// $dest = "./image/" . $photo;
// $temp = 0;

// ?>


<style>
    .btn-md {
        font-size: 18.5px;
        font-weight: bold;
    }

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
        /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */
    }

    .logo1 {
        /* max-width: 20%;
    height: 20%; */
        width: 20%;
        height: auto;
        margin-right: 10px;
        /* padding-right: 0px; */
        /* margin: 10px; */
        border-radius: 10px;
    }

    .text-content {
        /* text-align: left; */
    }

    .title {
        font-size: 16px;
        font-weight: bold;
        /* margin: 0 0 10px 0; */
    }

    .email {
        color: #8F8FB1;
        font-size: 14px;
    }

    .img-rd-15 {
        border-radius: 15px;
        /* max-width: 100%; */
        width: 200%;
    }

    sweet-alert p {
        color: #a8afc700;
    }
</style>


<!-- Page Header -->
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
<!-- End Page Header -->


<?php
$temp = 0;
$q = mysqli_query($con, "select * from song where sid=$sid");
while ($row = mysqli_fetch_array($q)) {
    ?>
    <div class="my-5">
        <div class="card shadow-lg border-0" style="border-radius: 12px;">
            <div class="container card-body px-4 py-5">

                <div class="row">

                    <div class="content-column col-lg-8 col-md-12 col-sm-12">
                        <!-- <div class="col-md-10 offset-md-1 col-sm-9 col-8"> -->
                        <div class="product-carousel">
                            <div id="carousel" class="carousel slide" data-bs-ride="false">
                                <div class="carousel-inner">
                                    <div class="container my-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <!-- Logo -->
                                            <img src=" <?php echo $row['image']; ?>" class=" rounded-10" height="100"
                                                width="100" alt="Company Logo">

                                            <!-- Text Block -->
                                            <div class="ms-3">
                                                <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">
                                                    <?php echo $row['name']; ?>
                                                </h2>
                                                <p class="text-muted mb-0" style="font-size: 1.0rem;">
                                                    <i class="uil uil-compact-disc text-primary"></i>
                                                    <?php echo $row['album']; ?>
                                                </p>

                                            </div>

                                        </div>

                                        <div class="container my-3">
                                            <div class="d-flex flex-wrap gap-3 mt-3">

                                                <?php
                                                $song_id = $row['sid']; // current song ID
                                            
                                                $genre_id = $row['sid']; // current song ID
                                            
                                                // Get all genre IDs linked to this song
                                                $genreMap = mysqli_query($con, "SELECT genre_id FROM genre_song WHERE song_id = $song_id");

                                                while ($map = mysqli_fetch_array($genreMap)) {
                                                    $genre_id = $map['genre_id'];

                                                    // Get genre details
                                                    $genreQry = mysqli_query($con, "SELECT * FROM genre WHERE gid = $genre_id");
                                                    while ($genre = mysqli_fetch_array($genreQry)) {
                                                        // <img src='$genre[photo]' alt='genre' class='logo1 mb-2'/>
                                                        echo "<div class='d-flex align-items-center px-3 py-2 rounded-pill bg-light text-dark shadow-sm'
                                                                style='font-weight: 500;'>
                                                                $genre[name]
                                                            </div>
                                                            ";
                                                    }
                                                }
                                                ?>

                                            </div>
                                        </div>

                                        <hr>

                                        <div class="bg-light text-dark" style=" max-height:345px; overflow-y:auto; padding:10px; border:1px solid #ccc; font-weight:500;
                                        border-radius:10px; line-height:1.8;">
                                            <?php echo nl2br(htmlspecialchars($row['lyrics'])); ?>
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



                    <div class="sidebar-column col-lg-4 col-md-12 col-sm-12">

                        <!-- <div class="container my-5">
                            <div class="text-center bg-light p-4 rounded shadow-sm" style="max-width: 400px; margin: auto;">
                                <h5 class="fw-bold text-dark mb-2">Application ends</h5>
                                <p class="fs-5 text-muted mb-4">May 18, 2026</p>
                                <a href="#" class="btn btn-primary btn-lg px-4">
                                    Apply For Job <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div> -->

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
                                        <i class="uil uil-user-square text-primary" style="font-size: 1.35rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.10rem;" class="text-dark">Artists</strong>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3">

                                    <?php
                                    $song_id = $row['sid']; // current song ID
                                
                                    // Get all artist IDs linked to this song
                                    $artistMap = mysqli_query($con, "SELECT artist_id FROM artist_song WHERE song_id = $song_id");

                                    while ($map = mysqli_fetch_array($artistMap)) {
                                        $artist_id = $map['artist_id'];

                                        // Get artist details
                                        $artistQry = mysqli_query($con, "SELECT * FROM artist WHERE arid = $artist_id");
                                        while ($artist = mysqli_fetch_array($artistQry)) {
                                            // <img src='$artist[photo]' alt='artist' class='logo1 mb-2'/>
                                            echo "<div class='d-flex align-items-center px-3 py-2 rounded-pill bg-white text-dark shadow-sm'
                                                        style='font-weight: 500;'>
                                                        $artist[name]
                                                    </div>
                                            ";
                                        }
                                    }
                                    ?>

                                    <!-- <div class="d-flex align-items-center px-3 py-2 rounded-pill bg-white text-dark shadow-sm"
                                        style="font-weight: 500;">
                                        Full Time
                                    </div> -->

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
                                    // Get all language IDs linked to this song
                                    $languageMap = mysqli_query($con, "SELECT language_id FROM language_song WHERE song_id = $song_id");

                                    if (mysqli_num_rows($languageMap) > 0) {
                                        while ($map = mysqli_fetch_array($languageMap)) {
                                            $language_id = $map['language_id'];

                                            // Get language details
                                            $languageQry = mysqli_query($con, "SELECT name FROM language WHERE lid = $language_id");
                                            while ($lang = mysqli_fetch_array($languageQry)) {
                                                echo "<div class='d-flex align-items-center px-3 py-2 rounded-pill bg-white text-dark shadow-sm'
                            style='font-weight: 500;'>
                            " . htmlspecialchars($lang['name']) . "
                        </div>";
                                            }
                                        }
                                    } else {
                                        echo "<div class='text-muted'>No language tagged.</div>";
                                    }
                                    ?>
                                </div>

                                <hr
                                    style="border: none; height: 1px; background-color: var(--primary-bg-color); margin: 0;">

                                <!-- Song Name -->
                                <!-- Song Name -->
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

                                <!-- Duration -->
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



                                <!-- Album Name -->
                                <!-- <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"
                                        style="width: 45px; height: 45px;">
                                        <i class="uil uil-compact-disc text-primary" style="font-size: 1.35rem;"></i>
                                    </div>
                                    <div>
                                                <strong style="font-size: 1.10rem;">Album Name</strong>
                                        <small class="text-muted d-block"
                                            style="font-size: 1rem;"><?php echo $row['album']; ?></small>
                                    </div>
                                                </div> -->


                            </div>
                        </div>
                    </div>


                    <div class="d-flex gap-4 bg-light p-4 rounded-10 shadow-sm">

                        <div class="flex-grow-1" style="min-width: 45%;">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="uil uil-music-note text-primary" style="font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <strong style="font-size: 1.0rem;" class="text-dark">Poster</strong>
                                </div>
                            </div>
                            <button type="button">hi</button>
                        </div>

                        <?php if (!empty($row['instrumental'])): ?>
                            <div class="flex-grow-1" style="min-width: 45%;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="uil uil-music-note text-primary" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.0rem;" class="text-dark">Instrumental
                                            Track</strong>
                                    </div>
                                </div>
                                <audio controls style="width: 100%;" class="mt-1">
                                    <source src="<?= htmlspecialchars($row['instrumental']) ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($row['vocal'])): ?>
                            <div class="flex-grow-1" style="min-width: 45%;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-white rounded-circle d-flex justify-content-center align-items-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="uil uil-microphone text-primary" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.0rem;" class="text-dark">Vocal Track</strong>
                                    </div>
                                </div>
                                <audio controls style="width: 100%;" class="mt-1">
                                    <source src="<?= htmlspecialchars($row['vocal']) ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        <?php endif; ?>

                    </div>


                </div>


            </div>
        </div>
    </div>
    <?php
}
?>


<!-- SCRIPTS -->


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

<script>
    // ખાતરી કરો કે DOM સંપૂર્ણપણે લોડ થઈ ગયો છે
    document.addEventListener('DOMContentLoaded', () => {

        // SweetAlert માટે jQuery નો ઉપયોગ કરીને event listener સેટ કરો
        $('.delete-song-alert').on('click', function (e) {
            e.preventDefault(); // Delete link પર સીધા જવાને અટકાવો

            // બટનમાંથી delete URL અને song ID મેળવો
            var deleteUrl = $(this).attr('href');
            var songId = $(this).data('sid');

            // SweetAlert કન્ફર્મેશન પોપ-અપ બતાવો
            swal({
                title: "Are you sure?",
                text: "તમે આ ગીત (ID: " + songId + ") ને પુનઃપ્રાપ્ત કરી શકશો નહીં!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            },
                function (isConfirm) {
                    if (isConfirm) {
                        // જો યુઝર કન્ફર્મ કરે, તો delete URL પર રીડાયરેક્ટ કરો
                        swal("Deleted!", "ગીત સફળતાપૂર્વક ડિલીટ થઈ ગયું છે.", "success");
                        // થોડા સમય પછી રીડાયરેક્ટ કરો જેથી યુઝર 'Deleted!' મેસેજ જોઈ શકે
                        setTimeout(function () {
                            window.location.href = deleteUrl;
                        }, 500);
                    } else {
                        // જો યુઝર કેન્સલ કરે
                        swal("Cancelled", "ગીત ફાઇલ સુરક્ષિત છે :)", "error");
                    }
                });
        });
    });
</script>
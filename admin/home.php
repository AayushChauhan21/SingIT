<?php
include('demo.php');
include('hhh.php');
include('connection.php');

?>

<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

<style>
    .main-sidebar-hide .side-menu .main-logo .icon-logo {
        display: block;
        margin-left: 17px;
        padding-top: 10px
    }

    .btn-md {
        /* padding: 0rem 3rem; */
        font-size: 1.1rem;
        /* border-radius: 10px; */
    }


    /* Ensure the image covers the container and has smooth edges */
    .carousel-item img {
        /* height: 250px; */
        object-fit: cover;
        border-radius: 0.75rem;
        /* rounded-xl */
    }

    /* Customizing the image size inside the carousel for consistency */
    .carousel-inner .carousel-item img {
        /* Set a fixed height for all carousel slides */
        height: 302px;
        /* તમે આ વેલ્યુ બદલી શકો છો (e.g., 300px) */
        /* Ensure the image covers the entire container, cropping the edges if necessary, but maintaining aspect ratio. */
        object-fit: cover;
        /* Optional: Ensure image is block level and takes full container width */
        display: block;
        width: 100%;
    }

    /* Gradient Caption for better readability */
    .carousel-caption {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.1));
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem 1.5rem;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        /* Match the inner border radius */
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }

    /* Customizing the Bootstrap indicator color for contrast */
    .carousel-indicators [data-bs-target] {
        background-color: white;
        opacity: 0.7;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        margin: 0 4px;
    }

    .carousel-indicators .active {
        background-color: #3b82f6;
        /* Tailwind blue-500 */
        opacity: 1;
    }

    /* --- ARROW CENTERING FIX --- */
    .carousel-control-prev,
    .carousel-control-next {
        /* Force the control button to occupy the full height of the carousel */
        height: 100%;
        display: flex;
        /* Use flexbox to center content */
        align-items: center;
        /* Vertically center the content */
        opacity: 0.8;
        /* Make them slightly more visible */
        transition: opacity 0.2s;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 1;
    }
</style>


<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Dashboard</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="http://127.0.0.1:8000/admin/dashboard">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Project Dashboard</li>
        </ol>
    </div>

</div>
<!-- End Page Header -->


<!--Row-->
<div class="row row-sm">

    <div class="col-sm-12 col-lg-12 col-xl-9">

        <!--Row-->
        <div class="row row-sm  mt-lg-4">
            <div class="col-sm-12 col-lg-12 col-xl-12">
                <!-- ama gradient kari sakai -->
                <div class="card custom-card card-box" id="gradient">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="offset-xl-3 offset-sm-6 col-xl-8 col-sm-6 col-12 img-bg ">
                                <h4 class="d-flex mb-3">
                                    <span class="font-weight-bold mx-5 pt-3 text-white">
                                        Welcome To Dashboard, <?php echo htmlspecialchars($admin['name']); ?>
                                    </span>
                                </h4>

                            </div>
                            <img src="assets/img/pngs/work3.png" alt="user-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Row -->

        <!--Row-->
        <div class="row row-sm">
            <!-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <h4 class="card-item-icon card-icon">

                                <i class="uil uil-chart text-primary"></i>
                            </h4>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                                    Sales</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                    <h4 class="font-weight-bold">₹ 5318</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- aa ne badal vanu che external ma -->
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <h4 class="card-item-icon card-icon">

                                <i class="uil uil-list-ul text-primary"></i>
                            </h4>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                                    Genre</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                    <h4 class="font-weight-bold">
                                        <?= $counts['genres'] ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <h4 class="card-item-icon card-icon">

                                <i class="uil uil-language text-primary"></i>
                            </h4>
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                                    Language</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                    <h4 class="font-weight-bold">
                                        <?= $counts['languages'] ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <h4 class="card-item-icon card-icon">

                                <i class="uil uil-image text-primary"></i>
                            </h4>
                            <div class="card-item-title  mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                                    Slider</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                    <h4 class="font-weight-bold">
                                        <?= $counts['sliders'] ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <h4 class="card-item-icon card-icon">

                                <i class="uil uil-user text-primary"></i>
                            </h4>
                            <div class="card-item-title  mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                                    Users</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item-stat">
                                    <h4 class="font-weight-bold">
                                        <?= $counts['users'] ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End row-->

        <!-- <div class="">
            <div class="card custom-card mg-b-20">
                <div class="card-body">
                    <div class="border-bottom-0 pt-0 pe-0 d-flex">
                        <div>
                            <label class="main-content-label mb-2">Revenue Genereted By Perticular
                                Books</label>
                            <span class="d-block tx-12 mb-3 text-muted">Recent 5 Revenue Genereted By
                                Perticular
                                Books</span>
                        </div>
                        <div class="ms-auto">
                            <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                            <div class="dropdown-menu">

                                <a class="dropdown-item" href="http://127.0.0.1:8000/admin/revenue-by-books">
                                    <b><i class="uil uil-window"></i>
                                        View All</b></a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive tasks">
                        <table class="table card-table table-vcenter text-nowrap mb-0 border">
                            <thead>
                                <tr>
                                    <th class="wd-lg-20p text-center p-3">Book<br>Image</th>
                                    <th class="wd-lg-20p text-center p-3">Book<br>Name</th>
                                    <th class="wd-lg-20p text-center p-3">Post<br>By</th>
                                    <th class="wd-lg-20p text-center p-3">Actual<br>Price</th>
                                    <th class="wd-lg-20p text-center p-3">Unit<br>Soled</th>
                                    <th class="wd-lg-20p text-center">Total<br>Revenue<br>Generated</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="font-weight-semibold">
                                        <center>
                                            <img src="http://127.0.0.1:8000/uploads/books/images/f150086a-d401-4608-939d-87c6a46df04b_image.jpg"
                                                class='rounded d-flex justify-content-center align-items-center'
                                                alt="book_img" height="50" width="35">
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <center>
                                            <div
                                                style="width: 150px; max-width: 150px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                                Alita Battle Angel
                                            </div>
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <h6 class="tx-13">Aayush Chauhan</h6>
                                    </td>
                                    <td class="text-center">
                                        ₹ 1500
                                    </td>
                                    <td class="text-center">
                                        2
                                    </td>
                                    <td class="text-center">
                                        ₹ 3000
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-semibold">
                                        <center>
                                            <img src="http://127.0.0.1:8000/uploads/books/images/cafa9fdf-2acb-405c-9b80-c96ec2902bf9_image.jpg"
                                                class='rounded d-flex justify-content-center align-items-center'
                                                alt="book_img" height="50" width="35">
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <center>
                                            <div
                                                style="width: 150px; max-width: 150px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                                Arcane - League of Legends
                                            </div>
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <h6 class="tx-13">Dhruv Sariya</h6>
                                    </td>
                                    <td class="text-center">
                                        ₹ 1200
                                    </td>
                                    <td class="text-center">
                                        1
                                    </td>
                                    <td class="text-center">
                                        ₹ 1200

                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-semibold">
                                        <center>
                                            <img src="http://127.0.0.1:8000/uploads/books/images/06488f80-a71d-46e7-8f0a-e7d406576eef_image.png"
                                                class='rounded d-flex justify-content-center align-items-center'
                                                alt="book_img" height="50" width="35">
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <center>
                                            <div
                                                style="width: 150px; max-width: 150px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                                STAR WARS HIDDEN EMPIRE #5
                                            </div>
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <h6 class="tx-13">Dhruv Sariya</h6>
                                    </td>
                                    <td class="text-center">
                                        ₹ 850
                                    </td>
                                    <td class="text-center">
                                        1

                                    </td>
                                    <td class="text-center">
                                        ₹ 850

                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-semibold">
                                        <center>
                                            <img src="http://127.0.0.1:8000/uploads/books/images/ef43ba26-2f0c-4916-96d2-88c22c77b2b4_image.jpg"
                                                class='rounded d-flex justify-content-center align-items-center'
                                                alt="book_img" height="50" width="35">
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <center>
                                            <div
                                                style="width: 150px; max-width: 150px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                                Transformers #1
                                            </div>
                                        </center>
                                    </td>
                                    <td class="text-center">
                                        <h6 class="tx-13">Stark</h6>
                                    </td>
                                    <td class="text-center">
                                        ₹ 268
                                    </td>
                                    <td class="text-center">
                                        1

                                    </td>
                                    <td class="text-center">
                                        ₹ 268

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->


    </div><!-- col end -->

    <div class="col-sm-12 col-lg-12 col-xl-3">

        <div class="card custom-card" style="margin-top: 21.5px;">
            <div class="card-body">
                <div class="card-item">
                    <h4 class="card-item-icon card-icon">

                        <i class="uil uil-user-square text-primary"></i>
                    </h4>
                    <div class="card-item-title mb-2">
                        <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                            Artist</label>
                    </div>
                    <div class="card-item-body">
                        <div class="card-item-stat">
                            <h4 class="font-weight-bold"><?= $counts['artists'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body">
                <div class="card-item">
                    <h4 class="card-item-icon card-icon">

                        <i class="uil uil-music text-primary"></i>
                    </h4>
                    <div class="card-item-title mb-2">
                        <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                            Songs</label>
                    </div>
                    <div class="card-item-body">
                        <div class="card-item-stat">
                            <h4 class="font-weight-bold"><?= $counts['songs'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- <div class="card custom-card card-dashboard-calendar pb-0">

            <div class="border-bottom-0 pt-0 pe-0 d-flex">
                <div>
                    <label class="main-content-label mb-2">Latest Transactions</label>
                    <span class="d-block tx-12 mb-3 text-muted">Last 5 Transactions</span>
                </div>
                <div class="ms-auto">
                    <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                    <div class="dropdown-menu">

                        <a class="dropdown-item" href="http://127.0.0.1:8000/admin/transactions">
                            <b><i class="uil uil-window"></i>
                                View All</b></a>
                    </div>
                </div>
            </div>


            <table class="table table-hover m-b-0 transcations mt-2">
                <tbody>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded-circle me-3"
                                    src="http://127.0.0.1:8000/uploads/users/default_image.jpg">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Aayush C</h6>
                                    <p class="mb-0 tx-13 text-muted">ayuchauhan397@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹ 1500</h6>
                                <p class="mb-0 tx-11 text-muted">30-05-2025 05:26:33</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded-circle me-3"
                                    src="http://127.0.0.1:8000/uploads/users/84295ecd-0ff4-430c-b818-98cef83d7004_image.jpg">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Dharmesh</h6>
                                    <p class="mb-0 tx-13 text-muted">dharmesh@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹ 1200</h6>
                                <p class="mb-0 tx-11 text-muted">29-05-2025 18:18:13</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded-circle me-3"
                                    src="http://127.0.0.1:8000/uploads/users/84295ecd-0ff4-430c-b818-98cef83d7004_image.jpg">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Dharmesh</h6>
                                    <p class="mb-0 tx-13 text-muted">dharmesh@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹ 1500</h6>
                                <p class="mb-0 tx-11 text-muted">29-05-2025 18:12:00</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded-circle me-3"
                                    src="http://127.0.0.1:8000/uploads/users/3865b448-5695-4447-bc22-67bb1d63dc3e_image.png">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Aryan</h6>
                                    <p class="mb-0 tx-13 text-muted">aryansariya009@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹ 1118</h6>
                                <p class="mb-0 tx-11 text-muted">27-05-2025 14:58:30</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> -->



        <!-- <div class="card custom-card card-dashboard-calendar pb-0">


            <div class="border-bottom-0 pt-0 pe-0 d-flex">
                <div>
                    <label class="main-content-label mb-2">Author's Payables</label>
                    <span class="d-block tx-12 mb-3 text-muted">Last 5 Author's Payables Details</span>
                </div>
                <div class="ms-auto">
                    <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                    <div class="dropdown-menu">

                        <a class="dropdown-item" href="http://127.0.0.1:8000/admin/authors-payables">
                            <b><i class="uil uil-window"></i>
                                View All</b></a>
                    </div>
                </div>
            </div>
            <table class="table table-hover m-b-0 transcations mt-2">
                <tbody>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded me-3"
                                    src="http://127.0.0.1:8000/uploads/users/9900fab0-3624-48a6-b078-43d062651141_profile.jpg">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Aayush Chauhan</h6>
                                    <p class="mb-0 tx-13 text-muted">chauhanaayush367@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹
                                    2,400
                                </h6>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded me-3"
                                    src="http://127.0.0.1:8000/uploads/users/ab0ccb02-965a-4b30-89ba-2159e87932dd_profile.jpg">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Dhruv Sariya</h6>
                                    <p class="mb-0 tx-13 text-muted">dsariyaj5@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹
                                    1,640
                                </h6>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="wd-5p">
                            <div class="main-img-user avatar-md">
                                <img alt="avatar" class="rounded me-3"
                                    src="http://127.0.0.1:8000/uploads/users/28c8204c-dc6a-48a4-9f06-c384e079e333_profile.jpg">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-middle ms-3">
                                <div class="d-inline-block">
                                    <h6 class="mb-1">Stark</h6>
                                    <p class="mb-0 tx-13 text-muted">stark172737@gmail.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-block">
                                <h6 class="mb-2 tx-15 font-weight-semibold">₹
                                    214
                                </h6>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> -->

    </div><!-- col end -->

    <!-- slider -->
    <!-- <div class="col-lg-6 col-xl-4 col-md-6">
        <div class="card custom-card">
            <div class="card-body h-100">
                <div>
                    <h6 class="main-content-label mb-1">With Caption</h6>
                    <p class="text-muted card-sub-title">Add captions to your slides easily with the
                        <code>.carousel-caption</code> element within any <code>.carousel-item.</code>
                    </p>
                </div>
                <div>
                    <div class="carousel slide" data-bs-ride="carousel" id="carouselExample4">
                        <ol class="carousel-indicators">
                            <li class="" data-bs-slide-to="0" data-bs-target="#carouselExample4"></li>
                            <li data-bs-slide-to="1" data-bs-target="#carouselExample4" class="active"
                                aria-current="true"></li>
                            <li data-bs-slide-to="2" data-bs-target="#carouselExample4" class=""></li>
                        </ol>
                        <div class="carousel-inner bg-dark">
                            <div class="carousel-item">
                                <img alt="img" class="d-block w-100 op-3" src="../assets/img/media/17.jpg">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>First Slide</h5>
                                    <p class="tx-14">Praesent commodo cursus magna, vel scelerisque nisl consectetur.
                                    </p>
                                </div>
                            </div>
                            <div class="carousel-item active">
                                <img alt="img" class="d-block w-100 op-3" src="../assets/img/media/18.jpg">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>Second Slide</h5>
                                    <p class="tx-14">Praesent commodo cursus magna, vel scelerisque nisl consectetur.
                                    </p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img alt="img" class="d-block w-100 op-3" src="../assets/img/media/19.jpg">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>Third Slide</h5>
                                    <p class="tx-14">Praesent commodo cursus magna, vel scelerisque nisl consectetur.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- slider -->

    <div class="col-lg-6 col-md-12">
        <div class="card custom-card">
            <div class="card-body h-100">
                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Today's Special</label>
                        <span class="d-block tx-12 mb-3 text-muted">Today's Special Song</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="view_special.php">
                                <b><i class="uil uil-window"></i> View All</b></a>
                        </div>
                    </div>
                </div>

                <ul id="special-gallery" class="list-unstyled row mb-0">
                    <?php if (!empty($recent_special)): ?>
                        <?php
                        // Use a column class that allows images to sit next to each other, e.g., col-4 for 3 images per row
                        $display_count = min(count($recent_special), 6);
                        ?>
                        <?php for ($i = 0; $i < $display_count; $i++):
                            $row = $recent_special[$i];
                            ?>
                            <li class="col-12">
                                <a href="view_song_details.php?sid=<?= urlencode($row['sid'] ?? '') ?>" class="wd-100p"
                                    title="<?= htmlspecialchars($row['song_name'] ?? 'Song') ?>">
                                    <img class="img-responsive img-in-grid"
                                        src="<?= htmlspecialchars($row['song_poster'] ?? 'assets/img/default.png') ?>"
                                        alt="<?= htmlspecialchars($row['song_name'] ?? 'Song') ?>">
                                </a>
                            </li>
                        <?php endfor; ?>
                    <?php else: ?>
                        <li class="col-12 text-center text-muted tx-12 py-3">No special song set.</li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="card custom-card">
            <div class="card-body h-100">

                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Slider</label>
                        <span class="d-block tx-12 mb-3 text-muted">Slider's Songs</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="view_slider.php">
                                <b><i class="uil uil-window"></i> View All</b></a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($recent_sliders)): ?>
                    <div class="">
                        <div id="mainSliderCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                            data-bs-interval="3000">

                            <div class="carousel-indicators">
                                <?php foreach ($recent_sliders as $index => $row): ?>
                                    <button type="button" data-bs-target="#mainSliderCarousel" data-bs-slide-to="<?= $index ?>"
                                        class="<?= $index === 0 ? 'active' : '' ?>"
                                        aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                                        aria-label="Slide <?= $index + 1 ?>"></button>
                                <?php endforeach; ?>
                            </div>

                            <div class="carousel-inner rounded-xl">
                                <?php foreach ($recent_sliders as $index => $row):

                                    $img_src_1 = htmlspecialchars(empty($row['song_poster']) ? 'perfect1.png' : $row['song_poster']);

                                    $song_name = htmlspecialchars($row['song_name'] ?? 'Untitled Song');
                                    ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <a href="view_song_details.php?sid=<?= urlencode($row['sid']) ?>"
                                            title="<?= $song_name ?>">
                                            <img src="<?= $img_src_1 ?>" class="d-block w-100" alt="<?= $song_name ?>">
                                        </a>

                                        <div class="carousel-caption d-none d-md-block text-start">
                                            <h5 class="mb-0 text-xl font-bold text-white"><?= $song_name ?></h5>
                                            <p class="text-sm text-gray-300 mb-0">
                                                <?= htmlspecialchars($row['artist_names'] ?? 'Artist(s)') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#mainSliderCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#mainSliderCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>

                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-3 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <p class="text-center text-gray-500 text-sm py-8">No slider images set. Add images via the Edit link
                            above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="col-md-6 genre_table">
        <div class="card custom-card mg-b-20">
            <div class="card-body">
                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Genres</label>
                        <span class="d-block tx-12 mb-3 text-muted">Recent 5 Genres Details</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">

                            <a class="dropdown-item" href="view_genres.php">
                                <b><i class="uil uil-window"></i>
                                    View All</b></a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive tasks">
                    <table class="table card-table table-vcenter text-nowrap mb-0 border">
                        <thead>
                            <tr>
                                <th class="wd-lg-20p text-center">Image</th>
                                <th class="wd-lg-20p text-center">Name</th>
                                <th class="wd-lg-20p text-center">Edit</th>
                                <th class="wd-lg-20p text-center">Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($recent_genres as $row):
                                // 🚀 Genres Image માટે ફૉલબૅક લોજિક
                                $genre_image_src = !empty($row['image']) ? htmlspecialchars($row['image']) : 'assets/img/favicon_1.png';
                                ?>
                                <tr>
                                    <td class="font-weight-semibold">
                                        <center>
                                            <img src="<?= $genre_image_src ?>" class=" rounded" height="38" width="60"
                                                alt="Genre Image">
                                        </center>
                                    </td>

                                    <td class="text-center">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="edit_genres.php?gid=<?= $row['gid'] ?>">
                                            <i class="uil uil-pen btn btn-md btn-success"></i>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href='delete.php?gid=<?= htmlspecialchars($row['gid']) ?>'
                                            class="genre-delete-btn">
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


    <div class="col-md-6 language_table">
        <div class="card custom-card mg-b-20">
            <div class="card-body">
                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Language</label>
                        <span class="d-block tx-12 mb-3 text-muted">Recent 5 Language Details</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">

                            <a class="dropdown-item" href="view_language.php">
                                <b><i class="uil uil-window"></i>
                                    View All</b></a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive tasks">
                    <table class="table card-table table-vcenter text-nowrap mb-0 border">
                        <thead>
                            <tr>
                                <th class="wd-lg-20p text-center">Image</th>
                                <th class="wd-lg-20p text-center">Name</th>
                                <th class="wd-lg-20p text-center">Edit</th>
                                <th class="wd-lg-20p text-center">Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($recent_languages as $row):
                                // 🚀 Language Image માટે ફૉલબૅક લોજિક
                                $language_image_src = !empty($row['image']) ? htmlspecialchars($row['image']) : 'assets/img/favicon_1.png';
                                ?>
                                <tr>
                                    <td class="font-weight-semibold">
                                        <center>
                                            <img src="<?= $language_image_src ?>" class=" rounded" height="38" width="60"
                                                alt="Language Image">
                                        </center>
                                    </td>

                                    <td class="text-center">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="edit_languages.php?lid=<?= $row['lid'] ?>">
                                            <i class="uil uil-pen btn btn-md btn-success"></i>
                                        </a>
                                    </td>

                                    <td class="text-center">
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

    <div class="col-lg-12 artist_table">
        <div class="card custom-card mg-b-20">
            <div class="card-body">
                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Artists</label>
                        <span class="d-block tx-12 mb-3 text-muted">Recent 5 Artists Details</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">

                            <a class="dropdown-item" href="view_artist.php">
                                <b><i class="uil uil-window"></i>
                                    View All</b></a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive tasks">
                    <table class="table card-table table-vcenter text-nowrap mb-0 border">
                        <thead>
                            <tr>
                                <th class="wd-lg-10p text-center">Image</th>
                                <th class="wd-lg-20p text-center">Name</th>
                                <th class="wd-lg-28p text-center">Description</th>
                                <th class="wd-lg-10p text-center">Image(Transparent)</th>
                                <th class="wd-lg-10p text-center">Edit</th>
                                <th class="wd-lg-10p text-center">Delete</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($recent_artists as $row):
                                // 🚀 photo માટે ફૉલબૅક લોજિક
                                $artist_photo_src = !empty($row['photo']) ? htmlspecialchars($row['photo']) : 'favicon_1.png';
                                // 🚀 image માટે ફૉલબૅક લોજિક
                                $artist_image_src = !empty($row['image']) ? htmlspecialchars($row['image']) : 'favicon_1.png';
                                ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <img src="<?= $artist_photo_src ?>"
                                            style="height: 60px; width: 45px; object-fit: cover;" class="rounded"
                                            alt="Artist Photo">
                                    </td>

                                    <td class="text-center">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </td>

                                    <td class="text-center align-middle">
                                        <div class="bg-light text-dark" style="
                                        /* Set max width to control column size */
                                        max-width: 400px; 
                                        /* Set max height to control row height */
                                        max-height: 80px; 
                                        /* Enable vertical scrollbar only if needed */
                                        overflow-y: auto; 
                                        /* Hide horizontal scrollbar */
                                        overflow-x: hidden; 
                                        
                                        /* ESSENTIAL FIX: Force long words/strings to wrap */
                                        word-wrap: break-word; 
                                        overflow-wrap: break-word;
                                        white-space: normal; /* Ensure text wrapping is active */
                                        
                                        /* Visual styling */
                                        
                                        font-size: 0.875rem;
                                        padding: 8px; 
                                        border-radius: 6px;
                                    ">
                                            <?php
                                            $description = $row['description'] ?? ''; // PHP 7.0+ Null Coalescing Operator
                                            echo htmlspecialchars(!empty($description) ? $description : "No description...");
                                            ?>
                                        </div>
                                    </td>

                                    <td class="text-center align-middle">
                                        <img src="<?= $artist_image_src ?>"
                                            style="height: 60px; width: 45px; object-fit: cover;" class="rounded"
                                            alt="Artist Image">
                                    </td>

                                    <td class="text-center">
                                        <a href="edit_artist.php?arid=<?= $row['arid'] ?>">
                                            <i class="uil uil-pen btn btn-md btn-success"></i>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="delete.php?arid=<?= $row['arid'] ?>" class="artist-delete-btn"> <i
                                                class="uil uil-trash-alt btn btn-md btn-danger"></i>
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

    <div class="col-md-12 song_table" id="exportexample">
        <div class="card custom-card mg-b-20">
            <div class="card-body">
                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Songs</label>
                        <span class="d-block tx-12 mb-3 text-muted">Recent 5 Songs Details</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="view_songs.php">
                                <b><i class="uil uil-window"></i> View All</b></a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive tasks">
                    <table class="table card-table table-vcenter text-nowrap mb-0 border">
                        <thead>
                            <tr>
                                <th class="wd-lg-10p text-center">Image</th>
                                <th class="wd-lg-10p text-center">Name</th>
                                <th class="wd-lg-10p text-center">Artist(s)</th>
                                <th class="wd-lg-10p text-center">Genre(s)</th>
                                <th class="wd-lg-10p text-center">Album</th>
                                <th class="wd-lg-10p text-center">Length</th>
                                <th class="wd-lg-10p text-center">Edit</th>
                                <th class="wd-lg-10p text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($recent_songs) && is_array($recent_songs)):
                                foreach ($recent_songs as $row):
                                    // 🚀 અહીં ફેરફાર કર્યો છે 🚀
                                    // જો $row['image'] ખાલી હોય, તો 'favicon_1.png' નો ઉપયોગ કરો.
                                    $song_image = !empty($row['image']) ? htmlspecialchars($row['image']) : 'favicon_1.png';
                                    ?>
                                    <tr>
                                        <td class="font-weight-semibold">
                                            <center>
                                                <img src="<?= $song_image ?>" class="rounded" height="50" width="50"
                                                    alt="Song Image">
                                            </center>
                                        </td>

                                        <td class="text-center">
                                            <h6 class="tx-13"><?= htmlspecialchars($row['name'] ?? 'N/A') ?></h6>
                                        </td>

                                        <td class="text-center">
                                            <h6 class="tx-13">
                                                <?php if (!empty($row['artist_names']) && !empty($row['artist_ids'])): ?>
                                                    <?php
                                                    $artist_names = explode(', ', $row['artist_names']);
                                                    $artist_ids = explode(', ', $row['artist_ids']);
                                                    foreach ($artist_names as $index => $artist_name):
                                                        $artist_id = isset($artist_ids[$index]) ? $artist_ids[$index] : '';
                                                        ?>
                                                        <a href="view_artist_details.php?arid=<?= urlencode($artist_id) ?>"
                                                            style="text-decoration: none;">
                                                            <span class="badge rounded-pill text-light bg-primary"
                                                                style="font-weight: normal; font-size: 13px; padding: 5px 15px;">
                                                                <?= htmlspecialchars($artist_name) ?>
                                                            </span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-danger">Unknown</span>
                                                <?php endif; ?>
                                            </h6>
                                        </td>

                                        <td class="text-center">
                                            <h6 class="tx-13">
                                                <?php if (!empty($row['genre_names']) && !empty($row['genre_ids'])): ?>
                                                    <?php
                                                    $genre_names = explode(', ', $row['genre_names']);
                                                    $genre_ids = explode(', ', $row['genre_ids']);
                                                    foreach ($genre_names as $index => $genre_name):
                                                        $genre_id = isset($genre_ids[$index]) ? $genre_ids[$index] : '';
                                                        ?>
                                                        <a href="view_genre_details.php?gid=<?= urlencode($genre_id) ?>"
                                                            style="text-decoration: none;">
                                                            <span class="badge rounded-pill text-light bg-dark"
                                                                style=" font-weight: normal; font-size: 13px; padding: 5px 15px;">
                                                                <?= htmlspecialchars($genre_name) ?>
                                                            </span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-danger">Unknown</span>
                                                <?php endif; ?>
                                            </h6>
                                        </td>

                                        <td class="text-center">
                                            <h6 class="tx-13">
                                                <?= !empty($row['album']) ? htmlspecialchars($row['album']) : '<span style="color: gray;">N/A</span>' ?>
                                            </h6>
                                        </td>

                                        <td class="text-center">
                                            <h6 class="tx-13"><?= htmlspecialchars($row['length'] ?? 'N/A') ?></h6>
                                        </td>

                                        <td class="text-center">
                                            <a href="edit_songs.php?sid=<?= $row['sid'] ?? '' ?>">
                                                <i class="uil uil-pen btn btn-md btn-success"></i>
                                            </a>
                                        </td>

                                        <td class="text-center">
                                            <a href='delete.php?sid=<?= htmlspecialchars($row['sid'] ?? '') ?>'
                                                class="song-delete-btn">
                                                <i class='uil uil-trash-alt btn btn-md btn-danger'></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No recent songs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- <div class="col-md-12">
        <div class="card custom-card mg-b-20">
            <div class="card-body">
                <div class="border-bottom-0 pt-0 pe-0 d-flex">
                    <div>
                        <label class="main-content-label mb-2">Reviews</label>
                        <span class="d-block tx-12 mb-3 text-muted">Recent 5 Reviews</span>
                    </div>
                    <div class="ms-auto">
                        <a href="javascript:void(0)" class="option-dots" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"><i class="fe fe-more-vertical"></i></a>
                        <div class="dropdown-menu">

                            <a class="dropdown-item" href="http://127.0.0.1:8000/admin/reviews">
                                <b><i class="uil uil-window"></i>
                                    View All</b></a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive tasks">
                    <table class="table card-table table-vcenter text-nowrap mb-0 border">
                        <thead>
                            <tr>
                                <th class="wd-lg-20p text-center">Image</th>
                                <th class="wd-lg-20p text-center">Book Name</th>
                                <th class="wd-lg-20p text-center">Post By</th>
                                <th class="wd-lg-20p text-center">Comment</th>
                                <th class="wd-lg-20p text-center">Review</th>
                                <th class="wd-lg-20p text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-weight-semibold">
                                    <center class="">
                                        <img src="http://127.0.0.1:8000/uploads/books/images/0bb75263-ed95-4170-a494-aedeae298111_image.jpg"
                                            class='rounded d-flex justify-content-center align-items-center'
                                            alt="book_img" height=50 width=35>
                                    </center>
                                <td class="text-center">
                                    <h6 class="tx-13">Get in Trouble</h6>
                                </td>
                                <td class="text-center">
                                    <h6 class="tx-13">

                                        Dharmesh
                                    </h6>
                                </td>
                                <td>
                                    <center>
                                        <div
                                            style="width: 300px; max-width: 300px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; align-items: center; text-align: center; justify-content: center; display: flex;">
                                            Thrill at the end of Story was Amazing🤐.
                                        </div>
                                    </center>
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-star-fill text-warning">&nbsp;&nbsp;</i><b>4.0</b>
                                    / 5
                                </td>
                                <td class="text-center">
                                    <a href="#" onclick="deleteReview(9);">
                                        <i class='uil uil-trash-alt btn btn-md btn-danger tx-16'></i></a>
                                    <form id="delete-review-form-9" action="http://127.0.0.1:8000/admin/reviews/9"
                                        method="post">
                                        <input type="hidden" name="_token"
                                            value="QgNGCyPZhZlAMrbvakiC6TLrA1HZ40PBJXLDGx85" autocomplete="off"> <input
                                            type="hidden" name="_method" value="delete">
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">
                                    <center class="">
                                        <img src="http://127.0.0.1:8000/uploads/books/images/cafa9fdf-2acb-405c-9b80-c96ec2902bf9_image.jpg"
                                            class='rounded d-flex justify-content-center align-items-center'
                                            alt="book_img" height=50 width=35>
                                    </center>
                                <td class="text-center">
                                    <h6 class="tx-13">Arcane - League of Legends</h6>
                                </td>
                                <td class="text-center">
                                    <h6 class="tx-13">

                                        Dharmesh
                                    </h6>
                                </td>
                                <td>
                                    <center>
                                        <div
                                            style="width: 300px; max-width: 300px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; align-items: center; text-align: center; justify-content: center; display: flex;">
                                            Love This Series of Comics of League of Legends💖.
                                        </div>
                                    </center>
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-star-fill text-warning">&nbsp;&nbsp;</i><b>5.0</b>
                                    / 5
                                </td>
                                <td class="text-center">
                                    <a href="#" onclick="deleteReview(8);">
                                        <i class='uil uil-trash-alt btn btn-md btn-danger tx-16'></i></a>
                                    <form id="delete-review-form-8" action="http://127.0.0.1:8000/admin/reviews/8"
                                        method="post">
                                        <input type="hidden" name="_token"
                                            value="QgNGCyPZhZlAMrbvakiC6TLrA1HZ40PBJXLDGx85" autocomplete="off"> <input
                                            type="hidden" name="_method" value="delete">
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">
                                    <center class="">
                                        <img src="http://127.0.0.1:8000/uploads/books/images/8cf97f22-faeb-4545-a7b6-48276a5d546f_image.jpg"
                                            class='rounded d-flex justify-content-center align-items-center'
                                            alt="book_img" height=50 width=35>
                                    </center>
                                <td class="text-center">
                                    <h6 class="tx-13">Romancing Bermuda</h6>
                                </td>
                                <td class="text-center">
                                    <h6 class="tx-13">

                                        Dharmesh
                                    </h6>
                                </td>
                                <td>
                                    <center>
                                        <div
                                            style="width: 300px; max-width: 300px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; align-items: center; text-align: center; justify-content: center; display: flex;">
                                            U can find better book then this one👎 , Choose Wisely my
                                            Friend.
                                        </div>
                                    </center>
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-star-fill text-warning">&nbsp;&nbsp;</i><b>2.0</b>
                                    / 5
                                </td>
                                <td class="text-center">
                                    <a href="#" onclick="deleteReview(7);">
                                        <i class='uil uil-trash-alt btn btn-md btn-danger tx-16'></i></a>
                                    <form id="delete-review-form-7" action="http://127.0.0.1:8000/admin/reviews/7"
                                        method="post">
                                        <input type="hidden" name="_token"
                                            value="QgNGCyPZhZlAMrbvakiC6TLrA1HZ40PBJXLDGx85" autocomplete="off"> <input
                                            type="hidden" name="_method" value="delete">
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">
                                    <center class="">
                                        <img src="http://127.0.0.1:8000/uploads/books/images/f150086a-d401-4608-939d-87c6a46df04b_image.jpg"
                                            class='rounded d-flex justify-content-center align-items-center'
                                            alt="book_img" height=50 width=35>
                                    </center>
                                <td class="text-center">
                                    <h6 class="tx-13">Alita Battle Angel</h6>
                                </td>
                                <td class="text-center">
                                    <h6 class="tx-13">

                                        Dharmesh
                                    </h6>
                                </td>
                                <td>
                                    <center>
                                        <div
                                            style="width: 300px; max-width: 300px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; align-items: center; text-align: center; justify-content: center; display: flex;">
                                            Amazing Book , Alita is real Champion
                                        </div>
                                    </center>
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-star-fill text-warning">&nbsp;&nbsp;</i><b>5.0</b>
                                    / 5
                                </td>
                                <td class="text-center">
                                    <a href="#" onclick="deleteReview(6);">
                                        <i class='uil uil-trash-alt btn btn-md btn-danger tx-16'></i></a>
                                    <form id="delete-review-form-6" action="http://127.0.0.1:8000/admin/reviews/6"
                                        method="post">
                                        <input type="hidden" name="_token"
                                            value="QgNGCyPZhZlAMrbvakiC6TLrA1HZ40PBJXLDGx85" autocomplete="off"> <input
                                            type="hidden" name="_method" value="delete">
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-semibold">
                                    <center class="">
                                        <img src="http://127.0.0.1:8000/uploads/books/images/06488f80-a71d-46e7-8f0a-e7d406576eef_image.png"
                                            class='rounded d-flex justify-content-center align-items-center'
                                            alt="book_img" height=50 width=35>
                                    </center>
                                <td class="text-center">
                                    <h6 class="tx-13">STAR WARS HIDDEN EMPIRE #5</h6>
                                </td>
                                <td class="text-center">
                                    <h6 class="tx-13">

                                        Dhruv Sariya
                                    </h6>
                                </td>
                                <td>
                                    <center>
                                        <div
                                            style="width: 300px; max-width: 300px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; align-items: center; text-align: center; justify-content: center; display: flex;">
                                            Overall, a great series with a nice ending.
                                        </div>
                                    </center>
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-star-fill text-warning">&nbsp;&nbsp;</i><b>5.0</b>
                                    / 5
                                </td>
                                <td class="text-center">
                                    <a href="#" onclick="deleteReview(4);">
                                        <i class='uil uil-trash-alt btn btn-md btn-danger tx-16'></i></a>
                                    <form id="delete-review-form-4" action="http://127.0.0.1:8000/admin/reviews/4"
                                        method="post">
                                        <input type="hidden" name="_token"
                                            value="QgNGCyPZhZlAMrbvakiC6TLrA1HZ40PBJXLDGx85" autocomplete="off"> <input
                                            type="hidden" name="_method" value="delete">
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->
</div>
<!-- Row end -->


<?php
include('fff.php');
?>


<!-- INTERNAL APEXCHART JS -->
<script src="assets/js/apexcharts.js"></script>

<!-- INTERNAL POLYFILLS JS -->
<script src="assets/plugins/polyfill/polyfill.min.js"></script>
<script src="assets/plugins/polyfill/classList.min.js"></script>
<script src="assets/plugins/polyfill/polyfill_mdn.js"></script>

<!-- INTERNAL JVECTORMAP JS -->
<script src="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="assets/plugins/jvectormap/gdp-data.js"></script>

<!-- ECOMMERCE DASHBOARD JS -->
<script src="assets/js/ecommerce-dashboard.js"></script>
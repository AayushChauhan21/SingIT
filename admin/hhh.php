<?php
include('connection.php');


$apiUrl = "http://localhost/SIngIT/flutter_crud/dashboard.php"; // API
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// Extract sections
$admin = $data['admin'];
$recent_songs = $data['recent_songs'];
$recent_artists = $data['recent_artists'];
$recent_genres = $data['recent_genres'];
$recent_sliders = $data['recent_sliders'];
$recent_special = $data['recent_special']; // Special Songs માટે
$recent_languages = $data['recent_languages'];
$counts = $data['counts'];


?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from php.spruko.com/spruha/spruha/pages/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 13 Dec 2023 07:38:21 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="description" content="Spruha - PHP Admin Panel Dashboard Template">
    <meta name="author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
        content="php dashboard, php template, admin dashboard bootstrap, bootstrap admin theme, admin, php admin panel, bootstrap admin template, admin dashboard template, admin template bootstrap, php admin dashboard, dashboard template, dashboard template bootstrap, bootstrap admin, admin panel template, dashboard">

    <!-- TITLE -->
    <title> SingIT - Admin </title>

    <!-- FAVICON -->
    <!-- <link rel="icon" href="https://php.spruko.com/spruha/spruha/assets/img/brand/favicon.ico"> -->
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICONS CSS -->
    <link href="assets/plugins/web-fonts/icons.css" rel="stylesheet">
    <link href="assets/plugins/web-fonts/font-awesome/font-awesome.min.css" rel="stylesheet">
    <link href="assets/plugins/web-fonts/plugin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <!-- STYLE CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/plugins.css" rel="stylesheet">

    <!-- SWITCHER CSS -->
    <link href="assets/switcher/css/switcher.css" rel="stylesheet">
    <link href="assets/switcher/demo.css" rel="stylesheet">

    <style>
        .nav-sub-link {
            text-decoration: none;
        }

        .main-sidebar-hide .side-menu .main-logo .icon-logo {
            display: block;
            margin-left: 17px;
            padding-top: 10px
        }

        #search-results-wrapper {
            position: absolute;
            top: 100%;
            /* Position right below the input group */
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(12, 10, 41, 1);
            border: 1px solid #AA62C7 !important;
            border-top: none;
            border-radius: 0 0 5px 5px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            /* width: 257px; */
            /* Hidden by default */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #search-results-wrapper.show {
            display: block;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            /* border-bottom: 1px solid #f0f0f0; */
            transition: background-color 0.2s;
            text-decoration: none;
            color: #AA62C7 !important;
        }

        .search-result-item:hover {
            /* background-color: #f7f7f7; */
        }

        .search-result-item img {
            width: 30px;
            height: 30px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }

        .main-header-left {
            flex-shrink: 0;
            /* Prevents shrinking */
            flex-grow: 0;
            /* Does not grow */
        }

        .main-header-right {
            flex-shrink: 0;
            /* Prevents shrinking */
            flex-grow: 0;
            /* Does not grow */
        }

        /* Allow the center section (containing the search bar) to fill the available space */
        .main-header-center {
            flex-grow: 1;
            /* Allows growth to fill space */
            display: flex;
            justify-content: center;
            /* Centers the content (search bar) horizontally */
            max-width: 50%;
            /* Optional: Limit search bar max width for better aesthetics */
            /* margin: 0 20px; */
            /* Optional: Add horizontal margin */
        }

        /* Remove responsive logo visibility on large screens where search bar is centered */
        @media (min-width: 992px) {

            /* 992px is typically Bootstrap's large breakpoint */
            .main-header-center .responsive-logo {
                display: none !important;
            }
        }
    </style>

</head>

<body class="ltr main-body leftmenu">


    <!-- LOADEAR -->
    <div id="global-loader">
        <img src="https://php.spruko.com/spruha/spruha/assets/img/loader.svg" class="loader-img" alt="Loader">
    </div>
    <!-- END LOADEAR -->

    <!-- PAGE -->
    <div class="page">

        <!-- HEADER -->

        <div class="main-header side-header sticky">
            <div class="main-container container-fluid">
                <div class="main-header-left">
                    <a class="main-header-menu-icon" href="javascript:void(0);" id="mainSidebarToggle"><span></span></a>
                    <div class="hor-logo">
                        <a class="main-logo" href="home.php">
                            <img src="jobhub-logo.svg" class="header-brand-img desktop-logo" alt="logo">
                            <img src="singit_logo_1.png" height="40" class="header-brand-img desktop-logo-dark"
                                alt="logo">
                        </a>
                    </div>
                </div>

                <!-- search  -->

                <div class="main-header-center" style="position: relative;">
                    <div class="responsive-logo">
                        <a href="home.php">
                            <img src="jobhub-logo.svg" class="mobile-logo" alt="logo"></a>
                        <a href="home.php"><img src="singit_logo_1.png" height="40" class="mobile-logo-dark"
                                alt="logo"></a>
                    </div>
                    <div class="input-group">

                        <input type="search" id="header-search-input" class="form-control rounded-0"
                            placeholder="Search for anything... (ctrl + Q)">
                        <!-- <button class="btn search-btn"><i class="fe fe-search"></i></button> -->
                    </div>

                    <!-- START: NEW SEARCH RESULTS WRAPPER -->
                    <div id="search-results-wrapper">
                        <div id="header-status-message" class="p-2 text-center text-muted border-bottom">
                            Start typing to search...
                        </div>
                        <div id="header-results-container" class="p-2">
                            <!-- Search results will be injected here -->
                        </div>
                    </div>
                    <!-- END: NEW SEARCH RESULTS WRAPPER -->
                </div>

                <div class="main-header-right">
                    <button class="navbar-toggler navresponsive-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
                    </button><!-- Navresponsive closed -->
                    <!-- Theme-Layout -->
                    <div class="dropdown d-flex main-header-theme">
                        <a class="nav-link icon layout-setting">
                            <span class="dark-layout">
                                <i class="fe fe-sun header-icons"></i>
                            </span>
                            <span class="light-layout">
                                <i class="fe fe-moon header-icons"></i>
                            </span>
                        </a>
                    </div>
                    <!-- Theme-Layout -->
                    <!-- country -->
                    <!-- <div class="dropdown main-header-notification flag-dropdown">
                                    <a class="nav-link icon country-Flag">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
                                            <g fill="#0052b4">
                                                <path
                                                    d="M52.92 100.142c-20.109 26.163-35.272 56.318-44.101 89.077h133.178L52.92 100.142zM503.181 189.219c-8.829-32.758-23.993-62.913-44.101-89.076l-89.075 89.076h133.176zM8.819 322.784c8.83 32.758 23.993 62.913 44.101 89.075l89.074-89.075H8.819zM411.858 52.921c-26.163-20.109-56.317-35.272-89.076-44.102v133.177l89.076-89.075zM100.142 459.079c26.163 20.109 56.318 35.272 89.076 44.102V370.005l-89.076 89.074zM189.217 8.819c-32.758 8.83-62.913 23.993-89.075 44.101l89.075 89.075V8.819zM322.783 503.181c32.758-8.83 62.913-23.993 89.075-44.101l-89.075-89.075v133.176zM370.005 322.784l89.075 89.076c20.108-26.162 35.272-56.318 44.101-89.076H370.005z" />
                                            </g>
                                            <g fill="#d80027">
                                                <path
                                                    d="M509.833 222.609H289.392V2.167A258.556 258.556 0 00256 0c-11.319 0-22.461.744-33.391 2.167v220.441H2.167A258.556 258.556 0 000 256c0 11.319.744 22.461 2.167 33.391h220.441v220.442a258.35 258.35 0 0066.783 0V289.392h220.442A258.533 258.533 0 00512 256c0-11.317-.744-22.461-2.167-33.391z" />
                                                <path
                                                    d="M322.783 322.784L437.019 437.02a256.636 256.636 0 0015.048-16.435l-97.802-97.802h-31.482v.001zM189.217 322.784h-.002L74.98 437.019a256.636 256.636 0 0016.435 15.048l97.802-97.804v-31.479zM189.217 189.219v-.002L74.981 74.98a256.636 256.636 0 00-15.048 16.435l97.803 97.803h31.481zM322.783 189.219L437.02 74.981a256.328 256.328 0 00-16.435-15.047l-97.802 97.803v31.482z" />
                                            </g>
                                        </svg>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a href="javascript:void(0);" class="dropdown-item d-flex ">
                                            <span class="avatar  me-3 align-self-center bg-transparent"><img
                                                    src="assets/img/flags/french_flag.jpg" alt="img"></span>
                                            <div class="d-flex">
                                                <span class="mt-2">French</span>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item d-flex">
                                            <span class="avatar  me-3 align-self-center bg-transparent"><img
                                                    src="assets/img/flags/germany_flag.jpg" alt="img"></span>
                                            <div class="d-flex">
                                                <span class="mt-2">Germany</span>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item d-flex">
                                            <span class="avatar me-3 align-self-center bg-transparent"><img
                                                    src="assets/img/flags/italy_flag.jpg" alt="img"></span>
                                            <div class="d-flex">
                                                <span class="mt-2">Italy</span>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item d-flex">
                                            <span class="avatar me-3 align-self-center bg-transparent"><img
                                                    src="assets/img/flags/russia_flag.jpg" alt="img"></span>
                                            <div class="d-flex">
                                                <span class="mt-2">Russia</span>
                                            </div>
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item d-flex">
                                            <span class="avatar  me-3 align-self-center bg-transparent"><img
                                                    src="assets/img/flags/spain_flag.jpg" alt="img"></span>
                                            <div class="d-flex">
                                                <span class="mt-2">spain</span>
                                            </div>
                                        </a>
                                    </div>
                                </div> -->
                    <!-- country -->
                    <!-- Full screen -->
                    <div class="dropdown ">
                        <a class="nav-link icon full-screen-link">
                            <i class="fe fe-maximize fullscreen-button fullscreen header-icons"></i>
                            <i class="fe fe-minimize fullscreen-button exit-fullscreen header-icons"></i>
                        </a>
                    </div>
                    <!-- Full screen -->
                    <!-- Notification -->

                    <!-- Notification -->
                    <!-- Messages -->

                    <!-- Messages -->
                    <!-- Profile -->
                    <div class="dropdown main-profile-menu">
                        <a class="d-flex" href="javascript:void(0);">
                            <span class="main-img-user">
                                <img src="<?= htmlspecialchars($admin['photo']) ?>" class="rounded1" height="115"
                                    width="115">
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="header-navheading">
                                <h6 class="main-notification-title">
                                    <?= htmlspecialchars($admin['name']) ?>
                                </h6>
                                <p class="main-notification-text">
                                    <?= htmlspecialchars($admin['email']) ?>
                                </p>
                            </div>
                            <a class="dropdown-item border-top" href="profile.php">
                                <i class="fe fe-user"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fe fe-power"></i> Log Out
                            </a>
                        </div>
                    </div>

                    <!-- Profile -->
                    <!-- Sidebar -->

                    <!-- Sidebar -->
                    <div
                        class="navbar navbar-expand-lg  nav nav-item  navbar-nav-right responsive-navbar navbar-dark  ">
                        <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                            <div class="d-flex order-lg-2 ms-auto">
                                <!-- Search -->
                                <div class="dropdown header-search">
                                    <a class="nav-link icon header-search">
                                        <i class="fe fe-search header-icons"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <div class="main-form-search p-2">
                                            <div class="input-group">
                                                <div class="input-group-btn search-panel">
                                                    <select class="form-control select2">
                                                        <option label="All categories">
                                                        </option>
                                                        <option value="IT Projects">
                                                            IT Projects
                                                        </option>
                                                        <option value="Business Case">
                                                            Business Case
                                                        </option>
                                                        <option value="Microsoft Project">
                                                            Microsoft Project
                                                        </option>
                                                        <option value="Risk Management">
                                                            Risk Management
                                                        </option>
                                                        <option value="Team Building">
                                                            Team Building
                                                        </option>
                                                    </select>
                                                </div>
                                                <input type="search" class="form-control"
                                                    placeholder="Search for anything...">
                                                <button class="btn search-btn"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="feather feather-search">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                    </svg></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Search -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END HEADER -->

        <!-- SIDEBAR -->

        <div class="sticky">
            <div class="main-menu main-sidebar main-sidebar-sticky side-menu">
                <div class="main-sidebar-header main-container-1 active">
                    <div class="sidemenu-logo">
                        <a class="main-logo" href="home.php">
                            <!-- <img src="assets/img/brand/logo-light.png" class="header-brand-img desktop-logo" alt="logo">
                            <img src="assets/img/brand/icon-light.png" class="header-brand-img icon-logo" alt="logo">
                            <img src="assets/img/brand/logo.png" class="header-brand-img desktop-logo theme-logo"
                                alt="logo">
                            <img src="assets/img/brand/icon.png" class="header-brand-img icon-logo theme-logo"
                                alt="logo"> -->
                            <img src="singit_logo_1.png" height="50" class="header-brand-img desktop-logo" alt="logo">
                            <img src="favicon.png" class="header-brand-img icon-logo" alt="logo">
                            <img src="jobhub-logo.svg" class="header-brand-img desktop-logo theme-logo" alt="logo">
                            <img src="favicon.svg" class="header-brand-img icon-logo theme-logo" alt="logo">
                        </a>
                    </div>
                    <div class="main-sidebar-body main-body-1">
                        <div class="slide-left disabled" id="slide-left"><i class="fe fe-chevron-left"></i></div>
                        <ul class="menu-nav nav">
                            <li class="nav-header"><span class="nav-label">Dashboard</span></li>
                            <li class="nav-item">
                                <a class="nav-link" href="home.php
                                ">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-home sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Dashboard</span>
                                </a>
                            </li>

                            <li class="nav-header"><span class="nav-label">Forms</span></li>
                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <i class="uil uil-list-ul sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Genres</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Genres</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="add_genres.php">Add
                                            Genres</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_genres.php">View
                                            Genres</a></li>
                                    <!-- <li class="nav-sub-item"><a class="nav-sub-link" href="blocked_Genres.php">Blocked
                                            Genres</a></li> -->
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <i class="uil uil-language sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Language</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Language</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="add_language.php">Add
                                            Language</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_language.php">View
                                            Language</a></li>
                                    <!-- <li class="nav-sub-item"><a class="nav-sub-link" href="blocked_language.php">Blocked
                                            language</a></li> -->
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <i class="uil uil-user-square sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Artist</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Artist</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="add_artist.php">Add
                                            Artist</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_artist.php">View
                                            Artist</a></li>
                                    <!-- <li class="nav-sub-item"><a class="nav-sub-link" href="blocked_Artist.php">Blocked
                                            Artist</a></li> -->
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <!-- <i class="uil uil-briefcase-alt"></i> -->
                                    <i class="uil uil-music sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Songs</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Songs</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="add_songs.php">Add
                                            Songs</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_songs.php">View
                                            Songs</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <!-- <i class="uil uil-briefcase-alt"></i> -->
                                    <i class="uil uil-image sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Slider</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Slider</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="add_slider.php">Add
                                            Slider</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_slider.php">View
                                            Slider</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <!-- <i class="uil uil-briefcase-alt"></i> -->
                                    <i class="uil uil-calendar-alt sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Today's Special</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Today's Special</a></li>
                                    <!-- <li class="nav-sub-item"><a class="nav-sub-link" href="add_special.php">Add
                                            Today's Special</a></li> -->
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_special.php">View
                                            Today's Special</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <!-- <i class="ti-shopping-cart-full sidemenu-icon menu-icon "></i> -->
                                    <i class="uil uil-user sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Users</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Users</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_users.php">View
                                            Users</a></li>
                                </ul>
                            </li>

                            <!-- <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="uil uil-star sidemenu-icon menu-icon"></i>
                                    <span class="sidemenu-label">Review</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Review</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="package.php">Add
                                            Review</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_package.php">View
                                            Review</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="blocked_package.php">Blocked
                                            Review</a></li>
                                </ul>
                            </li> -->

                            <!-- <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="uil uil-file-alt sidemenu-icon menu-icon "></i>
                                    <span class="sidemenu-label">FAQ_Master</span>
                                    <i class="angle fe fe-chevron-right"></i>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">FAQ_Master</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="faq_master.php">Add FAQ's</a>
                                    </li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="view_faq_master.php">View
                                            FAQ's</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link"
                                            href="blocked_faq_master.php">Blocked
                                            FAQ_Master</a></li>
                                </ul>
                            </li> -->

                            <!-- <li class="nav-item">
                                <a class="nav-link with-sub" href="javascript:void(0)">
                                    <span class="shape1"></span>
                                    <span class="shape2"></span>
                                    <i class="ti-bar-chart-alt sidemenu-icon menu-icon "></i>
                                    <span class="sidemenu-label">Charts</span>
                                    <span class="badge bg-danger side-badge">5</span>
                                </a>
                                <ul class="nav-sub">
                                    <li class="side-menu-label1"><a href="javascript:void(0)">Charts</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="chart-morris.html">Morris
                                            Charts</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="chart-flot.html">Flot
                                            Charts</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link"
                                            href="chart-chartjs.html">ChartJS</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link"
                                            href="chart-spark-peity.html">Sparkline &amp; Peity</a></li>
                                    <li class="nav-sub-item"><a class="nav-sub-link" href="chart-echart.html">Echart</a>
                                    </li>
                                </ul>
                            </li> -->
                        </ul>
                        <div class="slide-right" id="slide-right"><i class="fe fe-chevron-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SIDEBAR -->

        <!-- MAIN-CONTENT -->
        <div class="main-content side-content pt-0">
            <div class="main-container container-fluid">
                <div class="inner-body">
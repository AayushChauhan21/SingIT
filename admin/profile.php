<?php

include('demo.php');
include('hhh.php');
// Assuming connection.php is included in demo.php/hhh.php

// --- Configuration ---
$id = $_SESSION['admin_id'] ?? 1;

// Dynamically build base URL for redirection (for session messages if the API sends them back)
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SingIT/admin/";
// NOTE: I am keeping your API name as 'updateProfile.php' here.
$api_url = "http://localhost/SIngIT/flutter_crud/updateProfile.php"; // <--- 🔔 API TARGET URL

// --- Fetch Admin Data ---
$qry = mysqli_query($con, "SELECT id, name, email, password, photo FROM admin WHERE id=$id");
$row = mysqli_fetch_array($qry);

if (!$row) {
    die("<div class='alert alert-danger'>Admin profile not found.</div>");
}

$name = htmlspecialchars($row['name']);
$email = htmlspecialchars($row["email"]);
$password = htmlspecialchars($row["password"]);
$photo = htmlspecialchars($row['photo']);

// 🚀 CHANGE: Use the 'photo' value directly as the source URL (it's now the Cloudinary URL)
// If photo is empty, you might want a local fallback image path here for consistency.
// We assume if 'photo' is not a URL (e.g., old local file name), it will fall back to a default image.
// For now, we assume if $photo is present, it's a full URL or the necessary value.
$dest = !empty($photo) && (strpos($photo, 'http') === 0 || strpos($photo, '://') !== false)
    ? $photo
    : 'assets/img/default_profile.png'; // Using a reasonable fallback image path

// --- Session Message Check (for SweetAlert display) ---
$session_status = $_SESSION['status'] ?? '';
$session_message = $_SESSION['message'] ?? '';

if (isset($_SESSION['status']))
    unset($_SESSION['status']);
if (isset($_SESSION['message']))
    unset($_SESSION['message']);
?>

<style>
    .setimg {
        display: flex;
    }

    #img {
        padding: 0px;
        max-width: 100%;
        margin-top: 20px;
        border-radius: 50%;
    }

    .drop-container {
        position: relative;
        display: flex;
        gap: 5px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 150px;
        width: 88%;
        padding: 20px;
        border-radius: 10px;
        border: 2px dashed var(--dark-color);
        color: #fff;
        /* Text color for drop container */
        cursor: pointer;
        transition: background .2s ease-in-out, border .2s ease-in-out;
        background-color: #6259ca;
        /* Purple background */
        border-color: #4c449c;
        /* Darker purple border */
    }

    .drop-container:hover {
        background: #7a70d8;
        /* Lighter purple on hover */
        border-color: #5d54b8;
        /* Slightly lighter border on hover */
    }

    .drop-title {
        color: #fff;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        transition: color .2s ease-in-out;
    }

    /* Style for the browse button inside the drop container */
    #custom-browse-button {
        background: black;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        transition: background .2s ease-in-out;
        border: none;
        margin-top: 10px;
    }

    #custom-browse-button:hover {
        background: rgb(69, 68, 68);
    }

    /* Hide the actual file input element */
    input[type=file] {
        display: none;
    }

    .btn-lg {
        padding: 0rem 3rem;
        font-size: 0.875rem;
        border-radius: 10px;
    }

    .input {
        color: black;
    }
</style>

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Profile</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        </ol>
    </div>
</div>
<div class="row square">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="panel profile-cover">
                    <div class="profile-cover__img">
                        <img id="img" src="<?= $dest ?>" height="115" width="115" alt="Admin Photo">

                        <h3 class="h3">
                            <?= $name ?>
                        </h3>
                    </div>

                    <div class="profile-cover__action bg-img"></div>
                    <div class="profile-cover__info">
                        <ul class="nav">
                            <li><strong></strong>&nbsp;</li>
                        </ul>
                        <strong></strong>&nbsp;
                    </div>
                </div>
                <div class="profile-tab tab-menu-heading">
                    <nav class="nav main-nav-line p-3 tabs-menu profile-nav-line bg-gray-100">
                        <a class="nav-link active" data-bs-toggle="tab" href="#view_profile">View Profile</a>
                        <a class="nav-link" data-bs-toggle="tab" href="#edit">Edit Profile</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card main-content-body-profile">
            <div class="tab-content">

                <div class="main-content-body tab-pane p-4 border-top-0 active" id="view_profile">
                    <div class="card-body border">
                        <div class="mb-4 main-content-label">Personal Details</div>
                        <form class="form-horizontal">
                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Name</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input" name="name" placeholder="Name"
                                            value="<?= $name ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Email</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input" name="email"
                                            placeholder="Email Id" value="<?= $email ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Password</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control input" name="password"
                                            placeholder="Password" value="<?= $password ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="main-content-body tab-pane p-4 border-top-0" id="edit">
                    <div class="card-body border">
                        <div class="mb-4 main-content-label">Personal Details</div>
                        <form enctype="multipart/form-data" method="post" action="<?= $api_url ?>"
                            class="form-horizontal" id="adminUpdateForm">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="role" value="admin">
                            <input type="hidden" name="redirect_url" value="<?= $baseUrl ?>profile.php">

                            <div class="mb-4 main-content-label">Name</div>
                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Name</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="name" placeholder="Name"
                                            value="<?= $name ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Email</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" name="email" placeholder="Email Id"
                                            value="<?= $email ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">New Password (6 Chars)</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="password"
                                            placeholder="Leave empty to keep current password" value="" maxlength="6"
                                            size="6">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Image</label>
                                    </div>
                                    <div class="col-md-9">
                                        <label for="photo" class="drop-container" id="dropcontainer">
                                            <span class="drop-title" id="drop-title-text">Drop photo here or click to
                                                select</span>

                                            <button type="button" id="custom-browse-button">Browse...</button>

                                            <input type="file" name="photo" id="photo" accept="image/*">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <br>
                            <button type="submit" name="update" id="update"
                                class="btn btn-outline-success btn-lg input">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('fff.php');
?>

<script>
    // --- SweetAlert Helper Functions (Retained) ---
    const status = "<?= $session_status ?>";
    const message = "<?= addslashes($session_message) ?>";

    function triggerAlert(type, title, text) {
        swal({
            title: title,
            text: text,
            type: type,
            showConfirmButton: true,
            confirmButtonClass: "btn btn-" + (type === 'success' ? 'success' : 'danger')
        });
    }

    $(document).ready(function () {
        // --- 1. SweetAlert Display ---
        if (message && status) {
            let titleText = status === 'success' ? 'Success!' : 'Update Failed';
            triggerAlert(status, titleText, message);
        }

        // --- 2. File Input Custom Display Logic (Simplified) ---
        const fileInput = document.getElementById('photo');
        const customBrowseButton = document.getElementById('custom-browse-button');
        const dropTitleText = document.getElementById('drop-title-text');
        // NOTE: fileNameDisplay variable removed

        // Trigger the hidden file input when the custom browse button is clicked
        customBrowseButton.addEventListener('click', function (event) {
            event.preventDefault();
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                // Update title to indicate a file has been chosen
                dropTitleText.textContent = "File selected: " + this.files[0].name;
            } else {
                // Revert title if file selection is cancelled
                dropTitleText.textContent = "Drop photo here or click to select";
            }
        });

        // Optional: Add drag/drop functionality visual feedback (CSS already handles hover)
        const dropContainer = document.getElementById('dropcontainer');

        dropContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropContainer.style.borderColor = '#a0a0ff'; // Highlight on drag over
        });

        dropContainer.addEventListener('dragleave', () => {
            dropContainer.style.borderColor = '#4c449c'; // Revert on drag leave
        });

        dropContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            dropContainer.style.borderColor = '#4c449c'; // Revert on drop

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files; // Assign dropped files to the input
                const event = new Event('change');
                fileInput.dispatchEvent(event); // Trigger change event to update display
            }
        });
    });
</script>
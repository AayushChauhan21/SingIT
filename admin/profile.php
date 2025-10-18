<?php
include ('demo.php');
include ('hhh.php');


$qry = mysqli_query($con, "select * from admin_master where aid=$aid");
while ($row = mysqli_fetch_array($qry)) {
    $aname = $row['aname'];
    $email = $row["email"];
    $password = $row["password"];
    $phone = $row["phone"];
    $photo = $row['photo'];
}

$dest = "image/" . $photo;
$temp = 0;

// echo "<script>alert('$aname');</script>";

if (isset($_POST['update'])) {
    $aname = $_POST["aname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $phone = $_POST["phone"];
    $photo = $_FILES["photo"]["name"];

    $aname = trim($aname);
    $email = trim($email);
    $password = trim($password);
    $phone = trim($phone);
    $photo = trim($photo);

    if (!$aname) {
        echo "<script>alert('Name Is Required');</script>";
    } elseif (!$email) {
        echo "<script>alert('Email Is Required');</script>";
    } elseif (!$password) {
        echo "<script>alert('Password Is Required');</script>";
    } elseif (strlen($password) != 6) {
        echo "<script>alert('Password Must Be 6 Alphanumeric Character');</script>";
        $tmp = 2;
    } elseif (!$phone) {
        echo "<script>alert('Phone Number Is Required');</script>";
    } else {
        if ($photo) {
            $qry = "update admin_master set aname='$aname',email='$email',password='$password',phone='$phone',photo='$photo' where aid=$aid";
        } else {
            $qry = "update admin_master set aname='$aname',email='$email',password='$password',phone='$phone' where aid=$aid";
        }
        if ($qry) {
            mysqli_query($con, $qry);
            move_uploaded_file($_FILES["photo"]["tmp_name"], $dest);
            echo "<script>alert('Data Successfully Updated');</script>";
            echo "<script>window.location.assign('profile.php')</script>";
        }
    }
}
?>

<style>
    .setimg {
        display: flex;
    }


    #img {
        /* flex-basis: 40% */
        padding: 0px;
        max-width: 100%;
        /* margin-top: 10px; */
        margin-top: 20px;
        border-radius: 50%;

    }

    .tb {
        /* margin-top: 40px; */
        /* font-size: 20px; */
        padding-left: 10px;
        width: 100%;
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
        color: #444;
        cursor: pointer;
        transition: background .2s ease-in-out, border .2s ease-in-out;

    }

    .drop-container:hover {
        background: lightgray;
        border-color: #111;

    }

    .drop-container:hover .drop-title {
        color: #222;
    }

    .drop-title {
        color: var(--dark-color);
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        transition: color .2s ease-in-out;
    }

    input[type=file]::file-selector-button {
        margin-right: 20px;
        border: none;
        background: black;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        transition: background .2s ease-in-out;
    }

    input[type=file]::file-selector-button:hover {
        background: rgb(69, 68, 68);
        /* color: white; */

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

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Profile</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        </ol>
    </div>

</div>
<!-- End Page Header -->

<div class="row square">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="panel profile-cover">
                    <div class="profile-cover__img">
                        <img id="img" src="image/<?php echo $photo; ?>" height="115" width="115">

                        <h3 class="h3">
                            <?php echo $aname; ?>
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
                        <a class="nav-link  active" data-bs-toggle="tab" href="#view_profile">View Profile</a>
                        <a class="nav-link" data-bs-toggle="tab" href="#edit">Edit Profile</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card main-content-body-profile">
            <div class="tab-content">
                <div class="main-content-body tab-pane p-4 border-top-0 active" id="view_profile">
                    <div class="card-body border">
                        <div class="mb-4 main-content-label">Personal Details</div>
                        <form enctype="multipart/form-data" method="post" class="form-horizontal">
                            <div class="mb-4 main-content-label">Name</div>
                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Name</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input" name="aname" placeholder="Name"
                                            value="<?php echo $aname ?>" disabled>
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
                                            placeholder="Email Id" value="<?php echo $email ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Password</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input" name="password"
                                            placeholder="Password" value="<?php echo $password ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile Number</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input" name="phone"
                                            placeholder="Mobile Number" value="<?php echo $phone ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="mb-4 main-content-label">Email Preferences</div>
                            <div class="form-group mb-0">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Verified User</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-controls-stacked">
                                            <label class="ckbox mg-b-10"><input checked="" type="checkbox"><span> Accept
                                                    to receive post or page
                                                    notification emails</span></label>
                                            <label class="ckbox"><input checked="" type="checkbox"><span>
                                                    Accept to receive email sent to multiple
                                                    recipients</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </form>
                    </div>
                </div>

                <div class="main-content-body tab-pane p-4 border-top-0" id="edit">
                    <div class="card-body border">
                        <div class="mb-4 main-content-label">Personal Details</div>
                        <form enctype="multipart/form-data" method="post" class="form-horizontal">
                            <div class="mb-4 main-content-label">Name</div>
                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Name</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="aname" placeholder="Name"
                                            value="<?php echo $aname ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Email</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="email" placeholder="Email Id"
                                            value="<?php echo $email ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Password</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="password" placeholder="Password"
                                            value="<?php echo $password ?>" maxlength="6" size="6">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile Number</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="phone" placeholder="Mobile Number"
                                            value="<?php echo $phone ?>">
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
                                            <span class="drop-title">Drop photo here</span>

                                            <input type="file" name="photo" id="photo">
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <!-- <div class="mb-4 main-content-label">Email Preferences</div>
                            <div class="form-group mb-0">
                                <div class="row row-sm">
                                    <div class="col-md-3">
                                        <label class="form-label">Verified User</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-controls-stacked">
                                            <label class="ckbox mg-b-10"><input checked="" type="checkbox"><span> Accept
                                                    to receive post or page
                                                    notification emails</span></label>
                                            <label class="ckbox"><input checked="" type="checkbox"><span>
                                                    Accept to receive email sent to multiple
                                                    recipients</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

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
<!-- End Row -->


</div>
</div>
</div>
<!-- END MAIN-CONTENT -->

<?php
include ('fff.php');
?>
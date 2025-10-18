<?php
include('demo.php');
include('hhh.php');
include('connection.php');
error_reporting(1);

if (isset($_POST["insert"])) {
    // Insert , Registration , Sign Up Code
    $mid = rand(time(), 100000000);
    $receiver = $_POST["receiver"];
    $sub = $_POST["sub"];
    $msg = $_POST["msg"];

    // $date = date('Y-m-d');
    $date = date('Y-m-d H:i:s');

    $receiver = trim($receiver);
    $sub = trim($sub);
    $msg = trim($msg);
    if (!$receiver && !$sub && !$msg) {
        echo "<script>alert('Receiver, Subject & Message Are Required');</script>";
    } elseif (!$receiver && !$sub) {
        echo "<script>alert('Receiver & Subject Are Required');</script>";
    } elseif (!$sub && !$msg) {
        echo "<script>alert('Subject & Message Are Required');</script>";
    } elseif (!$receiver && !$msg) {
        echo "<script>alert('Receiver & Message Are Required');</script>";
    } elseif (!$receiver) {
        echo "<script>alert('Receiver Is Required');</script>";
    } elseif (!$sub) {
        echo "<script>alert('Subject Is Required');</script>";
    } elseif (!$msg) {
        echo "<script>alert('Message Is Required');</script>";
    } else {
        $qry = "INSERT INTO mail VALUES($mid,'d.j.sariya5@gmail.com','$receiver','$sub','$msg','$date')";
        if (mysqli_query($con, $qry)) {
            echo "<script>alert('Successfully Mail Sent');</script>";
            echo "<script>window.location.href='mail_sending.php?mid=$mid';</script>";
        }
    }
}

if (isset($_POST["cancel"])) {
    echo "<script>window.location.href='mail.php';</script>";
}

?>




<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Mail Compose</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Apps</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mail Compose</li>
        </ol>
    </div>

</div>
<!-- End Page Header -->

<!-- Row -->
<div class="row row-sm">

    <div class="col-lg-12 col-md-12">
        <form class="card custom-card" method="post" enctype="multipart/form-data">
            <div class="card-header">
                <h3 class="card-title tx-18">
                    <label class="main-content-label tx-15">Compose New Message</label>
                </h3>
            </div>
            <div class="card-body">
                <div>
                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-3 col-xl-2 form-label tx-semibold">To</label>
                            <div class="col-sm-9 col-xl-10">
                                <input type="text" class="form-control" id="receiver" name="receiver">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-3 col-xl-2 form-label tx-semibold">CC</label>
                            <div class="col-sm-9 col-xl-10">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-3 col-xl-2 form-label tx-semibold">BC</label>
                            <div class="col-sm-9 col-xl-10">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div> -->
                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-3 col-xl-2 form-label tx-semibold">Subject</label>
                            <div class="col-sm-9 col-xl-10">
                                <input type="text" class="form-control" id="sub" name="sub">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="col-sm-3 col-xl-2 form-label tx-semibold">Message</label>
                            <div class="col-sm-9 col-xl-10">
                                <textarea rows="10" class="form-control" id="msg" name="msg"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-sm-flex rounded-0">
                <!-- <div class="">
                    <a href="javascript:void(0);" class="btn px-2" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="Attach">
                        <i class="fe fe-paperclip fs-16"></i>
                    </a>
                    <a href="javascript:void(0);" class="btn px-2" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="Link">
                        <i class="fe fe-link fs-16"></i>
                    </a>
                    <a href="javascript:void(0);" class="btn px-2" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="Photos">
                        <i class="fe fe-image fs-16"></i>
                    </a>
                    <a href="javascript:void(0);" class="btn px-2" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="Delete">
                        <i class="fe fe-trash fs-16"></i>
                    </a>
                </div> -->
                <div class="btn-list ms-auto">
                    <button type="submit" name="cancel" id="cancel" class="btn btn-danger">Cancel</button>
                    <input type="submit" value="Insert" name="insert" id="insert" class="btn btn-primary">Send
                    message</input>
                    <!-- <input type="submit" value="Insert" name="insert" id="insert"
                        class="btn btn-outline-primary btn-lg input">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                </div>
            </div>
        </form>
    </div>
    <!-- End Row -->


</div>
</div>
</div>
<!-- END MAIN-CONTENT -->

<!-- RIGHT-SIDEBAR -->

<div class="sidebar sidebar-right sidebar-animate">
    <div class="sidebar-icon">
        <a href="#" class="text-end float-end text-dark fs-20" data-bs-toggle="sidebar-right"
            data-bs-target=".sidebar-right"><i class="fe fe-x"></i></a>
    </div>
    <div class="sidebar-body">
        <h5>Todo</h5>
        <div class="d-flex p-3">
            <label class="ckbox"><input checked type="checkbox"><span>Hangout With friends</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top">
            <label class="ckbox"><input type="checkbox"><span>Prepare for presentation</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top">
            <label class="ckbox"><input type="checkbox"><span>Prepare for presentation</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top">
            <label class="ckbox"><input checked type="checkbox"><span>System Updated</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top">
            <label class="ckbox"><input type="checkbox"><span>Do something more</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top">
            <label class="ckbox"><input type="checkbox"><span>System Updated</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top">
            <label class="ckbox"><input type="checkbox"><span>Find an Idea</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <div class="d-flex p-3 border-top mb-0">
            <label class="ckbox"><input type="checkbox"><span>Project review</span></label>
            <span class="ms-auto">
                <i class="fe fe-edit-2 text-primary me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Edit"></i>
                <i class="fe fe-trash-2 text-danger me-2" data-bs-toggle="tooltip" title="" data-bs-placement="top"
                    data-bs-original-title="Delete"></i>
            </span>
        </div>
        <h5>Overview</h5>
        <div class="p-4">
            <div class="main-traffic-detail-item">
                <div>
                    <span>Founder &amp; CEO</span> <span>24</span>
                </div>
                <div class="progress">
                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="20"
                        class="progress-bar progress-bar-xs wd-20p" role="progressbar"></div>
                </div><!-- progress -->
            </div>
            <div class="main-traffic-detail-item">
                <div>
                    <span>UX Designer</span> <span>1</span>
                </div>
                <div class="progress">
                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="15"
                        class="progress-bar progress-bar-xs bg-secondary wd-15p" role="progressbar"></div>
                </div><!-- progress -->
            </div>
            <div class="main-traffic-detail-item">
                <div>
                    <span>Recruitment</span> <span>87</span>
                </div>
                <div class="progress">
                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="45"
                        class="progress-bar progress-bar-xs bg-success wd-45p" role="progressbar"></div>
                </div><!-- progress -->
            </div>
            <div class="main-traffic-detail-item">
                <div>
                    <span>Software Engineer</span> <span>32</span>
                </div>
                <div class="progress">
                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="25"
                        class="progress-bar progress-bar-xs bg-info wd-25p" role="progressbar"></div>
                </div><!-- progress -->
            </div>
            <div class="main-traffic-detail-item">
                <div>
                    <span>Project Manager</span> <span>32</span>
                </div>
                <div class="progress">
                    <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="25"
                        class="progress-bar progress-bar-xs bg-danger wd-25p" role="progressbar"></div>
                </div><!-- progress -->
            </div>
        </div>
    </div>
</div>
<!-- END RIGHT-SIDEBAR -->

<?php
include('fff.php');
?>
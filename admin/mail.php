<?php
include('demo.php');
include('hhh.php');
include('con.php');
error_reporting(1);

// Single email deletion
if (isset($_POST["delete"])) {
    $mail_id = $_POST["mail_id"];
    $qry = "DELETE FROM mail WHERE mid = $mail_id";
    if (mysqli_query($con, $qry)) {
        echo "<script>alert('Mail Deleted Successfully');</script>";
    } else {
        echo "<script>alert('Error Deleting Mail');</script>";
    }
    echo "<script>window.location.href='mail.php';</script>"; // Replace 'mail.php' with the actual name of your page
}

// Multiple email deletion
if (isset($_POST["delete_selected"])) {
    if (!empty($_POST['mail_ids'])) {
        $mail_ids = implode(',', $_POST['mail_ids']);
        $qry = "DELETE FROM mail WHERE mid IN ($mail_ids)";
        if (mysqli_query($con, $qry)) {
            echo "<script>alert('Selected Mails Deleted Successfully');</script>";
        } else {
            echo "<script>alert('Error Deleting Selected Mails');</script>";
        }
    } else {
        echo "<script>alert('No Mails Selected');</script>";
    }
    echo "<script>window.location.href='mail.php';</script>"; // Replace 'mail.php' with the actual name of your page
}
?>


<!-- Page Header -->
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Mail Inbox</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Apps</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mail Inbox</li>
        </ol>
    </div>
    <div class="d-flex">

    </div>
</div>
<!-- End Page Header -->

<!-- row -->
<div class="row row-sm">

    <div class="col-xl-12 col-lg-8  main-content-body-mail1">
        <div class="card custom-card mail-container task-container overflow-hidden">
            <div class="inbox-body p-4">
                <!-- <div class="mail-option">
                    <div class="chk-all border-0">
                        <div class="btn-group">
                            <a data-bs-toggle="dropdown" href="javascript:void(0);" class="btn mini all"
                                aria-expanded="false">
                                All
                                <i class="fe fe-chevron-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0);"> None</a></li>
                                <li><a href="javascript:void(0);"> Read</a></li>
                                <li><a href="javascript:void(0);"> Unread</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="btn-group">
                        <a href="javascript:void(0);" class="btn mini tooltips">
                            <i class="fe fe-refresh-cw"></i>
                        </a>
                    </div>
                    <div class="btn-group hidden-phone">
                        <a data-bs-toggle="dropdown" href="javascript:void(0);" class="btn mini blue"
                            aria-expanded="false">
                            More
                            <i class="fe fe-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0);"><i class="fe fe-edit me-2"></i> Mark
                                    as Read</a></li>
                            <li><a href="javascript:void(0);"><i class="fe fe fe-slash me-2"></i>
                                    Spam</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0);"><i class="fe fe-trash me-2"></i>
                                    Delete</a></li>
                        </ul>
                    </div>
                    <ul class="unstyled inbox-pagination">
                        <li><span>1-50 of 234</span></li>

                        <li>
                            <a class="btn np-btn" href="javascript:void(0);"><i
                                    class="fe fe-chevron-right pagination-right"></i></a>
                        </li>
                    </ul>
                </div> -->
                <div class="table-responsive">
                    <form method="post" action="">
                        <div class="d-flex justify-content-between mb-2">
                            <button type="submit" name="delete_selected" class="btn btn-danger">
                                <b><i class="uil uil-envelope-minus tx-16"></i> Delete Selected</b>
                            </button>
                            <a href="mail-compose.php" class="btn btn-main-primary btn-compose">

                                <b><i class="uil uil-envelope-edit tx-16"></i> Compose</b>

                            </a>

                        </div>
                        <table class="table table-inbox text-md-nowrap table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th class="inbox-small-cells text-center">
                                        <label class="custom-control custom-checkbox mb-0">
                                            <input type="checkbox" class="custom-control-input" id="select_all">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </th>
                                    <th class="text-center">Receiver</th>
                                    <th class="text-center">Subject and Message</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qry = mysqli_query($con, "select * from mail");
                                if (mysqli_num_rows($qry) > 0) {
                                    while ($row = mysqli_fetch_array($qry)) {
                                        // Format the date
                                        $formatted_date = date('d-m-Y H:i:s', strtotime($row['date']));
                                        ?>
                                <tr class="">
                                    <td class="inbox-small-cells text-center">
                                        <label class="custom-control custom-checkbox mb-0">
                                            <input type="checkbox" class="custom-control-input select_row"
                                                name="mail_ids[]" value="<?php echo $row['mid']; ?>">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </td>
                                    <td class="view-message dont-show font-weight-semibold text-center">
                                        <a
                                            href="view_mail.php?mid=<?php echo $row['mid']; ?>"><?php echo $row['receiver']; ?></a>
                                    </td>
                                    <td class="view-message text-center"><b><?php echo $row['sub']; ?></b> -
                                        <?php echo $row['msg']; ?>
                                    </td>
                                    <td class="view-message text-end font-weight-semibold text-center">
                                        <?php echo $formatted_date; ?>
                                    </td>
                                    <td class="view-message text-end text-center">
                                        <form method="post" action="">
                                            <input type="hidden" name="mail_id" value="<?php echo $row['mid']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger">
                                                <b class="tx-20"><i class="uil uil-envelope-minus"></i></b>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No mails to display</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </form>
                </div>

            </div>
            <!-- mail-content -->
        </div>
    </div>
</div>
<!-- /row -->


<?php
include('fff.php');
?>

<!-- JavaScript for Select All functionality -->
<script>
document.getElementById('select_all').onclick = function() {
    var checkboxes = document.querySelectorAll('.select_row');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
}
</script>
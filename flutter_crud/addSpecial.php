<?php
// FILE: updatespecial.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection file include karein
include("connection.php");

// 1. Base URL dynamically build karein
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SingIT/admin/";


if (isset($_POST["update"])) {

    $sid_list = $_POST["sid_list"] ?? '';
    $success_count = 0;
    $sids_to_insert = [];

    // 2. Initial Validation 
    if (empty($sid_list)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Update failed. Please select at least one song for the special.';
        header("location:" . $baseUrl . "edit_special.php");
        exit;
    }

    $sids = explode(',', $sid_list);

    // 3. Prepare unique and safe IDs for insertion
    foreach ($sids as $sid) {
        $sid = trim($sid);
        if (!empty($sid) && is_numeric($sid)) {
            $safe_sid = mysqli_real_escape_string($con, $sid);
            if (!in_array($safe_sid, $sids_to_insert)) {
                $sids_to_insert[] = $safe_sid;
            }
        }
    }

    if (empty($sids_to_insert)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'No valid songs selected for update.';
        header("location:" . $baseUrl . "edit_special.php");
        exit;
    }

    // 4. 💣 Clear Existing special Data (Transactional Update)
    try {

        $delete_query = "DELETE FROM special";
        if (!mysqli_query($con, $delete_query)) {
            throw new Exception("Failed to clear existing special data: " . mysqli_error($con));
        }

        // 5. 🟢 Insert New Data
        foreach ($sids_to_insert as $safe_sid) {
            // Note: Assuming the special table only requires 'sid' and has auto-increment 'id'
            $insert_query = "INSERT INTO special (sid) VALUES ('$safe_sid')";

            if (mysqli_query($con, $insert_query)) {
                $success_count++;
            }
        }

        // 6. Success Response and Redirection
        if ($success_count > 0) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Special songs successfully updated with ' . $success_count . ' song(s)!';
        } else {
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] = 'Special list cleared. No new songs were inserted.';
        }

        header("location:" . $baseUrl . "view_special.php");

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database update error: ' . $e->getMessage();
        header("location:" . $baseUrl . "edit_special.php");
    }

    mysqli_close($con);
    exit;

} else {
    // Direct access handling
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = '❌ Invalid form submission method.';
    header("location:" . $baseUrl . "view_special.php");
    exit;
}
?>
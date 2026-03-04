<?php
// addSlider.php

// Session start karein
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection file include karein
include("connection.php");

// 1. Base URL dynamically build karein
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseUrl .= "://" . $_SERVER['HTTP_HOST'] . "/SingIT/admin/";

if (isset($_POST["insert"])) {

    $sid_list = $_POST["sid_list"] ?? '';
    $success_count = 0;
    $sids_to_insert = [];

    // 2. Prepare unique and safe IDs for insertion
    $sids = explode(',', $sid_list);
    foreach ($sids as $sid) {
        $sid = trim($sid);
        if (!empty($sid) && is_numeric($sid)) {
            $safe_sid = mysqli_real_escape_string($con, $sid);
            if (!in_array($safe_sid, $sids_to_insert)) {
                $sids_to_insert[] = $safe_sid;
            }
        }
    }

    // 3. START DATABASE OPERATIONS
    try {
        // Step A: TRUNCATE existing slider table (Clear everything)
        // This resets the table so only current selections remain
        mysqli_query($con, "TRUNCATE TABLE slider");

        // Step B: Insert the new selections
        if (!empty($sids_to_insert)) {
            foreach ($sids_to_insert as $safe_sid) {
                $insert_query = "INSERT INTO slider (sid) VALUES ('$safe_sid')";
                if (mysqli_query($con, $insert_query)) {
                    $success_count++;
                }
            }
        }

        // 4. Success/Failure Response
        if ($success_count > 0 || empty($sids_to_insert)) {
            // Note: Even if 0 songs are selected, it's a success (slider cleared)
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Slider updated successfully!';
            header("location:" . $baseUrl . "view_slider.php");
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Database update failed.';
            header("location:" . $_SERVER['HTTP_REFERER']);
        }

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = $e->getMessage();
        header("location:" . $baseUrl . "view_slider.php"); 
    }

    mysqli_close($con);
    exit;

} else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = '❌ Invalid form submission method.';
    header("location:" . $baseUrl . "add_slider.php");
    exit;
}
?>
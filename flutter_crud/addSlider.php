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

// Cloudinary initialization ki zaroorat nahi hai.

if (isset($_POST["insert"])) {

    $sid_list = $_POST["sid_list"] ?? '';
    $success_count = 0;
    $sids_to_insert = [];

    // 2. Initial Validation (Though JavaScript handles it, server-side is necessary)
    if (empty($sid_list)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database insert failed. Please select at least one song.';
        header("location:" . $_SERVER['HTTP_REFERER']); // Go back to the form page
        exit;
    }

    $sids = explode(',', $sid_list);

    // 3. Prepare unique and safe IDs for insertion
    foreach ($sids as $sid) {
        $sid = trim($sid);
        if (!empty($sid) && is_numeric($sid)) {
            $safe_sid = mysqli_real_escape_string($con, $sid);
            // Check if ID is already processed in this batch to prevent redundant work
            if (!in_array($safe_sid, $sids_to_insert)) {
                $sids_to_insert[] = $safe_sid;
            }
        }
    }

    if (empty($sids_to_insert)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Database insert failed. Please try again.';
        header("location:" . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // 4. Loop aur Insertion (Only insert new SIDs)
    try {
        foreach ($sids_to_insert as $safe_sid) {

            // Duplicate check: Check if this Song ID (sid) is already in the slider table
            $check_query = "SELECT sid FROM slider WHERE sid = '$safe_sid'";
            $check_result = mysqli_query($con, $check_query);

            if (mysqli_num_rows($check_result) == 0) {
                // Insert only if it doesn't exist
                $insert_query = "INSERT INTO slider (sid) VALUES ('$safe_sid')";

                if (mysqli_query($con, $insert_query)) {
                    $success_count++;
                }
            }
        }

        // 5. Success/Failure Response aur Redirection (Same messaging format)
        if ($success_count > 0) {
            // SUCCESS message
            $_SESSION['status'] = 'success';
            // Note: Message adjusted slightly to match the context
            $_SESSION['message'] = 'Song(s) successfully inserted into the slider!';
            header("location:" . $baseUrl . "view_slider.php"); // Redirect to the slider view page
        } else {
            // FAILURE/NO CHANGE message (e.g., all selected songs were already in the slider)
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] = 'Database insert failed. Please try again.'; // Generic error message
            header("location:" . $baseUrl . "view_slider.php");
        }

    } catch (Exception $e) {
        // Handle database or unexpected errors
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = $e->getMessage();
        header("location:" . $baseUrl . "view_genres.php"); // Use a general error redirect page
    }

    mysqli_close($con);
    exit;

} else {
    // Direct access handling
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = '❌ Invalid form submission method.';
    header("location:" . $baseUrl . "add_slider.php");
    exit;
}
?>
<?php
// FILE: updateProfile.php (Handles User JSON Response OR Admin Redirect)

require "connection.php";

// Session start is mandatory for setting messages and redirecting
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

// 1. Cloudinary Configuration 
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

$response = array("success" => false, "error" => "");

// --- Configuration ---
// Removed: $photo_upload_dir as local upload is replaced by Cloudinary
$admin_redirect_url = null; // Will store the URL to redirect the admin back to.

// 2. Check if minimum required POST data was sent
if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['role'])) {

    // --- Get Common Fields ---
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = strtolower($_POST['role']); // 'user' or 'admin'

    // Get redirect URL for Admin panel
    if ($role === 'admin' && isset($_POST['redirect_url'])) {
        $admin_redirect_url = $_POST['redirect_url'];
    }

    // Sanitize common fields
    $id_safe = mysqli_real_escape_string($con, $id);
    $name_safe = mysqli_real_escape_string($con, $name);
    $email_safe = mysqli_real_escape_string($con, $email);

    // Determine the target table and ID column name
    if ($role === 'user') {
        $table = 'user';
        $id_column = 'id';
    } elseif ($role === 'admin') {
        $table = 'admin';
        $id_column = 'id';
    } else {
        $response["error"] = "Invalid role specified.";
        goto finish;
    }

    // 3. Start building the SQL SET clause
    $set_clause = "name = '$name_safe', email = '$email_safe'";

    // --- Conditional Password Update ---
    if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
        $newPassword = trim($_POST['password']);

        // Admin length check (for security consistency)
        if ($role === 'admin' && strlen($newPassword) !== 6) {
            $response["error"] = "Admin password must be 6 characters long.";
            goto finish;
        }

        $password_safe = mysqli_real_escape_string($con, $newPassword);
        $set_clause .= ", password = '$password_safe'";
    }

    // --- Conditional Photo Update (Only for Admin) ---
    $photo_success = true;
    if ($role === 'admin' && isset($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
        $photo_file = $_FILES['photo'];
        $photo_tmp = $photo_file['tmp_name'];

        // 🚀 Attempt Cloudinary upload instead of local move
        try {
            $uploadResult = $cloudinary->uploadApi()->upload($photo_tmp, [
                // Optional: set a specific folder for admin profiles
                'folder' => 'admin_profiles'
            ]);
            $photoUrl = $uploadResult['secure_url']; // Get the URL

            $photoUrl_safe = mysqli_real_escape_string($con, $photoUrl);

            // Update the SET clause to use the Cloudinary URL
            $set_clause .= ", photo = '$photoUrl_safe'";

        } catch (Exception $e) {
            $response["error"] = "Failed to upload photo to Cloudinary: " . $e->getMessage();
            $photo_success = false;

            // Handle Admin redirect on upload failure
            if ($admin_redirect_url) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = '❌ Image upload failed: ' . $e->getMessage();
                $con->close();
                header("Location: " . $admin_redirect_url);
                exit;
            }
        }
    }

    if (!$photo_success) {
        goto finish; // Stop if photo upload failed (only relevant if it wasn't already redirected)
    }

    // 4. Final SQL query
    $sql = "UPDATE $table SET $set_clause WHERE $id_column = $id_safe";

    // 5. Run the query and check if it worked
    if ($con->query($sql) === TRUE) {
        // Success!
        $response["success"] = true;

        // --- ADMIN REDIRECT LOGIC (Success) ---
        if ($role === 'admin' && $admin_redirect_url) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Profile successfully updated!';
            $con->close();
            header("Location: " . $admin_redirect_url);
            exit;
        }

    } else {
        // Query failed
        if ($con->errno == 1062) {
            $error_msg = "This email address is already in use.";
        } else {
            $error_msg = "Database error: " . $con->error;
        }

        $response["error"] = $error_msg;

        // --- ADMIN REDIRECT LOGIC (Failure) ---
        if ($role === 'admin' && $admin_redirect_url) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $error_msg;
            $con->close();
            header("Location: " . $admin_redirect_url);
            exit;
        }
    }

} else {
    // Missing required fields
    $response["error"] = "Required fields (id, name, email, role) are missing.";
}

finish:
// 6. Close the connection and send the JSON response (only if not redirected)
$con->close();
echo json_encode($response);
?>
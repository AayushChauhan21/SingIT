<?php
// FILE: updateProfile.php (Handles User JSON Response OR Admin Redirect)

require "connection.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// 1. Cloudinary Configuration 
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

$response = array("success" => false, "error" => "");

// 🔧 Utility Function: Cloudinary URL માંથી Public ID કાઢવા માટે (Delete કરવા માટે)
function getPublicIdFromUrl($url)
{
    if (empty($url))
        return null;

    $path = parse_url($url, PHP_URL_PATH);
    $uploadPos = strpos($path, '/upload/');
    if ($uploadPos === false)
        return null;

    $relativePath = substr($path, $uploadPos + 8);

    // વર્ઝન ફોલ્ડર દૂર કરો (v12345678)
    $relativePath = preg_replace('/^v\d+\//', '', $relativePath);

    // એક્સ્ટેન્શન (.jpg, .png) દૂર કરો
    $publicId = pathinfo($relativePath, PATHINFO_DIRNAME) . '/' . pathinfo($relativePath, PATHINFO_FILENAME);

    return ltrim($publicId, './');
}

$admin_redirect_url = null;

// 2. Check if minimum required POST data was sent
if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['role'])) {

    // --- Get Common Fields ---
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = strtolower($_POST['role']); // 'user' or 'admin'

    if ($role === 'admin' && isset($_POST['redirect_url'])) {
        $admin_redirect_url = $_POST['redirect_url'];
    }

    $id_safe = mysqli_real_escape_string($con, $id);
    $name_safe = mysqli_real_escape_string($con, $name);
    $email_safe = mysqli_real_escape_string($con, $email);

    // Determine target table and ID column name
    if ($role === 'user') {
        $table = 'user';
        $id_column = 'id';
        $folderPath = 'User/Profile-Pic'; // 📁 User માટેનું ફોલ્ડર
    } elseif ($role === 'admin') {
        $table = 'admin';
        $id_column = 'id';
        // $folderPath = 'Admin/Profile-Pic'; // 📁 Admin માટેનું ફોલ્ડર (અથવા User/Profile-Pic પણ રાખી શકો)
        $folderPath = 'User/Profile-Pic';
    } else {
        $response["error"] = "Invalid role specified.";
        goto finish;
    }

    // 3. Start building the SQL SET clause
    $set_clause = "name = '$name_safe', email = '$email_safe'";

    // --- Conditional Password Update ---
    if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
        $newPassword = trim($_POST['password']);

        if ($role === 'admin' && strlen($newPassword) !== 6) {
            $response["error"] = "Admin password must be 6 characters long.";
            goto finish;
        }

        $password_safe = mysqli_real_escape_string($con, $newPassword);
        $set_clause .= ", password = '$password_safe'";
    }

    // --- Conditional Photo Update (User અને Admin બંને માટે) ---
    $photo_success = true;
    if (isset($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
        $photo_file = $_FILES['photo'];
        $photo_tmp = $photo_file['tmp_name'];

        try {
            // 🗑️ ૧. પહેલા ડેટાબેઝમાંથી જૂનો પ્રોફાઈલ ફોટો શોધીને Cloudinary માંથી ડિલીટ કરો
            $oldDataQuery = mysqli_query($con, "SELECT photo FROM $table WHERE $id_column = '$id_safe'");
            if ($oldDataQuery && mysqli_num_rows($oldDataQuery) > 0) {
                $oldRow = mysqli_fetch_assoc($oldDataQuery);
                $oldPhotoUrl = $oldRow['photo'] ?? null;

                if (!empty($oldPhotoUrl)) {
                    $oldPublicId = getPublicIdFromUrl($oldPhotoUrl);
                    if ($oldPublicId) {
                        try {
                            $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                        } catch (Exception $e) {
                            // જૂનો ફોટો ડીલીટ થવામાં એરર આવે તો ઈગ્નોર કરો
                        }
                    }
                }
            }

            // 📁 ૨. નવો પ્રોફાઈલ ફોટો 'User/Profile-Pic' (અથવા Admin) ફોલ્ડરમાં અપલોડ કરો
            $uploadResult = $cloudinary->uploadApi()->upload($photo_tmp, [
                'folder' => $folderPath
            ]);
            $photoUrl = $uploadResult['secure_url'];

            $photoUrl_safe = mysqli_real_escape_string($con, $photoUrl);

            // Update the SET clause
            $set_clause .= ", photo = '$photoUrl_safe'";

        } catch (Exception $e) {
            $response["error"] = "Failed to upload photo to Cloudinary: " . $e->getMessage();
            $photo_success = false;

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
        goto finish;
    }

    // 4. Final SQL query
    $sql = "UPDATE $table SET $set_clause WHERE $id_column = '$id_safe'";

    // 5. Run the query
    if ($con->query($sql) === TRUE) {
        $response["success"] = true;

        if ($role === 'admin' && $admin_redirect_url) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Profile successfully updated!';
            $con->close();
            header("Location: " . $admin_redirect_url);
            exit;
        }

    } else {
        if ($con->errno == 1062) {
            $error_msg = "This email address is already in use.";
        } else {
            $error_msg = "Database error: " . $con->error;
        }

        $response["error"] = $error_msg;

        if ($role === 'admin' && $admin_redirect_url) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $error_msg;
            $con->close();
            header("Location: " . $admin_redirect_url);
            exit;
        }
    }

} else {
    $response["error"] = "Required fields (id, name, email, role) are missing.";
}

finish:
$con->close();
echo json_encode($response);
?>
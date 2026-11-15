<?php
require "connection.php";
header('Content-Type: application/json');

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

// 2. Check if UID and Image File are provided from Flutter
if (isset($_POST['uid']) && isset($_FILES['photo']) && !empty($_FILES['photo']['name'])) {

    $uid = $_POST['uid'];
    $photo_file = $_FILES['photo'];
    $photo_tmp = $photo_file['tmp_name'];

    $uid_safe = mysqli_real_escape_string($con, $uid);

    try {
        // 🗑️ ૧. પહેલા ડેટાબેઝમાંથી જૂનો પ્રોફાઈલ ફોટો શોધીને Cloudinary માંથી ડિલીટ કરો
        $oldDataQuery = mysqli_query($con, "SELECT photo FROM user WHERE id = '$uid_safe'");
        if ($oldDataQuery && mysqli_num_rows($oldDataQuery) > 0) {
            $oldRow = mysqli_fetch_assoc($oldDataQuery);
            $oldPhotoUrl = $oldRow['photo'] ?? null;

            if (!empty($oldPhotoUrl)) {
                $oldPublicId = getPublicIdFromUrl($oldPhotoUrl);
                if ($oldPublicId) {
                    try {
                        $cloudinary->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                    } catch (Exception $e) {
                        // જૂનો ફોટો ડીલીટ થવામાં એરર આવે તો ઈગ્નોર કરો (જેથી પ્રોસેસ અટકે નહિ)
                    }
                }
            }
        }

        // 📁 ૨. નવો પ્રોફાઈલ ફોટો 'User/Profile-Pic' ફોલ્ડરમાં અપલોડ કરો
        $uploadResult = $cloudinary->uploadApi()->upload($photo_tmp, [
            'folder' => 'User/Profile-Pic'
        ]);

        $new_photo_url = $uploadResult['secure_url'];
        $photoUrl_safe = mysqli_real_escape_string($con, $new_photo_url);

        // 3. Build the SQL query string
        $sql = "UPDATE user SET photo = '$photoUrl_safe' WHERE id = '$uid_safe'";

        // 4. Execute the query
        if ($con->query($sql) === TRUE) {
            if ($con->affected_rows > 0 || $con->info) {
                // નોંધ: જો એ જ સેમ ફોટો અપલોડ થાય તો affected_rows 0 આવે, પણ ક્વેરી સક્સેસ હોય.
                $response["success"] = true;
                $response["new_photo_url"] = $new_photo_url; // ફ્લટરમાં પાછો નવો URL મોકલવા માટે
            } else {
                $response["error"] = "User not found.";
            }
        } else {
            $response["error"] = "Database update failed: " . $con->error;
        }

    } catch (Exception $e) {
        $response["error"] = "Failed to upload photo to Cloudinary: " . $e->getMessage();
    }

} else {
    // uid or photo file was not sent
    $response["error"] = "Required fields are missing (uid or photo file). Make sure Flutter sends MultipartRequest.";
}

// 6. Close the connection and send the response
$con->close();
echo json_encode($response);
?>
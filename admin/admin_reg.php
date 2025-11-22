<?php
require 'vendor/autoload.php'; // Cloudinary SDK

use Cloudinary\Cloudinary;

// Cloudinary config
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

// Database connection
$conn = new mysqli("localhost", "root", "", "singit");

$message = "";
$imageUrl = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 🔐 Encrypt password
    $file = $_FILES['photo']['tmp_name'];

    try {
        // 📤 Upload image to Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload($file);
        $imageUrl = $uploadResult['secure_url'];

        // 🧠 Insert into admin table
        $stmt = $conn->prepare("INSERT INTO user (name, email, password, photo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $imageUrl);
        $stmt->execute();

        $message = "✅ Admin registered successfully!";
    } catch (Exception $e) {
        $message = "❌ Upload failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 300px;
            padding: 8px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
        }

        .feedback {
            margin-top: 20px;
            font-weight: bold;
        }

        img {
            margin-top: 10px;
            max-width: 300px;
        }
    </style>
</head>

<body>
    <h2>🛡️ Admin Registration</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="file" name="photo" accept="image/*" required><br>
        <input type="submit" value="Register Admin">
    </form>

    <?php if (!empty($message)): ?>
        <div class="feedback"><?= $message ?></div>
        <?php if (!empty($imageUrl)): ?>
            <p>🌐 Image URL: <a href="<?= $imageUrl ?>" target="_blank"><?= $imageUrl ?></a></p>
            <img src="<?= $imageUrl ?>" alt="Uploaded Image">
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>
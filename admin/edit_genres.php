<?php
include('demo.php');
include('hhh.php');
include("connection.php");
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

$gid = $_GET['gid'];
$qry = mysqli_query($con, "SELECT * FROM genre WHERE gid=$gid");
$row = mysqli_fetch_array($qry);
$name = $row['name'];
$image = $row['image'];

$nameError = $photoError = "";

if (isset($_POST['update'])) {
    $name = trim($_POST["name"]);
    $newImage = $_FILES["image"]["tmp_name"];
    $photoType = $_FILES["image"]["type"];

    if (!$name) {
        $nameError = "⚠️ Name is required...";
    }

    if ($newImage) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($photoType, $allowedTypes)) {
            $photoError = "⚠️ Invalid format. Only JPG, PNG, or WEBP allowed.";
        }
    }

    if (!$nameError && !$photoError) {
        if ($newImage) {
            try {
                $uploadResult = $cloudinary->uploadApi()->upload($newImage);
                $imageUrl = $uploadResult['secure_url'];
                $qry = "UPDATE genre SET name='$name', image='$imageUrl' WHERE gid=$gid";
            } catch (Exception $e) {
                echo "<script>alert('❌ Upload failed: " . $e->getMessage() . "');</script>";
                exit;
            }
        } else {
            $qry = "UPDATE genre SET name='$name' WHERE gid=$gid";
        }

        mysqli_query($con, $qry);
        echo "<script>window.location.assign('view_genres.php')</script>";
    }
}

if (isset($_POST["view"])) {
    echo "<script>window.location.href='view_genres.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Genre</title>
    <style>
        form {
            background-color: white;
            border-radius: 20px;
        }

        h1 {
            border-radius: 10px;
        }

        .input {
            border-radius: 10px;
            height: 40px;
            color: black;
        }

        #name {
            border-color: #edeef3;
            background-color: white;
        }

        #custom-button {
            padding: 10px;
            color: white;
            background-color: #8371fd;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        #custom-button:hover {
            background-color: #9080f4;
        }

        #custom-text {
            margin-left: 10px;
            font-family: sans-serif;
            color: #aaa;
        }

        .btn-lg {
            padding: 0rem 3rem;
            font-size: 0.875rem;
            border-radius: 10px;
        }

        .setimg {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tb {
            padding-left: 10px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div>
            <h2 class="main-content-title tx-24 mg-b-5">Update Genre</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Genre</li>
            </ol>
        </div>
    </div>

    <div class="col-md-6 m-auto d-block">
        <form id="form1" enctype="multipart/form-data" method="post"
            class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">
            <h1 class="text-center text-light font-weight-bold bg-primary p-3 shadow">
                <strong>Genre Form</strong>
            </h1>
            <br />

            <h5>Enter Genre Name:</h5>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>"
                placeholder="Enter Genre Name..." class="form-control input">
            <span class="error" id="nameError"><?= $nameError ?></span>
            <br>

            <h5>Upload Genre Image:</h5><br>
            <div class="setimg">
                <div class="img">
                    <img src="<?= $image ?>" class="rounded-10" height="100" width="100">
                </div>
                <div class="tb">
                    <input type="file" id="image" name="image" hidden="hidden" />
                    <button type="button" id="custom-button">CHOOSE A FILE</button>
                    <span id="custom-text">No file chosen, yet.</span>
                    <span class="error" id="photoError"><?= $photoError ?></span>
                </div>
            </div>
            <br>
            <center>
                <input type="submit" value="Update" name="update" id="update"
                    class="btn btn-outline-primary btn-lg input">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input">
            </center>
        </form>
    </div>

    <script>
        const realFileBtn = document.getElementById("image");
        const customBtn = document.getElementById("custom-button");
        const customTxt = document.getElementById("custom-text");

        customBtn.addEventListener("click", function () {
            realFileBtn.click();
        });

        realFileBtn.addEventListener("change", function () {
            if (realFileBtn.value) {
                customTxt.innerHTML = realFileBtn.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
            } else {
                customTxt.innerHTML = "No file chosen, yet.";
            }
        });

        document.getElementById("form1").addEventListener("submit", function (e) {
            let nameField = document.getElementById("name");
            let photoField = document.getElementById("image");
            let nameError = document.getElementById("nameError");
            let photoError = document.getElementById("photoError");

            let valid = true;

            nameError.textContent = "";
            photoError.textContent = "";

            if (nameField.value.trim() === "") {
                nameError.textContent = "⚠️ Name is required.";
                valid = false;
            }

            if (photoField.value) {
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const fileName = photoField.value.toLowerCase();
                const fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    photoError.textContent = "⚠️ Only JPG, JPEG, PNG, or WEBP formats are allowed.";
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
<?php include('fff.php'); ?>
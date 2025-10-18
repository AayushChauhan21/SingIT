<?php
include('demo.php');
include('hhh.php');
include('connection.php');
// require 'vendor/autoload.php';

// use Cloudinary\Cloudinary;

// $cloudinary = new Cloudinary([
//     'cloud' => [
//         'cloud_name' => 'do3hihcwa',
//         'api_key' => '684953537186227',
//         'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
//     ]
// ]);

// if (isset($_POST["insert"])) {
//     $name = trim($_POST["name"]);
//     $photoTmp = $_FILES["photo"]["tmp_name"];

//     if (!$name && !$photoTmp) {
//         echo "<script>alert('Artist Name & Image Is Required');</script>";
//     } elseif (!$name) {
//         echo "<script>alert('Name Is Required');</script>";
//     } elseif (!$photoTmp) {
//         echo "<script>alert('Image Is Required');</script>";
//     } else {
//         try {
//             $uploadResult = $cloudinary->uploadApi()->upload($photoTmp);
//             $imageUrl = $uploadResult['secure_url'];

//             $qry = "INSERT INTO artist (name, photo) VALUES ('$name', '$imageUrl')";
//             mysqli_query($con, $qry);

//             echo "<script>alert('✅ Artist Inserted Successfully');</script>";
//             echo "<script>window.location.href='view_artist.php';</script>";
//         } catch (Exception $e) {
//             echo "<script>alert('❌ Upload failed: " . $e->getMessage() . "');</script>";
//         }
//     }
// }

// if (isset($_POST["view"])) {
//     echo "<script>window.location.href='view_artist.php';</script>";
// }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="style_adm.css"> -->
    <!-- BOOTSTRAP CSS -->
    <!-- <link id="style" href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
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
            /* font-size:19px; */
            height: 40px;
            color: black;

        }

        #name {
            /* border-color: #edeef3; */
            /* border-color: gray; */
            /* background-color: white; */
        }

        #custom-button {
            padding: 10px;
            color: white;
            /* background-color: #8371fd; */
            background: linear-gradient(135deg, #6259ca, #ff6ec4);
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
            justify-content: center
        }

        .tb {
            /* font-size: 20px; */
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
            <h2 class="main-content-title tx-24 mg-b-5">Add Artist</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Artist</li>
            </ol>
        </div>

    </div>
    <div class="col-md-6 m-auto d-block">
        <!-- style="border:2px solid #E802A3" -->
        <form action="http://localhost/flutter_crud/addArtists.php" method="post" enctype="multipart/form-data"
            id="form1" class="mb-4 mt-5 border font-weight-bold bg-white p-5 shadow">
            <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
                <strong>Artist Form</strong>
            </h1>
            <br />

            <h5>Enter Artist Name:</h5>
            <input type="text" name="name" id="name" placeholder="Enter Artist Name..." class="form-control input" />
            <span id="nameError" class="error"></span>
            <br />

            <h5>Upload Artist Image:</h5>
            <input type="file" id="photo" name="photo" hidden="hidden" />
            <button type="button" id="custom-button">CHOOSE A FILE</button>
            <span id="custom-text">No file chosen, yet.</span>
            <br />
            <span id="photoError" class="error"></span>
            <br /><br />

            <center>
                <input type="submit" value="Insert" name="insert" id="insert"
                    class="btn btn-outline-primary btn-lg input" />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!-- <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input" /> -->
            </center>
        </form>
        <br />
    </div>

    <script>
        const realFileBtn = document.getElementById("photo");
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
            const nameField = document.getElementById("name");
            const photoField = document.getElementById("photo");
            const nameError = document.getElementById("nameError");
            const photoError = document.getElementById("photoError");

            let valid = true;

            nameError.textContent = "";
            photoError.textContent = "";
            // nameField.style.borderColor = "#edeef3";
            // photoField.style.borderColor = "#edeef3";

            if (e.submitter && e.submitter.name === "insert") {
                if (nameField.value.trim() === "") {
                    nameError.textContent = "⚠️ Name is required.";
                    // nameField.style.borderColor = "red";
                    valid = false;
                }

                if (!photoField.value) {
                    photoError.textContent = "⚠️ Image is required.";
                    // photoField.style.borderColor = "red";
                    valid = false;
                } else {
                    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                    const fileName = photoField.value.toLowerCase();
                    const fileExtension = fileName.split('.').pop();

                    if (!allowedExtensions.includes(fileExtension)) {
                        photoError.textContent = "⚠️ Only JPG, JPEG, PNG, or WEBP formats are allowed.";
                        // photoField.style.borderColor = "red";
                        valid = false;
                    }
                }

                if (!valid) {
                    e.preventDefault();
                }
            }
        });
    </script>

    <script>
        const realFileBtn = document.getElementById("photo");
        const customBtn = document.getElementById("custom-button");
        const customTxt = document.getElementById("custom-text");

        customBtn.addEventListener("click", function () {
            realFileBtn.click();
        });

        realFileBtn.addEventListener("change", function () {
            if (realFileBtn.value) {
                customTxt.innerHTML = realFileBtn.value.match(
                    /[\/\\]([\w\d\s\.\-\(\)]+)$/
                )[1];
            } else {
                customTxt.innerHTML = "No file chosen, yet.";
            }
        });
    </script>
</body>

</html>
<?php
include('fff.php');
?>
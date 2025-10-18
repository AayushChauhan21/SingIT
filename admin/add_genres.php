<?php
include('demo.php');
include('hhh.php');
include('connection.php');

$name = "";


// require 'vendor/autoload.php';

// use Cloudinary\Cloudinary;

// $cloudinary = new Cloudinary([
//     'cloud' => [
//         'cloud_name' => 'do3hihcwa',
//         'api_key' => '684953537186227',
//         'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
//     ]
// ]);

// $nameError = $photoError = "";
// $name = "";

// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["insert"])) {
//     $name = trim($_POST["name"]);
//     $photoTmp = $_FILES["photo"]["tmp_name"];

//     if (!$nameError && !$photoError) {
//         try {
//             $uploadResult = $cloudinary->uploadApi()->upload($photoTmp);
//             $imageUrl = $uploadResult['secure_url'];

//             $qry = "INSERT INTO genre (name, image) VALUES ('$name', '$imageUrl')";
//             mysqli_query($con, $qry);

//             echo "<script>alert('✅ Genre Inserted Successfully'); window.location.href='view_genres.php';</script>";
//         } catch (Exception $e) {
//             echo "<script>alert('❌ Upload failed: " . $e->getMessage() . "');</script>";
//         }
//     }
// }

// if (isset($_POST["view"])) {
//     echo "<script>window.location.href='view_genres.php';</script>";
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Genres</title>
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
            /* border-color: #edeef3; */
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

        .btn-lg {
            padding: 0rem 3rem;
            font-size: 0.875rem;
            border-radius: 10px;
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
            <h2 class="main-content-title tx-24 mg-b-5">Add Genres</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Genres</li>
            </ol>
        </div>
    </div>
    <div class="col-md-6 m-auto d-block">
        <form action="http://localhost/flutter_crud/addGenres.php" method="post" enctype="multipart/form-data"
            id="form1" class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">
            <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
                <strong>Genres Form</strong>
            </h1>
            <br />
            <h5>Enter Genres Name:</h5>
            <input type="text" name="name" id="name" placeholder="Enter Genres Name..." class="form-control input"
                value="<?= htmlspecialchars($name) ?>">
            <span class="error" id="nameError"></span>
            <br>
            <h5>Upload Genres Image:</h5>
            <input type="file" id="photo" name="photo" hidden="hidden" />
            <button type="button" id="custom-button">CHOOSE A FILE</button>
            <span id="custom-text">No file chosen, yet.</span>
            <span class="error" id="photoError"></span>
            <br />
            <center>
                <input type="submit" value="Insert" name="insert" id="insert"
                    class="btn btn-outline-primary btn-lg input">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!-- <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input"> -->
            </center>
        </form>
        <br>
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
    </script>

    <script>
        document.getElementById("form1").addEventListener("submit", function (e) {
            const nameField = document.getElementById("name");
            const photoField = document.getElementById("photo");
            const nameError = document.getElementById("nameError");
            const photoError = document.getElementById("photoError");

            let valid = true;

            nameError.textContent = "";
            photoError.textContent = "";

            // Only validate if the submit button is "Insert"
            if (e.submitter && e.submitter.name === "insert") {
                if (nameField.value.trim() === "") {
                    nameError.textContent = "⚠️ Name is required.";
                    valid = false;
                }

                if (!photoField.value) {
                    photoError.textContent = "⚠️ Image is required.";
                    valid = false;
                } else {
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
            }
        });
    </script>
</body>

</html>
<?php include('fff.php'); ?>

<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
<script src="assets/plugins/datatable/js/dataTables.buttons.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.bootstrap5.min.js"></script>
<script src="assets/plugins/datatable/js/jszip.min.js"></script>
<script src="assets/plugins/datatable/pdfmake/pdfmake.min.js"></script>
<script src="assets/plugins/datatable/pdfmake/vfs_fonts.js"></script>
<script src="assets/plugins/datatable/js/buttons.html5.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.print.min.js"></script>
<script src="assets/plugins/datatable/js/buttons.colVis.min.js"></script>
<script src="assets/plugins/datatable/dataTables.responsive.min.js"></script>
<script src="assets/plugins/datatable/responsive.bootstrap5.min.js"></script>
<script src="assets/js/table-data.js"></script>
<script src="assets/js/select2.js"></script>
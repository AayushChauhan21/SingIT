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

<style>
    #custom-button_v,
    #custom-button_i,
    #custom-button_in {
        padding: 10px;
        color: white;
        /* background-color: #8371fd; */
        background: linear-gradient(135deg, #6259ca, #ff6ec4);
        border: none;
        border-radius: 10px;
        cursor: pointer;
    }

    #custom-button_v:hover,
    #custom-button_i:hover,
    #custom-button_in:hover {
        background-color: #9080f4;
    }

    #custom-text_v,
    #custom-text_i,
    #custom-text_in {
        margin-left: 10px;
        font-family: sans-serif;
        color: #aaa;
    }

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
    <form action="http://localhost/SIngIT/flutter_crud/addArtists.php" method="post" enctype="multipart/form-data"
        id="form1" class="mb-4 mt-5 border font-weight-bold bg-white p-5 shadow">
        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Artist Form</strong>
        </h1>
        <br />

        <h5>Enter Artist Name:</h5>
        <input type="text" name="name" id="name" placeholder="Enter Artist Name..." class="form-control input" />
        <span id="nameError" class="error"></span>
        <br />

        <h5>Enter Artist Description:</h5>
        <textarea name="description" id="description" placeholder="Enter Artist Biography or Description..."
            class="form-control" rows="4"></textarea>
        <span id="descriptionError" class="error"></span>
        <br />

        <h5>Upload Artist Image:</h5>
        <input type="file" id="photo" name="photo" hidden="hidden" />
        <button type="button" id="custom-button">CHOOSE A FILE</button>
        <span id="custom-text">No file chosen, yet.</span>
        <span id="photoError" class="error"></span>
        <br />

        <h5>Upload Artist Image(transparent):</h5>
        <input type="file" id="image" name="image" accept="image/png" hidden="hidden" />
        <button type="button" id="custom-button_v">CHOOSE A FILE</button>
        <span id="custom-text_v">No file chosen, yet.</span>
        <span class="error" id="audioVError"></span>
        <br />

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
    // --- Setup Custom File Inputs ---

    // 1. Main Artist Image (id="photo")
    const realFileBtn_Main = document.getElementById("photo");
    const customBtn_Main = document.getElementById("custom-button");
    const customTxt_Main = document.getElementById("custom-text");

    customBtn_Main.addEventListener("click", function () {
        realFileBtn_Main.click();
    });

    realFileBtn_Main.addEventListener("change", function () {
        if (realFileBtn_Main.value) {
            customTxt_Main.innerHTML = realFileBtn_Main.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            customTxt_Main.innerHTML = "No file chosen, yet.";
        }
    });

    // 2. Second Artist Image (id="image")
    const realFileBtn_Second = document.getElementById("image");
    const customBtn_Second = document.getElementById("custom-button_v"); // Uses _v suffix button
    const customTxt_Second = document.getElementById("custom-text_v"); // Uses _v suffix text

    customBtn_Second.addEventListener("click", function () {
        realFileBtn_Second.click();
    });

    realFileBtn_Second.addEventListener("change", function () {
        if (realFileBtn_Second.files.length > 0) {
            // FIX: Use the correct custom text element (customTxt_Second or customTxtV)
            customTxt_Second.innerHTML = realFileBtn_Second.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            customTxt_Second.innerHTML = "No file chosen, yet.";
        }
    });

    // --- Form Submission Validation ---

    document.getElementById("form1").addEventListener("submit", function (e) {
        const nameField = document.getElementById("name");
        const descriptionField = document.getElementById("description");
        const photoField = document.getElementById("photo");
        // FIX: Renamed variable to clearly indicate this is the second image field
        const imageSecondField = document.getElementById("image");

        const nameError = document.getElementById("nameError");
        const photoError = document.getElementById("photoError");
        const descriptionError = document.getElementById("descriptionError");

        // FIX: Using the correct HTML ID for the error element
        const imageSecondError = document.getElementById("audioVError");

        let valid = true;

        nameError.textContent = "";
        photoError.textContent = "";
        descriptionError.textContent = "";
        imageSecondError.textContent = ""; // Clear the 2nd Image error

        if (e.submitter && e.submitter.name === "insert") {
            // 1. Validate Artist Name (Required)
            if (nameField.value.trim() === "") {
                nameError.textContent = "⚠️ Name is required.";
                valid = false;
            }

            // 2. Validate Description (Required)
            if (descriptionField.value.trim() === "") {
                descriptionError.textContent = "⚠️ Description is required.";
                valid = false;
            }

            // 3. Validate Main Artist Photo (Required & Image Format)
            if (!photoField.value) {
                photoError.textContent = "⚠️ Photo is required.";
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

            // 4. Validate Second Artist Image (Required & PNG Format)
            // Note: The HTML has accept="audio/*", but validation checks PNG. We proceed with PNG check.
            if (!imageSecondField.value) {
                imageSecondError.textContent = "⚠️ Second Image is required.";
                valid = false;
            } else {
                const allowedExtensions = ['png']; // Only PNG is allowed for the transparent background image
                const fileName = imageSecondField.value.toLowerCase();
                const fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    imageSecondError.textContent = "⚠️ Only PNG format is allowed for the transparent image.";
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        }
    });
</script>

<?php
include('fff.php');
?>
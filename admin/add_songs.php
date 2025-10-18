<?php
include('demo.php');
include('hhh.php');
include('connection.php');


$artistQuery = mysqli_query($con, "SELECT arid, name FROM artist ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($artistQuery)) {
    $artistOptions[] = $row;
}

$genreQuery = mysqli_query($con, "SELECT gid, name FROM genre ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($genreQuery)) {
    $genreOptions[] = $row;
}

$languageQuery = mysqli_query($con, "SELECT lid, name FROM language ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($languageQuery)) {
    $languageOptions[] = $row;
}

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
//     $name = trim($_POST["trackName"]);
//     $album = trim($_POST["albumName"]);
//     $length = $_POST["duration"] ?? 0;
//     $lyrics = $_POST["syncedLyrics"] ?? '';
//     $imageUrl = $_POST["albumImageUrl"] ?? '';
//     $audioTmp = $_FILES["audio_v"]["tmp_name"] ?? '';
//     $instTmp = $_FILES["audio_i"]["tmp_name"] ?? '';
//     $manualTmp = $_FILES["manualImage"]["tmp_name"] ?? '';

//     if (!$name || !$audioTmp) {
//         echo "<script>alert('Track Name & Vocal Audio are required');</script>";
//         exit;
//     }

//     try {
//         $uploadResult = $cloudinary->uploadApi()->upload($audioTmp, ['resource_type' => 'video']);
//         $vocalUrl = $uploadResult['secure_url'];
//     } catch (Exception $e) {
//         echo "<script>alert('❌ Vocal upload failed: " . $e->getMessage() . "');</script>";
//         exit;
//     }

//     $instrumentalUrl = '';
//     if ($instTmp) {
//         try {
//             $uploadResult = $cloudinary->uploadApi()->upload($instTmp, ['resource_type' => 'video']);
//             $instrumentalUrl = $uploadResult['secure_url'];
//         } catch (Exception $e) {
//             echo "<script>alert('❌ Instrumental upload failed: " . $e->getMessage() . "');</script>";
//             exit;
//         }
//     }

//     if (!$imageUrl && $manualTmp) {
//         try {
//             $imgUpload = $cloudinary->uploadApi()->upload($manualTmp);
//             $imageUrl = $imgUpload['secure_url'];
//         } catch (Exception $e) {
//             echo "<script>alert('❌ Image upload failed: " . $e->getMessage() . "');</script>";
//             exit;
//         }
//     }

//     $qry = "INSERT INTO song (name, image, length, lyrics, album, instrumental, vocal)
//             VALUES (?, ?, ?, ?, ?, ?, ?)";

//     $stmt = $con->prepare($qry);
//     $stmt->bind_param("ssissss", $name, $imageUrl, $length, $lyrics, $album, $instrumentalUrl, $vocalUrl);

//     if ($stmt->execute()) {
//         $songId = $stmt->insert_id;

//         // 🎯 Multiple Artist Mapping
//         if (!empty($_POST['artistId'])) {
//             foreach ($_POST['artistId'] as $artistId) {
//                 mysqli_query($con, "INSERT INTO artist_song (artist_id, song_id) VALUES ('$artistId', '$songId')");
//             }
//         }

//         // 🎯 Multiple Genre Mapping
//         if (!empty($_POST['genreId'])) {
//             foreach ($_POST['genreId'] as $genreId) {
//                 mysqli_query($con, "INSERT INTO genre_song (genre_id, song_id) VALUES ('$genreId', '$songId')");
//             }
//         }

//         echo "<script>alert('✅ Song Inserted Successfully'); window.location.href='view_songs.php';</script>";
//     } else {
//         echo "<script>alert('❌ Insert failed: " . $stmt->error . "');</script>";
//     }

//     $stmt->close();
//     $con->close();
// }

// if (isset($_POST["view"])) {
//     echo "<script>window.location.href='view_songs.php';</script>";
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

    .img-preview {
        display: none;
        margin-top: 10px;
        border-radius: 10px;
        box-shadow: 0 0 10px #ccc;
    }

    .error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }
</style>

<script>
    async function fetchLyrics() {
        const artistSelect = document.getElementById("artistId");
        const selectedArtist = artistSelect.selectedOptions[0]?.text || "";
        const track = document.getElementById("trackName").value;

        if (!selectedArtist || !track) {
            alert("Please select at least one artist and enter track name.");
            return;
        }

        const res = await fetch(
            `lyrics_fetch.php?artist=${encodeURIComponent(selectedArtist)}&track=${encodeURIComponent(track)}`
        );
        const data = await res.json();

        if (data.status === "found") {
            document.getElementById("albumName").value = data.albumName;
            document.getElementById("duration").value = data.duration;
            document.getElementById("syncedLyrics").value = data.syncedLyrics;
            document.getElementById("albumImageUrl").value = data.albumImage;

            document.getElementById("albumImagePreview").src = data.albumImage;
            document.getElementById("albumImagePreview").style.display = "block";
            document.getElementById("manualImageUpload").style.display = "none";
        } else {
            document.getElementById("albumImagePreview").style.display = "none";
            document.getElementById("manualImageUpload").style.display = "block";
            alert("❌ No data found. Please enter manually.");
        }
    }
</script>


<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Add Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Songs</li>
        </ol>
    </div>

</div>

<!-- <div class="col-sm-6 col-md-6 col-lg-3">
    <div class="card custom-card text-center">
        <div class="card-body">
            <div>
                <h6 class="main-content-label mb-1">Success alert</h6>
                <p class="text-muted card-sub-title">A Success Message</p>
            </div>
            <div class="btn ripple btn-primary" id="swal-success">
                Click me !
            </div>
        </div>
    </div>
</div> -->

<div class="col-md-6 m-auto d-block">
    <form action="http://localhost/SIngIT/flutter_crud/addSongs.php" method="post" enctype="multipart/form-data"
        id="form1" class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">

        <h1 class="text-center text-light font-weight-bold p-3" id="gradient">
            <strong>Song Form</strong>
        </h1>
        <br />

        <br>
        <h5>Track Name:</h5>
        <input type="text" name="trackName" id="trackName" placeholder="Enter Track Name..." class="form-control input">
        <span class="error" id="trackNameError"></span>

        <!-- Genre -->
        <br>
        <h5 class="select-label">Genre(s):</h5>
        <select name="genreId[]" id="genreId" class="custom-select" multiple>
            <?php foreach ($genreOptions as $genre): ?>
                <option value="<?= $genre['gid'] ?>"><?= htmlspecialchars($genre['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <span class=" error" id="genreError"></span>

        <!-- Artist -->
        <br>
        <h5 class="select-label">Artist(s):</h5>
        <select name="artistId[]" id="artistId" class="custom-select" multiple>
            <?php foreach ($artistOptions as $artist): ?>
                <option value="<?= $artist['arid'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <span class=" error" id="artistError"></span>

        <br>
        <h5 class="select-label">Language(s):</h5>
        <select name="languageId[]" id="languageId" class="custom-select" multiple>
            <?php foreach ($languageOptions as $language): ?>
                <option value="<?= $language['lid'] ?>"><?= htmlspecialchars($language['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <span class=" error" id="languageError"></span>



        <br />

        <center>
            <button type="button" class="btn btn-outline-success mt-2 btn-lg input"
                onclick="fetchLyrics()">Auto-Fill</button>
        </center>

        <!-- Album Name -->
        <br>
        <h5>Album Name:</h5>
        <input type="text" name="albumName" id="albumName" placeholder="Enter Album Name..." class="form-control input">
        <span class="error" id="albumError"></span>

        <!-- Duration -->
        <br>
        <h5>Duration (in seconds):</h5>
        <input type="text" name="duration" id="duration" placeholder="Enter Duration..." class="form-control input">
        <span class="error" id="durationError"></span>

        <!-- Lyrics -->
        <br>
        <h5>Lyrics:</h5>
        <textarea name="syncedLyrics" id="syncedLyrics" rows="4" placeholder="Enter Lyrics..."
            class="form-control input" style="padding-top: 10px;"></textarea>
        <span class="error" id="lyricsError"></span>

        <input type="hidden" name="albumImageUrl" id="albumImageUrl">
        <img id="albumImagePreview" src="" alt="Album Image Preview" class="img-fluid img-preview mt-3 mb-3"
            height="100" width="100">

        <div id="manualImageUpload">
            <br>
            <h5>Upload Album Image (Manual):</h5>
            <!-- <input type="file" name="manualImage" accept="image/*" placeholder="" class="form-control input"> -->
            <input type="file" id="manualImage" name="manualImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_in">CHOOSE A FILE</button>
            <span id="custom-text_in">No file chosen, yet.</span>
            <span class="error" id="imageError"></span>
            <br>
        </div>

        <h5>Upload Vocal Audio:</h5>
        <input type="file" id="audio_v" name="audio_v" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_v">CHOOSE A FILE</button>
        <span id="custom-text_v">No file chosen, yet.</span>
        <span class="error" id="audioVError"></span>
        <br />

        <h5>Upload Instrumental Audio:</h5>
        <input type="file" id="audio_i" name="audio_i" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_i">CHOOSE A FILE</button>
        <span id="custom-text_i">No file chosen, yet.</span>
        <span class="error" id="audioIError"></span>
        <br />

        <center>
            <input type="submit" value="Insert" name="insert" id="insert" class="btn btn-outline-primary btn-lg input">
            &nbsp;&nbsp;&nbsp;
            <!-- <input type="submit" id="view" name="view" value="View" class="btn btn-outline-danger btn-lg input"> -->
        </center>
    </form>
</div>

<script>
    document.getElementById("form1").addEventListener("submit", function (e) {
        let valid = true;

        const trackName = document.getElementById("trackName");
        const genre = document.getElementById("genreId");
        const artist = document.getElementById("artistId");
        const language = document.getElementById("languageId");
        const albumName = document.getElementById("albumName");
        const duration = document.getElementById("duration");
        const lyrics = document.getElementById("syncedLyrics");
        const audioV = document.getElementById("audio_v");
        const audioI = document.getElementById("audio_i");

        // Clear all error messages
        document.querySelectorAll(".error").forEach(el => el.textContent = "");

        // Validation checks
        if (trackName.value.trim() === "") {
            document.getElementById("trackNameError").textContent = "⚠️ Track name is required.";
            valid = false;
        }

        if (genre.value === "") {
            document.getElementById("genreError").textContent = "⚠️ Select a genre.";
            valid = false;
        }

        if (artist.value === "") {
            document.getElementById("artistError").textContent = "⚠️ Select an artist.";
            valid = false;
        }

        if (language.selectedOptions.length === 0) {
            document.getElementById("languageError").textContent = "⚠️ Select at least one language.";
            valid = false;
        }

        if (albumName.value.trim() === "") {
            document.getElementById("albumError").textContent = "⚠️ Album name is required.";
            valid = false;
        }

        if (duration.value.trim() === "" || isNaN(duration.value) || parseInt(duration.value) <= 0) {
            document.getElementById("durationError").textContent = "⚠️ Enter valid duration.";
            valid = false;
        }

        if (lyrics.value.trim() === "") {
            document.getElementById("lyricsError").textContent = "⚠️ Lyrics are required.";
            valid = false;
        }

        if (audioV.files.length === 0) {
            document.getElementById("audioVError").textContent = "⚠️ Upload vocal audio.";
            valid = false;
        }

        if (audioI.files.length === 0) {
            document.getElementById("audioIError").textContent = "⚠️ Upload instrumental audio.";
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    // 🧼 Clear error on input or change
    [{
        input: "trackName",
        error: "trackNameError",
        type: "text"
    },
    {
        input: "genreId",
        error: "genreError",
        type: "select"
    },
    {
        input: "artistId",
        error: "artistError",
        type: "select"
    },
    {
        input: "albumName",
        error: "albumError",
        type: "text"
    },
    {
        input: "duration",
        error: "durationError",
        type: "text"
    },
    {
        input: "syncedLyrics",
        error: "lyricsError",
        type: "text"
    },
    {
        input: "audio_v",
        error: "audioVError",
        type: "file"
    },
    {
        input: "audio_i",
        error: "audioIError",
        type: "file"
    }
    ].forEach(({
        input,
        error,
        type
    }) => {
        const el = document.getElementById(input);
        const err = document.getElementById(error);

        if (type === "text") {
            el.addEventListener("input", () => {
                if (el.value.trim() !== "") err.textContent = "";
            });
        } else if (type === "select") {
            el.addEventListener("change", () => {
                if (el.value !== "") err.textContent = "";
            });
        } else if (type === "file") {
            el.addEventListener("change", () => {
                if (el.files.length > 0) err.textContent = "";
            });
        }
    });
</script>

<script>
    const realFileBtnV = document.getElementById("audio_v");
    const customBtnV = document.getElementById("custom-button_v");
    const customTxtV = document.getElementById("custom-text_v");

    customBtnV.addEventListener("click", function () {
        realFileBtnV.click();
    });

    realFileBtnV.addEventListener("change", function () {
        if (realFileBtnV.files.length > 0) {
            customTxtV.innerHTML = realFileBtnV.files[0].name;
        } else {
            customTxtV.innerHTML = "No file chosen, yet.";
        }
    });

    const realFileBtnI = document.getElementById("audio_i");
    const customBtnI = document.getElementById("custom-button_i");
    const customTxtI = document.getElementById("custom-text_i");

    customBtnI.addEventListener("click", function () {
        realFileBtnI.click();
    });

    realFileBtnI.addEventListener("change", function () {
        if (realFileBtnI.files.length > 0) {
            customTxtI.innerHTML = realFileBtnI.files[0].name;
        } else {
            customTxtI.innerHTML = "No file chosen, yet.";
        }
    });

    const realFileBtnImg = document.getElementById("manualImage");
    const customBtnImg = document.getElementById("custom-button_in");
    const customTxtImg = document.getElementById("custom-text_in");

    customBtnImg.addEventListener("click", function () {
        realFileBtnImg.click();
    });

    realFileBtnImg.addEventListener("change", function () {
        if (realFileBtnImg.files.length > 0) {
            customTxtImg.innerHTML = realFileBtnImg.files[0].name;
        } else {
            customTxtImg.innerHTML = "No file chosen, yet.";
        }
    });
</script>



<?php
include('fff.php');
?>
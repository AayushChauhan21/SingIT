<?php
include('demo.php');
include('hhh.php');
include('con.php');
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'do3hihcwa',
        'api_key' => '684953537186227',
        'api_secret' => 'KlMn8Q-QtRfVcVTXBomtZC2U1Og'
    ]
]);

$sid = $_GET['sid'] ?? 0;
if (!$sid) {
    echo "<script>alert('Invalid song ID'); window.location.href='view_songs.php';</script>";
    exit;
}

$songQuery = mysqli_query($con, "SELECT * FROM song WHERE sid = '$sid'");
$song = mysqli_fetch_assoc($songQuery);

$artistOptions = [];
$genreOptions = [];

$artistQuery = mysqli_query($con, "SELECT arid, name FROM artist ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($artistQuery)) {
    $artistOptions[] = $row;
}

$genreQuery = mysqli_query($con, "SELECT gid, name FROM genre ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($genreQuery)) {
    $genreOptions[] = $row;
}

$selectedArtists = [];
$selectedGenres = [];

$artistMap = mysqli_query($con, "SELECT artist_id FROM artist_song WHERE song_id = '$sid'");
while ($row = mysqli_fetch_assoc($artistMap)) {
    $selectedArtists[] = $row['artist_id'];
}

$genreMap = mysqli_query($con, "SELECT genre_id FROM genre_song WHERE song_id = '$sid'");
while ($row = mysqli_fetch_assoc($genreMap)) {
    $selectedGenres[] = $row['genre_id'];
}

// time ne 00:02:04 -> 204 kare che
function timeStringToSeconds($timeString)
{
    $parts = explode(':', $timeString);
    $parts = array_reverse($parts); // Handle MM:SS or HH:MM:SS

    $seconds = 0;
    if (isset($parts[0]))
        $seconds += (int) $parts[0];         // Seconds
    if (isset($parts[1]))
        $seconds += (int) $parts[1] * 60;     // Minutes
    if (isset($parts[2]))
        $seconds += (int) $parts[2] * 3600;   // Hours

    return $seconds;
}

function formatDuration($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
}

// ✅ Final usage
$seconds = timeStringToSeconds($song['length']);       // Converts "02:04" → 124
$displayTime = formatDuration($seconds);                // Converts 124 → "00:02:04"

$seconds = timeStringToSeconds($song['length']);
$displayTime = formatDuration($seconds);
// atlu

if (isset($_POST["update"])) {
    $name = trim($_POST["trackName"]);
    $album = trim($_POST["albumName"]);
    $rawDuration = $_POST["duration"] ?? 0; // e.g. "65"
    $length = formatDuration((int) $rawDuration); // Converts to "00:01:05"
    $lyrics = $_POST["syncedLyrics"] ?? '';
    $imageUrl = $_POST["albumImageUrl"] ?? $song['image'];
    $audioTmp = $_FILES["audio_v"]["tmp_name"] ?? '';
    $instTmp = $_FILES["audio_i"]["tmp_name"] ?? '';
    $manualTmp = $_FILES["manualImage"]["tmp_name"] ?? '';

    try {
        if ($audioTmp) {
            $uploadResult = $cloudinary->uploadApi()->upload($audioTmp, ['resource_type' => 'video']);
            $vocalUrl = $uploadResult['secure_url'];
        } else {
            $vocalUrl = $song['vocal'];
        }

        if ($instTmp) {
            $uploadResult = $cloudinary->uploadApi()->upload($instTmp, ['resource_type' => 'video']);
            $instrumentalUrl = $uploadResult['secure_url'];
        } else {
            $instrumentalUrl = $song['instrumental'];
        }

        if ($manualTmp) {
            $imgUpload = $cloudinary->uploadApi()->upload($manualTmp);
            $imageUrl = $imgUpload['secure_url'];
        }

        $qry = "UPDATE song SET name=?, image=?, length=?, lyrics=?, album=?, instrumental=?, vocal=? WHERE sid=?";
        $stmt = $con->prepare($qry);
        $stmt->bind_param("sssssssi", $name, $imageUrl, $length, $lyrics, $album, $instrumentalUrl, $vocalUrl, $sid);

        if ($stmt->execute()) {
            mysqli_query($con, "DELETE FROM artist_song WHERE song_id = '$sid'");
            mysqli_query($con, "DELETE FROM genre_song WHERE song_id = '$sid'");

            if (!empty($_POST['artistId'])) {
                foreach ($_POST['artistId'] as $artistId) {
                    mysqli_query($con, "INSERT INTO artist_song (artist_id, song_id) VALUES ('$artistId', '$sid')");
                }
            }

            if (!empty($_POST['genreId'])) {
                foreach ($_POST['genreId'] as $genreId) {
                    mysqli_query($con, "INSERT INTO genre_song (genre_id, song_id) VALUES ('$genreId', '$sid')");
                }
            }

            echo "<script>alert('✅ Song Updated Successfully'); window.location.href='view_songs.php';</script>";
        } else {
            echo "<script>alert('❌ Update failed: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "<script>alert('❌ Error: " . $e->getMessage() . "');</script>";
    }
}
?>


<style>
    #custom-button_v,
    #custom-button_i,
    #custom-button_in {
        padding: 10px;
        color: white;
        background-color: #8371fd;
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

    .form-control {
        border-radius: 10px;
        /* font-size:19px; */
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

<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Add Songs</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Songs</li>
        </ol>
    </div>

</div>

<div class="col-md-6 m-auto d-block">
    <form method="POST" action="" enctype="multipart/form-data" id="form1"
        class="mb-4 mt-5 font-weight-bold border bg-white p-5 shadow">
        <h1 class="text-center text-light font-weight-bold bg-primary p-3 shadow">
            <strong>Edit Song</strong>
        </h1>
        <br />

        <div class="form-group">
            <h5>Track Name:</h5>
            <input type="text" name="trackName" id="trackName" class="form-control"
                value="<?php echo htmlspecialchars($song['name']); ?>">
            <span class="error" id="trackNameError"></span>
        </div>

        <div class="form-group">
            <h5>Select Genres:</h5>
            <select name="genreId[]" class="form-control" multiple>
                <?php foreach ($genreOptions as $genre): ?>
                    <option value="<?php echo $genre['gid']; ?>" <?php echo in_array($genre['gid'], $selectedGenres) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="error" id="genreError"></span>

        </div>

        <div class="form-group">
            <h5>Select Artists:</h5>
            <select name="artistId[]" id="artistId" class="form-control" multiple>
                <?php foreach ($artistOptions as $artist): ?>
                    <option value="<?php echo $artist['arid']; ?>" <?php echo in_array($artist['arid'], $selectedArtists) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($artist['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="error" id="artistError"></span>

        </div>

        <!-- aa junu -->

        <center>
            <button type="button" class="btn btn-outline-success mt-2 btn-lg input"
                onclick="fetchLyrics()">Auto-Fill</button>
        </center>



        <div class="form-group">
            <h5>Album Name:</h5>
            <input type="text" name="albumName" id="albumName" class="form-control"
                value="<?php echo htmlspecialchars($song['album']); ?>">
            <span class="error" id="albumError"></span>
        </div>

        <div class="form-group">
            <h5>Duration (seconds):</h5>
            <input type="text" name="duration" id="duration" class="form-control"
                value="<?= timeStringToSeconds($song['length']) ?>">
            <span class="error" id="durationError"></span>
        </div>

        <div class="form-group">
            <h5>Lyrics:</h5>
            <textarea name="syncedLyrics" id="syncedLyrics" rows="4"
                class="form-control input"><?= htmlspecialchars($song['lyrics']) ?></textarea>
            <span class="error" id="lyricsError"></span>
        </div>

        <div id="manualImageUpload">
            <h5>Upload Album Image (Manual):</h5>
            <input type="file" id="manualImage" name="manualImage" accept="image/*" hidden="hidden" />
            <button type="button" id="custom-button_in">CHOOSE A FILE</button>
            <span id="custom-text_in">No file chosen, yet.</span>
            <br>
        </div>

        <div class="form-group">
            <!-- <h5>Album Image URL</h5> -->
            <input type="text" name="albumImageUrl" id="albumImageUrl" class="form-control" hidden
                value="<?php echo htmlspecialchars($song['image']); ?>">
            <img id="albumImagePreview" src="<?php echo htmlspecialchars($song['image']); ?>"
                style="height:80px; width:80px; object-fit:cover; border-radius:8px; margin-top:10px; box-shadow:0 0 6px rgba(0,0,0,0.2);">
        </div>

        <h5>Upload Vocal Audio:</h5>
        <input type="file" id="audio_v" name="audio_v" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_v">CHOOSE A FILE</button>
        <span id="custom-text_v">No file chosen, yet.</span>
        <span class="error" id="audioVError"></span>

        <audio controls class="mt-2" style="height:30px;">
            <source src="<?= $song['vocal'] ?>" type="audio/mpeg">
        </audio>
        <br />

        <h5>Upload Instrumental Audio:</h5>
        <input type="file" id="audio_i" name="audio_i" accept="audio/*" hidden="hidden" />
        <button type="button" id="custom-button_i">CHOOSE A FILE</button>
        <span id="custom-text_i">No file chosen, yet.</span>
        <span class="error" id="audioIError"></span>
        <audio controls class="mt-2" style="height:30px;">
            <source src="<?= $song['instrumental'] ?>" type="audio/mpeg">
        </audio>
        <br />
        <br />

        <center>
            <button type="submit" name="update" class="btn btn-outline-primary btn-lg input">Update</button>
            <!-- &nbsp;&nbsp;&nbsp;
            <button type="submit" name="view" class="btn btn-outline-danger btn-lg input">View</button> -->
        </center>
    </form>
</div>

<script>
    async function fetchLyrics() {
        const artistSelect = document.getElementById("artistId");
        const selectedArtist = artistSelect.selectedOptions[0]?.text || "";
        const track = document.getElementById("trackName").value;

        if (!selectedArtist || !track) {
            alert("Please select at least one artist and enter track name.");
            return;
        }

        try {
            const res = await fetch(
                `lyrics_fetch.php?artist=${encodeURIComponent(selectedArtist)}&track=${encodeURIComponent(track)}`
            );
            const data = await res.json();

            if (data.status === "found") {
                document.getElementById("albumName").value = data.albumName;
                document.getElementById("duration").value = data.duration;
                document.getElementById("syncedLyrics").value = data.syncedLyrics;
                document.getElementById("albumImageUrl").value = data.albumImage;

                const preview = document.getElementById("albumImagePreview");
                preview.src = data.albumImage;
                preview.style.display = "block";
            } else {
                document.getElementById("albumImagePreview").style.display = "none";
                alert("❌ No data found. Please enter manually.");
            }
        } catch (err) {
            alert("❌ Error fetching lyrics: " + err.message);
        }
    }

    // Manual image preview on file select
    document.getElementById("manualImage").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById("manualImagePreview");
            preview.src = URL.createObjectURL(file);
            preview.style.display = "block";
        }
    });

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

<script>
    document.getElementById("form1").addEventListener("submit", function (e) {
        let valid = true;

        const trackName = document.getElementById("trackName");
        const genreSelect = document.querySelector("select[name='genreId[]']");
        const artistSelect = document.getElementById("artistId");
        const albumName = document.getElementById("albumName");
        const duration = document.getElementById("duration");
        const syncedLyrics = document.getElementById("syncedLyrics");
        const audioV = document.getElementById("audio_v");
        const audioI = document.getElementById("audio_i");
        const manualImage = document.getElementById("manualImage");

        // Clear previous errors
        document.querySelectorAll(".error").forEach(el => el.textContent = "");

        // Track name
        if (trackName.value.trim() === "") {
            document.getElementById("trackNameError").textContent = "⚠️ Track name is required.";
            valid = false;
        }

        // Genre selection
        if ([...genreSelect.selectedOptions].length === 0) {
            document.getElementById("genreError").textContent = "⚠️ Select at least one genre.";
            valid = false;
        }

        // Artist selection
        if ([...artistSelect.selectedOptions].length === 0) {
            document.getElementById("artistError").textContent = "⚠️ Select at least one artist.";
            valid = false;
        }

        if (albumName.value.trim() === "") {
            document.getElementById("albumError").textContent = "⚠️ Album name is required.";
            valid = false;
        }

        // Duration
        const durValue = duration.value.trim();
        if (durValue === "") {
            document.getElementById("durationError").textContent = "⚠️ Duration is required.";
            valid = false;
        } else {
            const dur = parseInt(durValue);
            if (isNaN(dur) || dur <= 0) {
                document.getElementById("durationError").textContent = "⚠️ Duration must be a positive number.";
                valid = false;
            }
        }

        if (syncedLyrics.value.trim() === "") {
            document.getElementById("lyricsError").textContent = "⚠️ Lyrics is required.";
            valid = false;
        }

        // File validation helper
        const validateFile = (input, allowedTypes, errorId, label) => {
            if (input.files.length > 0) {
                const type = input.files[0].type;
                if (!allowedTypes.includes(type)) {
                    document.getElementById(errorId).textContent = `⚠️ Invalid ${label} format.`;
                    valid = false;
                }
            }
        };

        validateFile(audioV, ['audio/mpeg', 'audio/mp3', 'audio/wav'], "audioVError", "vocal audio");
        validateFile(audioI, ['audio/mpeg', 'audio/mp3', 'audio/wav'], "audioIError", "instrumental audio");
        validateFile(manualImage, ['image/jpeg', 'image/png', 'image/webp'], "imageError", "album image");

        if (!valid) e.preventDefault();
    });
</script>

<?php include('fff.php'); ?>
<script>
    // --- SweetAlert Helper Functions ---
    function showSuccessAlert(msg) {
        swal({
            title: 'Well done!',
            text: msg,
            type: 'success',
            confirmButtonColor: '#57a94f'
        });
    }

    function showErrorAlert(msg) {
        swal({
            title: 'Oops!',
            text: msg,
            type: 'error',
            confirmButtonColor: '#ff0000'
        });
    }

    // --- 1. ID કાઢવા માટેના ફંક્શન ---
    function extractId(url, paramName) {
        var urlParams = new URLSearchParams(url.split('?')[1]);
        return urlParams.get(paramName) || 0;
    }

    // દરેક એન્ટિટી માટે ચોક્કસ ID એક્સટ્રેક્ટર ફંક્શન્સ
    function extractSongId(url) {
        return extractId(url, 'sid');
    }

    function extractArtistId(url) {
        return extractId(url, 'arid');
    }

    function extractGenreId(url) {
        return extractId(url, 'gid');
    }

    function extractLanguageId(url) {
        return extractId(url, 'lid');
    }

    function extractSliderId(url) {
        return extractId(url, 'id');
    } // Slider માટે 'id' વાપરવામાં આવે છે

    // --- 3. Generic Delete Function ---
    function setupDeleteLogic(tableClass, deleteButtonClass, apiURL, idExtractor, entityName) {
        $(tableClass).on('click', deleteButtonClass, function (e) {
            e.preventDefault();

            var deleteUrl = $(this).attr('href');
            var id_value = idExtractor(deleteUrl);

            // API માં મોકલવા માટે ID key નક્કી કરો (sid, arid, gid, lid, id)
            var id_key = deleteUrl.includes('sid=') ? 'sid' :
                (deleteUrl.includes('arid=') ? 'arid' :
                    (deleteUrl.includes('gid=') ? 'gid' :
                        (deleteUrl.includes('lid=') ? 'lid' :
                            'id')));

            if (!id_value) {
                showErrorAlert(entityName + " ID not found in the link.");
                return;
            }

            // Main Confirmation Pop-up
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this " + entityName + "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
                function (isConfirm) {
                    if (isConfirm) {
                        swal({
                            title: "Deleting...",
                            text: "Please wait while we delete the " + entityName + ".",
                            type: "info",
                            showConfirmButton: false,
                        });

                        // AJAX Call
                        var dataToSend = {};
                        dataToSend[id_key] = id_value;

                        $.ajax({
                            url: apiURL,
                            type: 'POST',
                            data: dataToSend,
                            dataType: 'json',
                            success: function (response) {
                                if (response.status === 'success') {
                                    swal({
                                        title: "Deleted!",
                                        text: response.message,
                                        type: "success",
                                        showConfirmButton: false,
                                        timer: 2000
                                    });

                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);

                                } else {
                                    showErrorAlert(response.message);
                                }
                            },
                            error: function (xhr, status, error) {
                                showErrorAlert("Server error or connection failed for " +
                                    entityName + " deletion.");
                            }
                        });

                    } else {
                        swal({
                            title: "Cancelled",
                            text: "Your " + entityName + " is safe :)",
                            type: "error",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
        });
    }

    // ------------------------------------------------------------------
    // --- Document Ready / Initialization ---
    // ------------------------------------------------------------------
    $(document).ready(function () {

    // --- Session Status Check Logic (જેમ છે તેમ) ---
    <?php if (isset($_SESSION['status'])): ?>

        var status = '<?php echo $_SESSION['status']; ?>';
            var message = '<?php echo addslashes($_SESSION['message']); ?>';

            if (status === 'success') {
                showSuccessAlert(message);
            } else {
                showErrorAlert(message);
            }

        <?php unset($_SESSION['status']); ?>
                <?php unset($_SESSION['message']); ?>

                <?php endif; ?>

    // ------------------------------------------------------------------
    // --- Setting up Delete Logic for All Entities ---
    // ------------------------------------------------------------------

    // A) SONG DELETE LOGIC (Normal Songs)
    setupDeleteLogic('.song_table', '.song-delete-btn', 'http://localhost/SIngIT/flutter_crud/deleteSong.php',
                    extractSongId, 'Song');

        // B) ARTIST DELETE LOGIC
        setupDeleteLogic('.artist_table', '.artist-delete-btn',
            'http://localhost/SIngIT/flutter_crud/deleteArtist.php', extractArtistId, 'Artist');

        // C) GENRE DELETE LOGIC
        setupDeleteLogic('.genre_table', '.genre-delete-btn',
            'http://localhost/SIngIT/flutter_crud/deleteGenre.php', extractGenreId, 'Genre');

        // D) LANGUAGE DELETE LOGIC
        setupDeleteLogic('.language_table', '.language-delete-btn',
            'http://localhost/SIngIT/flutter_crud/deleteLanguage.php', extractLanguageId, 'Language');

        // E) SLIDER DELETE LOGIC
        setupDeleteLogic('.slider_table', '.slider-delete-btn',
            'http://localhost/SIngIT/flutter_crud/deleteSlider.php', extractSliderId, 'Slider');

        // 🟢 F) SPECIAL SONG DELETE LOGIC (આ તમારો નવો કૉલ છે)
        // Note: Special Songs View uses: .special_table (container) and .special-delete-btn (button)
        // We assume deleteSong.php handles the actual deletion based on 'sid'.
        setupDeleteLogic('.special_table', '.special-delete-btn',
            'http://localhost/SIngIT/flutter_crud/deleteSong.php', extractSongId, 'Special Song');

    });
</script>
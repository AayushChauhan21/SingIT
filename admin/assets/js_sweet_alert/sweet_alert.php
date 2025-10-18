<script>
    // --- SweetAlert Helper Functions (જેમ છે તેમ) ---
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

    // --- 1. Song ID કાઢવા માટેનું ફંક્શન (જેમ છે તેમ) ---
    function extractSongId(url) {
        var urlParams = new URLSearchParams(url.split('?')[1]);
        return urlParams.get('sid') || 0;
    }

    // --- 2. Artist ID કાઢવા માટેનું નવું ફંક્શન ---
    function extractArtistId(url) {
        var urlParams = new URLSearchParams(url.split('?')[1]);
        // 'arid' (Artist ID) ને કાઢવા માટે
        return urlParams.get('arid') || 0;
    }

    function extractGenreId(url) {
        var urlParams = new URLSearchParams(url.split('?')[1]);
        // 'gid' (Genre ID) ને કાઢવા માટે
        return urlParams.get('gid') || 0;
    }

    function extractLanguageId(url) {
        var urlParams = new URLSearchParams(url.split('?')[1]);
        // 'lid' (Language ID) ને કાઢવા માટે
        return urlParams.get('lid') || 0;
    }


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
        // --- A) SONG DELETE LOGIC (Existing Logic) ---
        // ------------------------------------------------------------------
        // Home page પરના Songs Table માંથી Delete બટન ક્લિક થવા પર

        $('.song_table').on('click', '.song-delete-btn', function (e) {
            e.preventDefault();

            var deleteUrl = $(this).attr('href');
            var sid_value = extractSongId(deleteUrl);

            if (!sid_value) {
                showErrorAlert("Song ID not found in the link.");
                return;
            }

            // Main Confirmation Pop-up
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this song!",
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
                            text: "Please wait while we delete the song.",
                            type: "info",
                            showConfirmButton: false,
                        });

                        // AJAX Call to deleteSong.php
                        $.ajax({
                            url: 'http://localhost/SIngIT/flutter_crud/deleteSong.php', // Song Delete API
                            type: 'POST',
                            data: {
                                sid: sid_value
                            },
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
                                showErrorAlert("Server error or connection failed.");
                            }
                        });

                    } else {
                        swal({
                            title: "Cancelled",
                            text: "Your song is safe :)",
                            type: "error",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
        });


        // ------------------------------------------------------------------
        // --- B) ARTIST DELETE LOGIC (NEW LOGIC) ---
        // ------------------------------------------------------------------
        // આ લોજિક Artist Table માટે છે. તમારે Artist Table ના div/container ને 
        // એક ID આપવું પડશે (દા.ત., '#artistTable') અને delete બટનને 
        // '.artist-delete-btn' class આપવો પડશે.

        // **ઉદાહરણ:** જો તમારા Artist Table ને 'artistTable' ID આપેલું હોય.

        $('.artist_table').on('click', '.artist-delete-btn', function (e) {
            e.preventDefault();

            var deleteUrl = $(this).attr('href');
            var arid_value = extractArtistId(deleteUrl); // Artist ID કાઢો

            // ... બાકીનું Confirmation અને AJAX લોજિક જેમ છે તેમ જાળવી રાખો ...
            if (!arid_value) {
                showErrorAlert("Artist ID not found in the link.");
                return;
            }

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this artist!",
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
                            text: "Please wait while we delete the artist.",
                            type: "info",
                            showConfirmButton: false,
                        });

                        // AJAX Call to deleteArtist.php
                        $.ajax({
                            url: 'http://localhost/SIngIT/flutter_crud/deleteArtist.php',
                            type: 'POST',
                            data: {
                                arid: arid_value
                            },
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
                                showErrorAlert(
                                    "Server error or connection failed for Artist deletion."
                                );
                            }
                        });

                    } else {
                        swal({
                            title: "Cancelled",
                            text: "Your artist is safe :)",
                            type: "error",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
        });

        $('.genre_table').on('click', '.genre-delete-btn', function (e) {
            e.preventDefault();

            var deleteUrl = $(this).attr('href');
            var gid_value = extractGenreId(deleteUrl); // Genre ID કાઢો
            var entityName = 'Genre';

            if (!gid_value) {
                showErrorAlert(entityName + " ID not found in the link.");
                return;
            }

            // Main Confirmation Pop-up: આનાથી SweetAlert દેખાશે.
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
                            showConfirmButton: false
                        });

                        $.ajax({
                            url: 'http://localhost/SIngIT/flutter_crud/deleteGenre.php',
                            type: 'POST',
                            data: {
                                gid: gid_value
                            },
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
                                showErrorAlert(
                                    "Server error or connection failed for Genre deletion."
                                );
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

        $('.language_table').on('click', '.language-delete-btn', function (e) {
            e.preventDefault();

            var deleteUrl = $(this).attr('href');
            var lid_value = extractLanguageId(deleteUrl); // Language ID કાઢો
            var entityName = 'Language';

            if (!lid_value) {
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

                        // AJAX Call to deleteLanguage.php
                        $.ajax({
                            url: 'http://localhost/SIngIT/flutter_crud/deleteLanguage.php', // નવો API URL
                            type: 'POST',
                            data: {
                                lid: lid_value // Language ID ('lid') મોકલો
                            },
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
                                showErrorAlert(
                                    "Server error or connection failed for Language deletion."
                                );
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


        // --- Original Warning Alert (Sample) - આ ફક્ત એક ઉદાહરણ માટે છે, તમે તેને દૂર કરી શકો છો ---
        $('#swal-warning').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
                function (isConfirm) {
                    if (isConfirm) {
                        swal({
                            title: "Deleted!",
                            text: "Your imaginary file has been deleted.",
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            type: "error",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
        });
    });
</script>
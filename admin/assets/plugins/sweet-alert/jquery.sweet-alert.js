$(function (e) {

	//Basic
	$('#swal-basic').on('click', function () {
		swal('Welcome to Your Admin Page')
	});

	//A title with a text under
	$('#swal-title').click(function () {
		swal(
			{
				title: 'Here is  a title!',
				text: 'All are available in the template',
			}
		)
	});

	//Success Message
	$('#swal-success').click(function () {
		swal(
			{
				title: 'Well done!',
				text: 'You clicked the button!',
				type: 'success',
				confirmButtonColor: '#57a94f'
			}
		)
	});


	//Warning Message
	// $('#swal-warning').click(function () {
	// 	swal({
	// 		title: "Are you sure?",
	// 		text: "",
	// 		type: "warning",
	// 		showCancelButton: true,
	// 		confirmButtonClass: "btn btn-danger",
	// 		confirmButtonText: "Yes, delete it!",
	// 		closeOnConfirm: false
	// 	},
	// 		function () {
	// 			swal("Deleted!", "Your imaginary file has been deleted.", "success");
	// 		});
	// });

	// popup alert mate che
	// $('#exportexample').on('click', '.song-delete-btn', function (e) {
	// 	e.preventDefault();
	// 	var deleteUrl = $(this).attr('href');
	// 	var sid = deleteUrl.split('sid=')[1];

	// 	// Main Confirmation Pop-up
	// 	swal({
	// 		title: "Are you sure?",
	// 		text: "You will not be able to recover this song!",
	// 		type: "warning",
	// 		showCancelButton: true,
	// 		confirmButtonClass: "btn btn-danger",
	// 		confirmButtonText: "Yes, delete it!",
	// 		cancelButtonText: "No, cancel plx!",
	// 		closeOnConfirm: false,
	// 		closeOnCancel: false // Ise FALSE rehne dete hain!
	// 	},
	// 		function (isConfirm) {
	// 			if (isConfirm) {
	// 				// CONFIRM LOGIC: AJAX Call shuru karo

	// 				// 1. Loading state SweetAlert dikhao
	// 				swal({
	// 					title: "Deleting...",
	// 					text: "Please wait while we delete the song.",
	// 					type: "info",
	// 					showConfirmButton: false,
	// 				});

	// 				// 2. AJAX (API Call) se delete.php API ko call karo
	// 				$.ajax({
	// 					url: 'http://localhost/SIngIT/flutter_crud/deleteSong.php',
	// 					type: 'POST',
	// 					data: { sid: sid },
	// 					dataType: 'json',
	// 					success: function (response) {
	// 						if (response.status === 'success') {
	// 							// Success message dikhao
	// 							swal({
	// 								title: "Deleted!",
	// 								text: response.message,
	// 								type: "success",
	// 								showConfirmButton: false,
	// 								timer: 2000 // 2 second delay
	// 							});

	// 							// 2 second baad page ko reload karo
	// 							setTimeout(function () {
	// 								window.location.reload();
	// 							}, 2000);

	// 						} else {
	// 							// Deletion failed
	// 							swal({
	// 								title: "Failed",
	// 								text: response.message,
	// 								type: "error",
	// 								showConfirmButton: true,
	// 							});
	// 						}
	// 					},
	// 					error: function (xhr, status, error) {
	// 						// Network ya server side error
	// 						swal({
	// 							title: "Error!",
	// 							text: "Server error or connection failed.",
	// 							type: "error",
	// 							showConfirmButton: true,
	// 						});
	// 					}
	// 				});

	// 			} else {
	// 				// CANCEL LOGIC: Show 'Cancelled' message (2s delay ke baad close)

	// 				// Hum yahan koi extra delay nahi daal rahe hain kyunki aapne closeOnCancel: false rakha hai.
	// 				// Aur yeh naya swal call automatically pehle wale ko replace kar dega.
	// 				swal({
	// 					title: "Cancelled",
	// 					text: "Your song is safe :)",
	// 					type: "error",
	// 					showConfirmButton: false,
	// 					timer: 2000 // <-- Yeh 2 second tak rukega
	// 				});
	// 			}
	// 		});
	// });

	// // --- Original Warning Alert (Separate button ke liye) ---
	// $('#swal-warning').click(function () {
	// 	swal({
	// 		title: "Are you sure?",
	// 		text: "You will not be able to recover this imaginary file!",
	// 		type: "warning",
	// 		showCancelButton: true,
	// 		confirmButtonClass: "btn-danger",
	// 		confirmButtonText: "Yes, delete it!",
	// 		cancelButtonText: "No, cancel plx!",
	// 		closeOnConfirm: false,
	// 		closeOnCancel: false // Ise FALSE rakha hai
	// 	},
	// 		function (isConfirm) {
	// 			if (isConfirm) {
	// 				swal({
	// 					title: "Deleted!",
	// 					text: "Your imaginary file has been deleted.",
	// 					type: "success",
	// 					showConfirmButton: false,
	// 					timer: 2000
	// 				});
	// 			} else {
	// 				swal({
	// 					title: "Cancelled",
	// 					text: "Your imaginary file is safe :)",
	// 					type: "error",
	// 					showConfirmButton: false,
	// 					timer: 2000
	// 				});
	// 			}
	// 		});
	// });


	//Parameter
	$('#swal-parameter').click(function () {
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
					swal("Deleted!", "Your imaginary file has been deleted.", "success");
				} else {
					swal("Cancelled", "Your imaginary file is safe :)", "error");
				}
			});
	});

	//Custom Image
	$('#swal-image').click(function () {
		swal({
			title: 'Lovely!',
			text: 'your image is uploaded.',
			imageUrl: '../assets/img/brand/logo.png',
			animation: false
		})
	});

	//Auto Close Timer
	$('#swal-timer').click(function () {
		swal({
			title: 'Auto close alert!',
			text: 'I will close in 1 seconds.',
			timer: 1000
		})
		// .then(
		// function () {
		// },
		// handling the promise rejection
		// function (dismiss) {
		// 	if (dismiss === 'timer') {
		// 		console.log('I was closed by the timer')
		// 	}
		// }
		// )
	});


	//Ajax with Loader Alert
	$('#swal-ajax').click(function () {
		swal({
			title: "Ajax request example",
			text: "Submit to run ajax request",
			type: "info",
			showCancelButton: true,
			closeOnConfirm: false,
			showLoaderOnConfirm: true
		}, function () {
			setTimeout(function () {
				swal("Ajax request finished!");
			}, 2000);
		});
	});

});
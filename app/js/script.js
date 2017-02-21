$(document).ready(function() {
	// Initialize document
	$('select').material_select();
	$('.login-form-processing').hide();
	$('.loader').hide();
	// Materialize initial
	$('.datepicker').pickadate({
    	selectMonths: true, // Creates a dropdown to control month
    	selectYears: 15 // Creates a dropdown of 15 years to control year
  	});
  	$('.tooltipped').tooltip({delay: 50});
  	$('.materialize-textarea').trigger('autoresize');
  	// Initialize forms for profile and event-edit.
  	$('#edit-event-form :input').hide();
  	$('.input-field-event-edit').hide();
  	$('#input-field-edit-event-btn').hide();
  	$('#save-event-detail').hide();
  	$('#edit-event-detail').show();
  	$('#delete-event-detail').show();  	
  	$('#save-profile-detail').hide();
  	$('.input-field-profile-edit').hide();

  	// Fired when the user submits the login-form.
	$('#login-form').submit(function(e) {
		showLoading();
		params = $('.login-input').serialize();
		$.ajax({
			url: "php/login.php",
			type: "POST",
			data: {
				action: 'login',
				data: params,
				},
				success: function(data)
				{
					data = jQuery.parseJSON(data);
					// Clear
					$('.state-message#state-login').removeClass('success');
					$('.state-message#state-login').removeClass('error');
					// Add Error / Success classes
					if(data['success']) {
						hideLoading(data['message']);
						$('.login').fadeOut('300');
						location.reload();
					}
					else {
						hideLoading();
						$('.state-message').addClass('error').text(data['message']);
					}
				},
				error: function(data)
				{
					console.log(data);
				}
			});
		return false;
	});

	// Fired when the user submits the register-form.
	$('.register-form').submit(function(e) {
		// Check if the passwords match
		$pw1 = $('#passwordOne').val();
		$pw2 = $('#passwordTwo').val();
		if($pw1 != $pw2 && $pw1 == "")
		{
			alert('Kennwörter sind nicht identisch!');
			return;
		}
		showLoader('Registrieren');
		$('#close-loader-overlay').hide();
		params = $('.register-input').serialize();
		console.log(params);
		$.ajax({
			url: 'php/modify.php',
			type: "POST",
			data: {
				action: 'saveProfile',
				data: params,
				},
				success: function(data) 
				{
					data = jQuery.parseJSON(data);
					$('#state-loader').removeClass('success');
					$('#state-loader').removeClass('error');
					if(data['success']) 
					{
						$('#close-loader-overlay').fadeIn('300');
						$('.loader-processing').fadeOut('300');
						$('.state-message').addClass('success').text(data['message']);
					}
					else
					{
						$('#close-loader-overlay').fadeIn('300');
						$('.loader-processing').fadeOut('300');
						$('.state-message').addClass('error').text(data['message']);
					}
				},
				error: function(data)
				{
					console.log(data);
				}
			});
		return false;
		});

	// Fired when the user clicks the log-out button on the nav.
	$('.nav-logout').on('click', function(e) {
		showLoader("Ausloggen");
		e.preventDefault();
		$.ajax({
			url: "php/logout.php",
			type: "POST",
			data: {
				action: 'logout'
			},
			success: function(data)
			{
				data = jQuery.parseJSON(data);
				$('#state-loader').removeClass('success');
				$('#state-loader').removeClass('error');
				$('#close-loader-overlay').fadeIn('300');
				$('.loader-processing').fadeOut('300');
				if(data['success'])
				{
					$('.state-message').addClass('success redirectToStart').text(data['message']);
				}
				else
				{
					$('.state-message').addClass('error').text(data['message']);
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		})
	});	

	// Fired when the user submits the "new-event" form.
	$('#new-event-form').submit(function(e) {
		var msg ="";
		var success = false;
		showLoader("Veranstaltung erstellen");
		// Upload all text-data
		params = $('.new-event-input').serialize();
		var selectValues = $('#type').val();
		var image_path = $('#image-path').val();
		params = params + "&image-path=" + image_path;
		params = params + "&user-id=" + $('#user').attr('user-id');
		var type ="";
		$.each(selectValues, function(index, value) {
			type = type + "," + value;
		});
		params = params + "&type=" + type;
		$.ajax({
			url: 'php/modify.php',
			type: 'POST',
			data: {
				action: 'saveEvent',
				data: params
			},
			success: function(data)
			{
				data = jQuery.parseJSON(data);
				console.log(data);
				if(data['success'])
				{
					success = true;
					msg = data['message'];
					// Upload the Image
					var file_data = $('#image-to-upload').prop("files")[0];
					var form_data = new FormData();
					form_data.append('file', file_data);
					$.ajax({
						url: 'php/image_upload.php',
						type: "POST",
						data: form_data,
						contentType: false,
						cache: false,
						processData: false,
						success: function(data)
						{
							$('#state-loader').removeClass('success');
							$('#state-loader').removeClass('error');
							$('#close-loader-overlay').fadeIn('300');
							$('.loader-processing').fadeOut('300');
							if(success)
							{
								$('.state-message').addClass('success').text(msg);
								$('#new-event-form')[0].reset();
							}
						},
						error: function(data)
						{
							$('.state-message').addClass('error').text(data["message"]);
							$('#new-event-form')[0].reset();
							return false;
						}
					});
				}
				else
				{
					$('.state-message').addClass('error').text(data['message']);
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
	return false;
	});

	// Fires when the user clicks on the "Edit-Event-Button"
	$('.edit-event').click(function() {
		showLoader("Veranstaltung öffnen");
		var eventId = $(this).attr('event-id');
		var url = 'index.php?seite=edit-event.php';
		var form = $('<form action="' + url + '" method="post">' + '<input type="text" name="event-id" value="' + eventId + '" /></form>');
		$('body').append(form);
		form.submit();
	});

	// Fires when the user wants to edit specific event details.
	$("#edit-event-detail").click(function() {
		// Hide all Information fields
		$('.information-field').hide();
		// Show the form fields
		$('#input-field-edit-event-btn').show();
		$('#edit-event-form :input').show();
  		$('.input-field-event-edit').show();
  		$('.new-event-multiple-input select').hide();
		// Replace the Edit-Button with the Save-Button
		$('#edit-event-detail').hide();
		$('#save-event-detail').show();
	});

	// Fires when the user wants to edit the profile details.
	$('#edit-profile-detail').click(function() {
		// Hide all Information fields
		$('.information-field').hide();
		// Show the form fields
		  $('.input-field-profile-edit').show();
  		// Replace the Edit-button with the Save-Button
  		$('#save-profile-detail').show();
  		$('#edit-profile-detail').hide();
	});

	// Fires when the user submits the edit-event-form. This is the case when he modifies the event.
	$('#edit-event-form').submit(function() {
		var success = false;
		var msg = "";
		showLoader("Veranstaltung speichern");
		// Upload all text data.
		var id = $('#event-data').attr('event-id');
		params = $('#edit-event-form').serialize();
		params = params + "&id=" + id;
		var selectValues = $('#edit-type').val();
		var type ="";
		$.each(selectValues, function(index, value) {
			type = type + "," + value;
		});
		params = params + "&type=" + type;
		$.ajax({
			url: 'php/modify.php',
			type: 'POST',
			data: {
				action: 'saveEvent',
				data: params
			},
			success: function(data)
			{
				data = jQuery.parseJSON(data);
				$('#state-loader').removeClass('success');
				$('#state-loader').removeClass('error');
				if(data['success'])
				{
					success = true;
					msg = data['message'];
					// Upload Image
					var file_data = $('#image-to-upload').prop("files")[0];
					var form_data = new FormData();
					form_data.append('file', file_data);
					$.ajax({
						url: 'php/image_upload.php',
						type: "POST",
						data: form_data,
						contentType: false,
						cache: false,
						processData: false,
						success: function(data)
						{
							$('#close-loader-overlay').fadeIn('300');
							$('.loader-processing').fadeOut('300');
							if(success)
							{
								$('.state-message').addClass('success redirectToOverview').text(msg);
							}
							else
							{
								$('.state-message').addClass('error redirectToOverview').text(msg);
								console.log(data);
							}
						},
						error: function(data)
						{
							success = false;
							message = data;
						}
					});
				}
				else
				{
					success = false;
					msg = data['message'];
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
		return false;
	});

	// Fires when the user submits the edit-profile-form. This is the case when he edits his profile.
	$('#edit-profile-form').submit(function() {
		showLoader("Änderungen speichern");
		var params = $('#edit-profile-form').serialize();
		var userId = $('#save-profile-detail').attr('profile-id');
		params = params + "&user-id=" + userId;
		$.ajax({
			url: 'php/modify.php',
			type: 'POST',
			data: {
				action: 'saveProfile',
				data: params
			},
			success: function(data)
			{
				data = jQuery.parseJSON(data);
				$('#state-loader').removeClass('success');
				$('#state-loader').removeClass('error');
				$('#close-loader-overlay').fadeIn('300');
				$('.loader-processing').fadeOut('300');
				if(data['success'])
				{
					$('.state-message').addClass('success redirectToProfile').text(data['message']);
				}
				else
				{
					$('.state-message').addClass('error').text(data['message']);
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
		return false;
	});

	// Fires when the user clicks on the go-to-events button on the profile view. Redirect's the user on the event-overview.
	$('#go-to-events').click(function() {
		showLoader("Veranstaltungen öffnen");
		window.location = '?seite=overview.php';
		loadContent('./pages/overview.php', 'content');
	});

	// Fires when the user clicks the delete-event-detail button. This is the case when he wants to delete a event on the event-detail-view.
	$('#delete-event-detail').click(function() {
		showLoader("Veranstaltung auflösen");
		var id = "id=" + $('#event-data').attr('event-id');
		$.ajax({
			url: 'php/modify.php',
			type: 'POST',
			data: {
				action: 'deleteEvent',
				data: id
			},
			success: function(data)
			{
				data = jQuery.parseJSON(data);
				console.log(data);
				$('#state-loader').removeClass('success');
				$('#state-loader').removeClass('error');
				$('#close-loader-overlay').fadeIn('300');
				$('.loader-processing').fadeOut('300');
				if(data['success'])
				{
					$('.state-message').addClass('success redirectToOverview').text(data['message']);
				}
				else
				{
					$('.state-message').addClass('error').text(data['message']);
				}
			},
			error: function(data)
			{
				console.log(data);
			}
		});
	});

	// Fires when the user clicks on the buy-event button on the home-page. User gets redirectet on the buy-view for the specific event.
	$('.buy-event').click(function(e) {
		var eventId = $(this).attr('event-info-id');
		showLoader("Veranstaltung öffnen");
		var url = 'index.php?seite=buy-ticket.php';
		var form = $('<form action="' + url + '" method="post">' + '<input type="text" name="event-id" value="' + eventId + '" /></form>');
		$('body').append(form);
		form.submit();
	});

	// Closes the loader-overlay. Redirects the user to a page specified in the .state-message class.
	$('#close-loader-overlay').click(function() {
		hideLoader();
		if($('.state-message').hasClass('redirectToOverview'))
		{
			window.location = '?seite=overview.php';
			loadContent('./pages/overview.php', 'content');
		}
		else if($('.state-message').hasClass('redirectToProfile'))
		{
			window.location = '?seite=profile.php';
			loadContent('./pages/profile.php', 'content');
		}
		else if($('.state-message').hasClass('redirectToStart'))
		{
			window.location = '?seite=home.php';
			loadContent('./pages/home.php', 'content');
		}
		return false;
	});

	// Fires when the user clicks on the #close-overlay icon on the login form. Closes the login-form.
	$('#close-overlay').click(function()
	{
		$('.login').fadeOut('300');
	});

	// Fires when the user clicks on the #nav-login button. Opens the login-form.
	$('.nav-login').click(function()
	{
		$('.login').fadeIn('300');
	});

	// Shows a loader-overlay with a text.
	function showLoader(text)
	{
		$('#loader-text').text(text);
		$('.loader').fadeIn('300');
	}

	// Hides the loader-overlay.
	function hideLoader()
	{
		$('.loader').fadeOut('300');
	}

	// Shows the loading-bar.
	function showLoading()
	{
		$('.login-form-content').fadeOut('300');
		$('.login-form-processing').fadeIn('300');
	}

	// Hides the loading-bar.
	function hideLoading()
	{
		$('.login-form-processing').fadeOut('300');
		$('.login-form-content').fadeIn('300');
	}

	// Loads the content from a site urlVal into a div divVal
	// Code copied from T:\ÜK-Kurse\Modul 133 Web Applikation realisieren\Beispiele\jquerry.php
	function loadContent(urlVal, divVal) {
        $.ajax({
            url: urlVal,
            type: 'post',
            dataType: 'text',
            async: true,
            success: function(response) {

                document.getElementById(divVal).innerHTML = response;
            }
        });
    }
});
jQuery(document).ready(function($){

    // SIGNUP
    $('#signupForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'custom_signup',
                first_name: $('input[name=first_name]').val(),
                last_name: $('input[name=last_name]').val(),
                email: $('input[name=email]').val(),
                password: $('input[name=password]').val()
            },
            success: function(response) {
				if (response.success) {
					// Use the redirect URL sent from the server
					window.location.href = response.data.redirect; 
				} else {
					// If it's an error, response.data usually contains the error message string
					$('#signupForm .response').html(response.data);
				}
			}
        });
    });

    // LOGIN
    $('#loginForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'custom_login',
                username: $('#loginForm input[name=username]').val(),
                password: $('#loginForm input[name=password]').val()
            },
            success: function(response) {
			// If backend sends success, use the dynamic redirect URL
			if (response.success) {
				window.location.href = response.data.redirect; 
			} else {
				// Show error message (e.g., "Invalid username or password")
				$('#loginForm .response').html(response.data);
			}
		}

        });
    });
	

});

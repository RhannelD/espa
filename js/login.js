$(document).ready(function(){
	removeInvalidClassOnType();
	showingNotShowingPassword();
	sign_up();

	// Login User
	$('#login-user').submit(function( e ) {
		e.preventDefault();
		var username = $(".c_username").val().trim();
		var password = $(".c_password").val().trim();
		
		$(".signin_loading").collapse('show');

		$.post( "../login/login-verify.php", {
			username: username,
			password: password
		}, function( data ) {
			if(data.alert == "success"){
				window.location.href = data.panel;
				return;
			}
			if (data.hasOwnProperty('error')) {
  				$('.c_'+data.error).addClass('is-invalid');
  			}
  			if (data.attempt > 2){
  				suggestForgotPassword();
  				return
  			}
	  		swal(data.title, data.message, data.alert).then(function() {
			    $(".signin_loading").collapse('hide');
			});
	  		$(".signin_loading").collapse('hide');
		}, "json");
    });

	// Load Forgot Password panel if enter Username is found, Load Finding Account Panel if account is not found
	$('.btn_forgot_password').click(function(){
		var username = $(".c_username").val().trim();

		$('.forgot_password_body').load('../login/login_find_user.php', function(){
			findAccount();
		});
	});

    function suggestForgotPassword(){
    	swal({
            title: 'Forgot Password?',
            text: "Do you want to recover you account?",
            icon: 'error',
            buttons:{
                confirm: {
                    text : 'Recover',
                    className : 'btn btn-success'
                },
                cancel: {
                    visible: true,
                    className: 'btn btn-info'
                }
            }	
        }).then((Recover) => {
            if (Recover) {
  				$('.btn_forgot_password').trigger('click');
            }
            $(".signin_loading").collapse('hide');
        });
    }

	// Trigger on Finding the Account from enter Username/ID
	function findAccount(){
		$('#find_user_form').submit(function( e ) {
			e.preventDefault();
			var username = $(".c_username_find").val().trim();
			
			$.post( "../login/login_finding_user.php", {
				username: username
			}, function( data ) {
				if(data.alert == "success"){
					loadForgotPassword(data.id, data.username);
					return;
				}
				swal(data.title, data.message, data.alert);
			}, "json");
	    });
	}

	// Load the Forgot Password panel if User account has been found
	function loadForgotPassword(id, username){
		$('.forgot_password_body').load('../login/login_forgot_password.php', {
			id: id,
			username: username
		}, function(){
			forgotPasswordSendEmailCode(id);
			forgotPasswordResendEmailCode(id);
			forgotPasswordValidate(id);
		});
	}

	function forgotPasswordSendEmailCode(id) {
		$.post( "../login/login_forgot_password_send_email.php", {
			id: id
		});
	}

	function forgotPasswordResendEmailCode(id){
		$('.btn_resend_code').click(function(){
			forgotPasswordSendEmailCode(id);
		});
	}

	// Validating answer then load the changed password panel
	function forgotPasswordValidate(id){
		$('#forgot_password_form').submit(function( e ) {
			e.preventDefault();
			var code = $(".forgot_password_code").val().trim();
			
			$.post( "../login/login_forgot_password_verify.php", {
				id: 	id,
				code: 	code
			}, function( data ) {
				if(data.alert == "success"){
					loadChangePassword(id);
					return;
				}
				swal(data.title, data.message, data.alert);
			}, "json");
	    });
	}

	// Load the changed password panel
	function loadChangePassword(id){
		$('.forgot_password_body').load('../login/login_change_password.php', {
			id: id
		}, function(){
			changePassword(id);
			removeInvalidClassOnType();
			showingNotShowingPassword();
		});
	}

	// Changing/ updating the password
	function changePassword(id){
		$('#change_password_form').submit(function( e ) {
			e.preventDefault();
			var new_password = $(".change_password_new").val().trim();
			var retype_password = $(".change_password_retype").val().trim();
			
			$.post( "../login/login_change_password_save.php", {
				id: id,
				new_password: new_password,
				retype_password: retype_password
			}, function( data ) {
				if(data.alert == "success"){
					window.location.href = data.panel;
					return;
				}
				if (data.hasOwnProperty('error')) {
	  				$(data.error).addClass('is-invalid');
	  			}
				swal(data.title, data.message, data.alert);
			}, "json");
	    });
	}

	// remove invalid class on new and retype password inputs
	function removeInvalidClassOnType(){
		$('.change_password_new, .change_password_retype, .c_password').keypress(function(e){
			$(this).removeClass('is-invalid');
		});
	}

	function showingNotShowingPassword(){
		$(".show_password").on('click', function(event) {
	        event.preventDefault();
	        if($(this).parent().siblings('input').attr("type") == "password"){
	            $(this).parent().siblings('input').attr('type', 'text');
	            $(this).children('i').removeClass( "fa-eye-slash" );
	            $(this).children('i').addClass( "fa-eye" );
	            return;
	        }
	        $(this).parent().siblings('input').attr('type', 'password');
	        $(this).children('i').addClass( "fa-eye-slash" );
	        $(this).children('i').removeClass( "fa-eye" );
	    });
	}  

	function sign_up(){
		$('.btn_sign_up').click(function(){
			loadSignUp();
		});
	}
});
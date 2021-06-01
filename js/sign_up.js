function loadSignUp(){
	$(".sign_up_body").load("../sign_up/sign_up.php", function(){
		showingNotShowingPassword();
		loadSelectProgram();
		loadSelectTrack();
		signupStudentConfirmation();
		removeInvalidClassOnType();
		verifyPasswords();
	});
	
	// Showing and unshowing password
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

	// Verifying the two entered password when focusout
	function verifyPasswords(){
		$('.c_student_password, .c_student_confirm_password').focusout(function(e){
			var password 			= $('.c_student_password').val().trim();
			var confirm_password 	= $('.c_student_confirm_password').val().trim();

			if (password.length == 0 || confirm_password.length == 0){
				return;
			}
			if (password!=confirm_password){
				$('.c_student_password, .c_student_confirm_password').addClass('is-invalid');
				return;
			}

			$('.c_student_password, .c_student_confirm_password').removeClass('is-invalid');
		});
	} 

	// loading/adding the academic year for selection
	function loadSelectProgram(){
		$(".c_student_program").on('change' , function(){
			var id = $(".c_student_program").val().trim();
			$(".c_student_academic_year").load("../Tab_Student/student_select_academic_year.php", {
				id: id
			});
		});
	}

	// loading/adding the academic year for selection
	function loadSelectTrack(){
		$(".c_student_academic_year, .c_student_program").on('change' , function(){
			var id = $(".c_student_program").val().trim();
			var year = $(".c_student_academic_year").val().trim();
			$(".c_student_track").load("../Tab_Student/student_select_track.php", {
				id: id,
				year: year
			});
		});
	}

	// remove invalid class on new and retype password inputs
	function removeInvalidClassOnType(){
		$('input').keypress(function(e){
			$(this).removeClass('is-invalid');
		});
	}

	// Confirmation before Sign Up
	function signupStudentConfirmation(){
		$( "#formStudentSignUp" ).submit(function( e ) {
			e.preventDefault();

			var password 			= $('.c_student_password').val().trim();
			var confirm_password 	= $('.c_student_confirm_password').val().trim();

			if (password!=confirm_password){
				swal('Passwords not match!', 'Password and Confirm Password does not match', 'error');
				$('.c_student_password, .c_student_confirm_password').addClass('is-invalid');
				return;
			}

			swal({
	            title: 'Sign-up Account?',
	            text: "",
	            icon: 'info',
	            buttons:{
	                confirm: {
	                    text : 'Sign-up',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Create) => {
	        	if (Create)
	        		signingUpStudent();
	        });
	    });
	}

	// Signing up
	function signingUpStudent(){
		var sr_code 	= $('.c_student_sr_code').val().trim();
		var firstname 	= $('.c_student_firstname').val().trim();
		var lastname 	= $('.c_student_lastname').val().trim();
		var email	 	= $('.c_student_email').val().trim();
		var password 	= $('.c_student_password').val().trim();
		var c_password 	= $('.c_student_confirm_password').val().trim();
		var gender 		= $('.c_student_gender:checked').val().trim();
		var program 	= $('.c_student_program').val().trim();
		var year 		= $('.c_student_academic_year').val().trim();
		var track 		= $('.c_student_track').val().trim();

		$.post( "../sign_up/sign_up_save.php", {
			sr_code: 	sr_code,
			firstname: 	firstname,
			lastname: 	lastname,
			gender: 	gender,
			email: 		email,
			password: 	password,
			c_password: c_password,
			program: 	program,
			year: year,
			track: track
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_student_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}
}

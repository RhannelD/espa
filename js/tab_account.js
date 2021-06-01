function loadAccount(){
	$("#mainpanel").load("../Tab_Account/account.php", function(){
		$('.btn_cancel').hide();
		$('.btn_save').hide();

		var fname = $('.c_account_firstname').val().trim();
		var lname = $('.c_account_lastname').val().trim();
		var gender = $('.c_account_gender:checked').val().trim();

		editAccountClicked();
		editAccountClose(fname, lname, gender);
		editingAccountConfirmation();
		openChangePassword();
		openChangeRecovery();
		editingPasswordConfirmation();
		removeInvalidClassOnType();
		showingNotShowingPassword();
	});

	// Editing the Account Info open
	function editAccountClicked(){
		$('.btn_edit').click(function(){
			$('.btn_cancel').show();
			$('.btn_save').show();
			$('.btn_edit').hide();
			$('.btn_change_password').hide();
			$(".form_change_password").collapse('hide');
			$(".form_change_recovery").collapse('hide');

			$('.c_account_firstname').attr('disabled', false);
			$('.c_account_lastname').attr('disabled', false);
			$('.c_account_gender').attr('disabled', false);
		});
	}

	// Editing the Account Info closing
	function editAccountClose(fname, lname, gender){
		$('.btn_cancel').click(function(){
			$('.c_account_firstname').val(fname);
			$('.c_account_lastname').val(lname);
			switch (gender) {
				case 'male':
					$('input#male').click();
					break;
				default:
					$('input#female').click();
					break;
			}

			$('.btn_cancel').hide();
			$('.btn_save').hide();
			$('.btn_edit').show();
			$('.btn_change_password').show();

			$('.c_account_firstname').attr('disabled', true);
			$('.c_account_lastname').attr('disabled', true);
			$('.c_account_gender').attr('disabled', true);
		});
	}

	// Confirmation before Updating
	function editingAccountConfirmation(){
		$( "#formAccountEditing" ).submit(function( e ) {
			e.preventDefault();

			swal({
	            title: 'Update Account?',
	            text: "Updating Account Info",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Save',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Save) => {
	        	if (Save)
	        		editingAccount();
	        });
	    });
	}

	// Updating Account
	function editingAccount(){	
		var id 		= $('.c_account_id').val().trim();
		var fname 	= $('.c_account_firstname').val().trim();
		var lname 	= $('.c_account_lastname').val().trim();
		var gender 	= $('.c_account_gender:checked').val().trim();

	  	$.post( "../Tab_Account/account_edit_save.php", {
	  		id: id,
			firstname: fname,
			lastname: lname,
			gender: gender
		}, function( data ) {
	        	swal(data.title, data.message, data.alert);
				if(data.alert=="success"){
					editAccountClose(data.data['firstname'], data.data['lastname'], data.data['gender']);
					changeAccountNameDisplay((data.data['firstname']+' '+data.data['lastname']));
	  			}
	  			$('.btn_cancel').click();
		}, "json");
	}

	// Blanking the change password panel
	function openChangePassword() {
		$(".form_change_password").on("show.bs.collapse", function(){
		    $('.change_password').val('');
		    $('.change_password_new').val('');
		    $('.change_password_retype').val('');

		    $(".form_change_recovery").collapse('hide');
		});
	}

	// Blanking the change password panel
	function openChangeRecovery() {
		$(".form_change_recovery").on("show.bs.collapse", function(){
		    $('.recovery_answer').val('');
		    $('.recovery_password').val('');

		    $(".form_change_password").collapse('hide');

		    $(".recovery_question").load("../Tab_Account/account_load_question.php");
		});
	}

	// Confirmation before Updating Password
	function editingPasswordConfirmation(){
		$( "#change_password_form" ).submit(function( e ) {
			e.preventDefault();

			swal({
	            title: 'Update Password?',
	            text: "Updating Account Password",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Save',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Save) => {
	        	if (Save)
	        		editingPassword();
	        });
	    });
	}

	// Updating Account Password
	function editingPassword(){	
		var id 				= $('.c_account_id').val().trim();
		var password 		= $('.change_password').val().trim();
		var password_new 	= $('.change_password_new').val().trim();
		var password_retype = $('.change_password_retype').val().trim();

	  	$.post( "../Tab_Account/account_change_password.php", {
	  		id: id,
			password: password,
			password_new: password_new,
			password_retype: password_retype
		}, function( data ) {
	        	swal(data.title, data.message, data.alert);
				if (data.hasOwnProperty('error')) {
	  				$(data.error).addClass('is-invalid');
	  			}
	  			if (data.alert=='success') {
	  				$('.change_password, .change_password_new, .change_password_retype').removeClass('is-invalid');
	  				$(".form_change_password").collapse('hide');
	  			}
		}, "json");
	}

	// remove invalid class on new and retype password inputs
	function removeInvalidClassOnType(){
		$('.change_password, .change_password_new, .change_password_retype, .recovery_password').keypress(function(e){
			$(this).removeClass('is-invalid');
		});
	}  
}

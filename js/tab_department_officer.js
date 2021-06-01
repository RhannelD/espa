function loadOfficers(){
	$("#mainpanel").load("../Tab_Department_Officer/officer.php", function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchOfficers(history.state.page);
		else										// regular search
			searchOfficers(); 

		if (history.state.id != undefined)			// reloaded page and see previous info
			officerInfo(history.state.id);

		loadCreatingOfficerForm();

		// Search trigger by Search Icon
		$(".officer_search_icon").click(function(){
			searchOfficers(); 
		});

		// Search trigger by Enter key
		$('.officer_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchOfficers(); 
		    }
		});
	});

	// replacing/storing the officer history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'officer';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'officer', '');
	}
	
	// replacing/storing the officer history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'officer';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'officer', '');
	}

	// Searching the Officers
	function searchOfficers(page = 1){
		historyReplacePage(page=page);
		var search = $(".officer_search_input").val().trim();
		$(".table_officer").load("../Tab_Department_Officer/officer_search.php", {
			"search": search, 
			"page": page
		}, function(){
			clickedOfficer();
			clickedOfficerPagination();
			$(".table_officer").collapse('show');
		});
	}

	// going the another officer page trigger
	function clickedOfficerPagination(){
		$(".officer_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchOfficers(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// Getting the info for viewing the selected/clicked officer
	function clickedOfficer(){
	    $(".officer_table tbody .rows").click(function(e){
	    	var id = $(this).attr('id').trim();
	    	officerInfo(id);
	    });	
	}

	// Viewing the info of the selected officer
	function officerInfo(id){
		$(".info_officer").load("../Tab_Department_Officer/officer_info.php", {
			id: id
		},function(){
			historyReplaceID(id=id);
			officerDeleteConfirmation();
			loadChangeOfficerPassword();
			loadEditingOfficerForm();
			loadChangeOfficerPosition();
			$(".info_officer").collapse('show');
		});
	}

	function unloadProgramInfo(){
		$(".info_officer").collapse('hide');
		historyReplaceID(id=0);
	}

	// Asking confirmation if sure on deleting the selected officer
	function officerDeleteConfirmation(){
		$(".officer_delete").click(function(e){	
			var name = $(".info_officer_name").text().trim();
			swal({
	            title: 'Are you sure?',
	            text: "Deleting Department Officer \""+ name +"\" profile",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Delete',
	                    className : 'btn btn-danger'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Delete) => {
	            if (Delete) {
					officerDelete();
	            }
	        });
		});
	}

	// Deleting the selected student
	function officerDelete(){
		$.post( "../Tab_Department_Officer/officer_delete.php", {
			id: history.state.id
		}, function( data ) {
			swal(data.title, data.message, data.alert);
			if(data.alert=="success"){
				$(".officer_table_row_"+history.state.id).remove();
				unloadProgramInfo();
			}
		}, "json");
	}

	// Opening the Officer Creating form
	function loadCreatingOfficerForm(){
		$(".create_officer_open_modal").click(function(){
			$(".officer_editing").empty();
			$(".officer_password").empty();
			$(".officer_position").empty();
			$(".officer_creating").load("../Tab_Department_Officer/officer_create.php", function(){
				showingNotShowingPassword();
				removeInvalidClassOnType();
				creationOfficerConfirmation();
			});
		});
	}	

	// Confirmation before Creation
	function creationOfficerConfirmation(){
		$( "#formOfficerCreation" ).submit(function( e ) {
			e.preventDefault();
			swal({
	            title: 'Create Department Officer?',
	            text: "Creating Department Officer Profile",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Create',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Create) => {
	        	if (Create)
	        		creatingOfficer();
	        });
	    });
	}

	// Creating the  profile
	function creatingOfficer(){	
		var firstname 	= $('.c_firstname').val().trim();
		var lastname 	= $('.c_lastname').val().trim();
		var email	 	= $('.c_email').val().trim();
		var gender 		= $('.c_gender:checked').val().trim();
		var department 	= $('.c_department').val().trim();
		var officer 	= $('.c_officer').val().trim();
		var password 	= $('.c_password').val().trim();
	  	
	  	$.post( "../Tab_Department_Officer/officer_create_save.php", {
			firstname: 	firstname,
			lastname: 	lastname,
			gender: 	gender,
			email: 		email,
			department: department,
			officer: 	officer,
			password: 	password
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".officer_pagination .active a").attr("id"));
					searchOfficers(page=page);
					officerInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Load the Changing password panel
	function loadChangeOfficerPassword(){
		$(".password_officer_open_modal").click(function(){
			$(".officer_creating").empty();
			$(".officer_editing").empty();
			$(".officer_position").empty();
			$(".officer_password").load("../Tab_Department_Officer/officer_change_password.php", {
				id: history.state.id
			}, function(){
				removeInvalidClassOnType();
				changeOfficerPasswordConfirmation();
				showingNotShowingPassword();
			});
		});
	}

	// Confirmation before Changing the Password
	function changeOfficerPasswordConfirmation(){
		$( "#formOfficerChangePassword" ).submit(function( e ) {
			e.preventDefault();

			swal({
	            title: 'Change Officer Password?',
	            text: "Changing Password for Department Officer",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Change Password',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Change) => {
	        	if (Change)
	        		changingOfficerPassword();
	        });
	    });
	}

	// Change Officer Password
	function changingOfficerPassword(){
		var password 	= $('.c_password').val().trim();

	  	$.post( "../Tab_Department_Officer/officer_change_password_save.php", {
	  		id: 	history.state.id,
	  		password: 	password
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Opening the Department Officer Editing form
	function loadEditingOfficerForm(){
		$(".edit_officer_open_modal").click(function(){
			$(".officer_creating").empty();
			$(".officer_password").empty();
			$(".officer_position").empty();
			$(".officer_editing").load("../Tab_Department_Officer/officer_edit.php", {
				id: history.state.id
			}, function(){
				removeInvalidClassOnType();
				editingOfficerConfirmation();
			});
		});
	}

	// Confirmation before Editing
	function editingOfficerConfirmation(){
		$( "#formOfficerEditing" ).submit(function( e ) {
			e.preventDefault();

			swal({
	            title: 'Update Officer Profile?',
	            text: "Updating Department Officer Profile",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Update',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Update) => {
	        	if (Update)
	        		editingOfficer();
	        });
	    });
	}

	// Editing the Department Officer
	function editingOfficer(){	
		var firstname 	= $('.c_firstname').val().trim();
		var lastname 	= $('.c_lastname').val().trim();
		var gender 		= $('.c_gender:checked').val().trim();
	  	
	  	$.post( "../Tab_Department_Officer/officer_edit_save.php", {
	  		id: 		history.state.id,
			firstname: 	firstname,
			lastname: 	lastname,
			gender: 	gender
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".officer_pagination .active a").attr("id"));
					searchOfficers(page=page);
					officerInfo(history.state.id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Load the Changing Position panel
	function loadChangeOfficerPosition(){
		$(".position_officer_open_modal").click(function(){
			$(".officer_creating").empty();
			$(".officer_editing").empty();
			$(".officer_password").empty();
			$(".officer_position").load("../Tab_Department_Officer/officer_position.php", {
				id: history.state.id
			}, function(){
				editingOfficerPositionConfirmation();
			});
		});
	}

	// Confirmation before Changing Position
	function editingOfficerPositionConfirmation(){
		$( "#formOfficerPosition" ).submit(function( e ) {
			e.preventDefault();

			swal({
	            title: 'Update Officer\'s Position?',
	            text: "Updating Department Officer's Position",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Update',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Update) => {
	        	if (Update)
	        		editingOfficerPosition();
	        });
	    });
	}

	// Editing the Department Officer's Position
	function editingOfficerPosition(){	
		var department 	= $('.c_department').val().trim();
		var officer 	= $('.c_officer').val().trim();
	  	
	  	$.post( "../Tab_Department_Officer/officer_position_save.php", {
	  		id: 		history.state.id,
			department: department,
			officer: 	officer
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".officer_pagination .active a").attr("id"));
					searchOfficers(page=page);
					officerInfo(history.state.id);
					$('.modal-backdrop').remove();
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// remove invalid class on new and retype password inputs
	function removeInvalidClassOnType(){
		$('.c_firstname, .c_lastname, .c_password, .c_email').keypress(function(e){
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
}
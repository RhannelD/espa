function loadStudents(){
	$("#mainpanel").load("../Tab_Student/student.php", function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchStudents(history.state.page);
		else										// regular search
			searchStudents(); 

		if (history.state.id != undefined)			// reloaded page and see previous info
			studentInfo(history.state.id);

		loadCreatingStudentForm();

		// Search trigger by Search Icon
		$(".student_search_icon").click(function(){
			searchStudents(); 
		});

		// Search trigger by Enter key
		$('.student_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchStudents(); 
		    }
		});
	});

	// replacing/storing the student history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'student';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'student', '');
	}
	
	// replacing/storing the student history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'student';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'student', '');
	}

	// Searching the Students
	function searchStudents(page = 1){
		historyReplacePage(page=page);
		var search = $(".student_search_input").val().trim();
		$(".table_student").load("../Tab_Student/student_search.php", {
			"search": search, 
			"page": page
		}, function(){
			clickedStudent();
			clickedStudentPagination();
			$(".table_student").collapse('show');
		});
	}

	// going the another student page trigger
	function clickedStudentPagination(){
		$(".student_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchStudents(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// Getting the info for viewing the selected/clicked student
	function clickedStudent(){
	    $(".student_table tbody .rows").click(function(e){
	    	var SR_Code = $(this).attr('id').trim();
	    	studentInfo(SR_Code);
	    });	
	}

	// Viewing the info of the selected student
	function studentInfo(SR_Code){
		$(".info_student").load("../Tab_Student/student_info.php", {
			id: SR_Code
		},function(){
			studentDeleteConfirmation();
			loadEditingStudentForm();
			loadShiftingStudentForm();
			loadChangeStudentPassword();
			openStudentCurriculum();
			openStudentEvaluate();
			openStudentPredict();
			historyReplaceID(id=SR_Code);
			$(".info_student").collapse('show');
		});
	}

	function unloadProgramInfo(){
		$(".info_student").collapse('hide');
		historyReplaceID(id=0);
	}

	// Open Student Grades
	function openStudentCurriculum(){
		$(".student_curriculum_open").click(function(e){	
			var id = $(this).attr("id");
			history.pushState({tab: 'student_grade', id: id}, 'student_grade', '');
			loadStudentGrade();
		});
	}

	// Open Student Grades
	function openStudentPredict(){
		$(".student_predict_open").click(function(e){	
			var id = $(this).attr("id");
			history.pushState({tab: 'student_predict', id: id}, 'student_predict', '');
			loadStudentPredict();
		});
	}

	// Open Student Evaluation
	function openStudentEvaluate(){
		$(".student_evaluate_open").click(function(e){	
			var id = $(this).attr("id");
			history.pushState({tab: 'student_evaluate', id: id}, 'student_evaluate', '');
			loadStudentEvaluate();
		});
	}

	// Asking confirmation if sure on deleting the selected student
	function studentDeleteConfirmation(){
		$(".student_delete").click(function(e){	
			var name = $(".info_student_name").text().trim();
			swal({
	            title: 'Are you sure?',
	            text: "Deleting Student \""+ name +"\" profile",
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
	            	var sr_code = $(this).attr("id");
					studentDelete(sr_code);
	            }
	        });
		});
	}

	// Deleting the selected student
	function studentDelete(id){
		$.post( "../Tab_Student/student_delete.php", {
			id: id
		}, function( data ) {
			swal(data.title, data.message, data.alert);
			if(data.alert=="success"){
				unloadProgramInfo();
				$(".student_table_row_"+id).remove();
			}
		}, "json");
	}

	// Opening the Student Creating form
	function loadCreatingStudentForm(){
		$(".create_student_open_modal").click(function(){
			$(".student_editing").empty();
			$(".student_shifting").empty();
			$(".student_password").empty();
			$(".student_creating").load("../Tab_Student/student_create.php", function(){
				loadSelectProgram();
				loadSelectTrack();
				removeInvalidClassOnType();
				creationStudentConfirmation();
			});
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

	// Confirmation before Creation
	function creationStudentConfirmation(){
		$( "#formStudentCreation" ).submit(function( e ) {
			e.preventDefault();
			var sr_code 	= $('.c_student_sr_code').val().trim();

			swal({
	            title: 'Create Student Profile?',
	            text: "Creating Student Profile \""+ sr_code +"\"",
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
	        		creatingStudent();
	        });
	    });
	}

	// Creating the Student profile
	function creatingStudent(){	
		var sr_code 	= $('.c_student_sr_code').val().trim();
		var firstname 	= $('.c_student_firstname').val().trim();
		var lastname 	= $('.c_student_lastname').val().trim();
		var email	 	= $('.c_student_email').val().trim();
		var password 	= $('.c_student_password').val().trim();
		var gender 		= $('.c_student_gender:checked').val().trim();
		var program 	= $('.c_student_program').val().trim();
		var year 		= $('.c_student_academic_year').val().trim();
		var track 		= $('.c_student_track').val().trim();
	  	
	  	$.post( "../Tab_Student/student_create_save.php", {
			sr_code: 	sr_code,
			firstname: 	firstname,
			lastname: 	lastname,
			gender: 	gender,
			email: 		email,
			password: 	password,
			program: 	program,
			year: year,
			track: track
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".student_pagination .active a").attr("id"));
					searchStudents(page=page);
					studentInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_student_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Opening the Student Editing form
	function loadEditingStudentForm(){
		$(".edit_student_open_modal").click(function(){
			var sr_code = $('.info_student_sr_code').text().trim();

			$(".student_creating").empty();
			$(".student_shifting").empty();
			$(".student_password").empty();
			$(".student_editing").load("../Tab_Student/student_edit.php", {
				sr_code: sr_code
			}, function(){
				removeInvalidClassOnType();
				editingStudentConfirmation();
			});
		});
	}

	// Confirmation before Editing
	function editingStudentConfirmation(){
		$( "#formStudentEditing" ).submit(function( e ) {
			e.preventDefault();

			var sr_code 	= $('.c_student_sr_code').val().trim();

			swal({
	            title: 'Update Student Profile?',
	            text: "Updating Student Profile \""+ sr_code +"\"",
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
	        		editingStudent();
	        });
	    });
	}

	// Creating the Program
	function editingStudent(){	
		var sr_code 	= $('.c_student_sr_code').val().trim();
		var firstname 	= $('.c_student_firstname').val().trim();
		var lastname 	= $('.c_student_lastname').val().trim();
		var email	 	= $('.c_student_email').val().trim();
		var gender 		= $('.c_student_gender:checked').val().trim();
	  	
	  	$.post( "../Tab_Student/student_edit_save.php", {
	  		sr_code: 	sr_code,
			firstname: 	firstname,
			lastname: 	lastname,
			email: 		email,
			gender: 	gender
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".student_pagination .active a").attr("id"));
					searchStudents(page=page);
					studentInfo(id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_student_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Opening the Student Editing form
	function loadShiftingStudentForm(){
		$(".shift_student_open_modal").click(function(){
			var sr_code = $('.info_student_sr_code').text().trim();

			$(".student_creating").empty();
			$(".student_editing").empty();
			$(".student_password").empty();
			$(".student_shifting").load("../Tab_Student/student_shift.php", {
				sr_code: sr_code
			}, function(){
				loadSelectProgram();
				loadSelectTrack();
				removeInvalidClassOnType();
				shiftingStudentConfirmation();
			});
		});
	}

	// Confirmation before Shifting
	function shiftingStudentConfirmation(){
		$( "#formStudentShifting" ).submit(function( e ) {
			e.preventDefault();

			var sr_code 	= $('.c_student_sr_code').val().trim();

			swal({
	            title: 'Shift Student Program?',
	            text: "Shifting Program for Student \""+ sr_code +"\"",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Shift Program',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Shift) => {
	        	if (Shift)
	        		shiftingStudent();
	        });
	    });
	}

	// Shifting Student Program
	function shiftingStudent(){
		var sr_code 	= $('.c_student_sr_code').val().trim();
		var program 	= $('.c_student_program').val().trim();
		var year 		= $('.c_student_academic_year').val().trim();
		var track 		= $('.c_student_track').val().trim();

	  	$.post( "../Tab_Student/student_shift_save.php", {
	  		sr_code: 	sr_code,
			program: 	program,
			year: 		year,
			track: 		track
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".student_pagination .active a").attr("id"));
					searchStudents(page=page);
					studentInfo(id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_student_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	function loadChangeStudentPassword(){
		$(".password_student_open_modal").click(function(){
			var sr_code = $('.info_student_sr_code').text().trim();

			$(".student_creating").empty();
			$(".student_editing").empty();
			$(".student_shifting").empty();
			$(".student_password").load("../Tab_Student/student_change_password.php", {
				sr_code: sr_code
			}, function(){
				removeInvalidClassOnType();
				changeStudentPasswordConfirmation();
				showingNotShowingPassword();
			});
		});
	}

	// Confirmation before Changing the Password
	function changeStudentPasswordConfirmation(){
		$( "#formStudentChangePassword" ).submit(function( e ) {
			e.preventDefault();

			var sr_code 	= $('.c_student_sr_code').val().trim();

			swal({
	            title: 'Change Student Password?',
	            text: "Changing Password for Student \""+ sr_code +"\"",
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
	        		changingStudentPassword();
	        });
	    });
	}

	// Change Student Password
	function changingStudentPassword(){
		var sr_code 	= $('.c_student_sr_code').val().trim();
		var password 	= $('.c_student_password').val().trim();

	  	$.post( "../Tab_Student/student_change_password_save.php", {
	  		sr_code: 	sr_code,
	  		password: 	password
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

	// remove invalid class on new and retype password inputs
	function removeInvalidClassOnType(){
		$('.c_student_sr_code, .c_student_firstname, .c_student_lastname, .c_student_password').keypress(function(e){
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
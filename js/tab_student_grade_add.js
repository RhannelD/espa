function loadStudentGradeAdd(){
	if (history.state.id == null) {
		history.replaceState(null, '', '');
		openingPage();
		return;
	}

	$("#mainpanel").load("../Tab_Student_Grade/student_grade_add.php", {
		sr_code: history.state.id
	}, function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchCourses(history.state.page);
		else										// regular search
			searchCourses();
		
		changedTobeQuery();
		changedNumOfRows();
		backToStudentCurriculum();

		// Search trigger by Search Icon
		$(".course_search_icon").click(function(){
			searchCourses(); 
		});

		// Search trigger by Enter key
		$('.course_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchCourses(); 
		    }
		});
	});

	// Back to Student Curriculum Open
	function backToStudentCurriculum(){
		$('.student_open_back').click(function(){
			var id = $(this).attr('id').trim();
			history.pushState({tab: 'student_grade', id: id}, 'student_grade', '');
			loadStudentGrade();
		});
	}

	// replacing/storing the courses history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'student_grade_add';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'student_grade_add', '');
	}

	// Searching the Courses
	function searchCourses(page = 1){
		historyReplacePage(page=page);

		var sr_code = history.state.id;
		var search = $(".course_search_input").val().trim();
		var to_be_query = $(".to_be_query").val().trim();
		var number_of_rows = $(".number_of_rows").val().trim();

		$(".table_courses").load("../Tab_Student_Grade/student_grade_add_search.php", {
			search: search, 
			sr_code: sr_code,
			to_be_query: to_be_query,
			number_of_rows: number_of_rows,
			page: page
		}, function(){
			clickedProgramPagination();
			grade_selected();
			saveGradeButtonConfirmation();
			saveAllGradeButtonConfirmation();
			deleteGradeConfirmation();
		});
	}

	// going the another courses page trigger
	function clickedProgramPagination(){
		$(".courses_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchCourses(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// Selected Query to be shown has been changed
	function changedTobeQuery(){
		$('.to_be_query').on('change' , function(){
			searchCourses();
		});
	}

	// Selected Number of rows to be shown has been changed
	function changedNumOfRows(){
		$('.number_of_rows').on('change' , function(){
			searchCourses();
		});
	}

	// Show Save button when a grade has been selected on a course
	function grade_selected(){
		$('.select_course').on('change' , function(){
			var grade = $(this).val().trim();
			var course_id = $(this).attr('id').trim();
			if (grade != "") {
				$(this).parent().siblings('td').children('.save_grade').removeClass('d-none');
				return;
			}
			if(!$(this).parent().siblings('td').children('.save_grade').hasClass("d-none")){
			    $(this).parent().siblings('td').children('.save_grade').addClass('d-none');
			}
		});
	}

	// Save grade confirmation 
	function saveGradeButtonConfirmation(){
		$('.save_grade').click(function(){
			var grade_added = {};

			var course_id = $(this).attr('id').trim();
			var grade = $(this).parent().siblings('td').children('.select_course').val().trim();

			grade_added[course_id] = grade;

			if (!parseFloat(grade_added[course_id]))
				return;

			swal({
	            title: 'Adding Grade',
	            text: "Do you really want to add this grade?",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Add',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Add) => {
	        	if (Add)
	        		addingStudentGrade(grade_added);
	        });

		});
	}

	// Saving the grade
	function addingStudentGrade(grades_added){
		var student = $('.student_name').text().trim();

	  	$.post( "../Tab_Student_Grade/student_grade_add_save.php", {
	  		sr_code: history.state.id,
			grades_added: grades_added,
			student: student
		}, function( data ) {
	        	if(data.alert=="success"){
	        		var page = parseInt($(".courses_pagination .active a").attr("id"));
					searchCourses(page=page);	  			
				}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// open Delete grade confirmation by password
	function deleteGradeConfirmation(){
		$('.delete_grade').click(function(){
			var grade_rec_id = $(this).attr('id').trim();

			$('.confirming_delete_grade').load('../Tab_Student_Grade/student_grade_add_delete.php', {
				sr_code: history.state.id,
				grade_rec_id: grade_rec_id
			}, function(){
				studentGradeDelete();
				showingNotShowingPassword();
			});
		});
	}

	// Deleting the selected grade
	function studentGradeDelete(){
		$('#formDeleteGradeConfirmation').submit(function( e ) {
			e.preventDefault();
			var grade_rec_id 	= $('.c_grade_rec_id').val().trim();
			var password 		= $('.c_password').val().trim();
			
			$.post( "../Tab_Student_Grade/student_grade_add_deleting.php", {
				password: password,
				grade_rec_id: grade_rec_id
			}, function( data ) {
	  			swal(data.title, data.message, data.alert);
	  			if(data.alert=="success"){
	  				$('#cancel_edit').click();
	  				var page = parseInt($(".courses_pagination .active a").attr("id"));
					searchCourses(page=page);
					$('.modal-backdrop').remove();
	  			}
			}, "json");
	    });
	}

	// Save grade confirmation 
	function saveAllGradeButtonConfirmation(){
		$('.save_all_grade').click(function(){
			var grades_added = {};
			$("td .select_course").each(function(i){
			  	if ($(this).val() > 0){
			  		grades_added[$(this).attr('id')] = $(this).val();
			  	}
			});

			if (Object.keys(grades_added).length == 0){
				swal('Nothing to be Saved!', 'You didn\'t select even a single grade on any course', 'info');
				return;
			}

			swal({
	            title: 'Adding All Selected Grade',
	            text: "Do you really want to add all of this grade?",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Add',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Add) => {
	        	if (Add)
	        		addingStudentGrade(grades_added);
	        });

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
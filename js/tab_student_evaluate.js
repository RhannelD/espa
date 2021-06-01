function loadStudentEvaluate(){
	if (history.state.id == null) {
		history.replaceState(null, '', '');
		openingPage();
		return;
	}
	var request_id = 0;
	if (history.state.request_id != null) {
		request_id = history.state.request_id;
	}

	$("#mainpanel").load("../Tab_Student_Evaluate/student_evaluate.php", {
		sr_code: history.state.id,
		request_id: request_id
	}, function(){
		backToStudentInfo();
		clickAddCourses();
		clickRemoveAllCourses();
		searchCourses();
		changedTobeQuery();
		clickAutoloadCourses();
		clickPrintProposal();
		clickUploadProposal();
		clickConfirmUpload();
		removeInvalidClassOnType();

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

	// Back to Student Info or Evaluation TabPane
	function backToStudentInfo(){
		$('.student_evaluate_back').click(function(){
			if (!$('#evaluation').hasClass('show')){
				$('#evaluation-tab').click();
				return;
			}
			if (history.state.request_id > 0) {
				history.pushState({tab: 'request', id: history.state.request_id}, 'request', '');
				loadRequest();
				return;
			}

			var id = $(this).attr('id').trim();
			history.pushState({tab: 'student', id: id}, 'student', '');
			loadStudents();
		});
	}

	// Go to Adding Courses Tab
	function clickAddCourses(){
		$('.add_courses').click(function(){
			$('#courses-tab').click();
		});
	}

	// Removing Added Courses Confirmation
	function clickRemoveAllCourses(){
		$('.remove_all_courses').click(function(){
			swal({
                title: 'Are you sure?',
                text: "Removing all added courses",
                icon: 'warning',
                buttons:{
                    confirm: {
                        text : 'Remove',
                        className : 'btn btn-danger'
                    },
                    cancel: {
                        visible: true,
                        className: 'btn btn-info'
                    }
                }	
            }).then((Remove) => {
            	if (Remove)
            		removingAllAddedCourses();
            });
		});
	}

	// Removing all added Courses
	function removingAllAddedCourses(){
		$('.added_courses').empty();
		addNoneAddedCourse();
		searchCourses();
	}

	// Searching the Courses
	function searchCourses(page = 1){
		var search = $(".course_search_input").val().trim();
		var to_be_query = $(".to_be_query").val().trim();
		var added_courses = getAddedCourses();

		$(".table_courses").load("../Tab_Student_Evaluate/student_evaluate_course_search.php", {
			sr_code: history.state.id,
			search: search, 
			page: page,
			to_be_query: to_be_query,
			added_courses: added_courses
		}, function(){
			clickedCoursePagination();
			addSelectedCourse();
		});
	}

	// Reloading the Search Courses retaining its page
	function reloadSearchWithPage(){
    	var page = parseInt($(".course_pagination .active a").attr("id"));
		searchCourses(page=page);
	}

	// Selected Query to be shown has been changed
	function changedTobeQuery(){
		$('.to_be_query').on('change' , function(){
			searchCourses();
		});
	}

	// going the another courses page trigger
	function clickedCoursePagination(){
		$(".course_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchCourses(page=clickedPage);
		});
	}

	// Add selected course
	function addSelectedCourse(){
		$('.add_selected_course').click(function(){
			var course_id = $(this).parent().parent().attr('id').trim();
			var added_courses = getAddedCourses();

			if(jQuery.inArray(course_id, added_courses) != -1) {
				swal('Already Added', 'Course is already exist', 'info');
				return;
			}

			$(this).children().attr('class', 'fad fa-spinner-third fa-spin');

			$.post( "../Tab_Student_Evaluate/student_evaluate_verify_selected_course.php", {
			sr_code: history.state.id,
			course_id: course_id
			}, function( data ) {
		    	 	if(data.alert=="success"){
		    	 		loadSelectedCourse(data.course_data);
						return;
		  			}
		  			swal(data.title, data.message, data.alert);
			}, "json");
		});
	}

	// Getting the Prereqs that are already added (for sql purposes)
	function getAddedCourses(){
		var added_prereqs = [];
		$(".added_courses tr").each((index, elem) => {
		  	added_prereqs.push(elem.id);
		});
		if(added_prereqs=="")
			return [];
		return added_prereqs;
	}

	// Adding the Selected Course to the Table
	function loadSelectedCourse(course){
		$.get( "../Tab_Student_Evaluate/student_evaluate_course_added.php", {
			course: course
		}, function( data ) {
				var ifPreUnitsExceed = calculateTotalUnits();

			    $('.course_added_none').remove();
				$(".added_courses").append($(data));
		    	$('#evaluation-tab').click();
		    	reloadSearchWithPage();
				removeAddedCourse();

				var ifPostUnitsExceed = calculateTotalUnits();

				if (!ifPreUnitsExceed && ifPostUnitsExceed) {
					swal('Exceeding Units!', 'You just have exceed the maximum units', 'info');
				}
		});
	}

	// Calculating the Units and Add class danger if exceeds.... Also return true if exceed
	function calculateTotalUnits(){
		var units = getTotalAddedUnits();
		$('.total_units').val(units);

		var max_units = parseInt($('.maximum_units').text().trim());
		if (units > max_units){
			$('.total_units').addClass('text-danger');
			return true;
		}
		$('.total_units').removeClass('text-danger');
		return false;
	}

	function getTotalAddedUnits() {
		var units = 0;
		$(".added_courses .course_added .course_added_units").each(function(e){
		  	units += parseInt($(this).text().trim());
		});
		return units;
	}

	// removing the selected course at the table
	function removeAddedCourse(){
		$('.remove_course_added').click(function(){
			$(this).parent().parent().remove();

			addNoneAddedCourse();
			reloadSearchWithPage()
		});
	}

	// Adding none result at table if has no Added courses
	function addNoneAddedCourse(){
		if ( $('.added_courses').children().length <= 0 ) {
		    $('.added_courses').append($.parseHTML('<tr class="course_added_none"><td class="text-nowrap alert-info" colspan="6">None</td></tr>'));
		}
		calculateTotalUnits();
	}

	// Autoload Courses Confirmation
	function clickAutoloadCourses(){
		$('.autoload_courses').click(function(){
			swal({
                title: 'Are you sure?',
                text: "System will autoload courses",
                icon: 'info',
                buttons:{
                    confirm: {
                        text : 'Load',
                        className : 'btn btn-success'
                    },
                    cancel: {
                        visible: true,
                        className: 'btn btn-info'
                    }
                }	
            }).then((Load) => {
            	if (Load)
            		autoloadingCourses();
            });
		});
	}

	// Autoloading Courses
	function autoloadingCourses() {
		var added_courses 	= getAddedCourses();
		var units 			= getTotalAddedUnits();
		var max_units 	  	= parseInt($('.maximum_units').text().trim());
		
		$.post( "../Tab_Student_Evaluate/student_evaluate_autoload_courses.php", {
		sr_code: history.state.id,
		max_units: max_units,
		units: units,
		added_courses: added_courses
		}, function( data ) {
	    	 	var ifPreUnitsExceed = calculateTotalUnits();

			    $('.course_added_none').remove();
				$(".added_courses").append($(data));
		    	$('#evaluation-tab').click();
		    	reloadSearchWithPage();
				removeAddedCourse();

				var ifPostUnitsExceed = calculateTotalUnits();

				if (!ifPreUnitsExceed && ifPostUnitsExceed) {
					swal('Exceeding Units!', 'You just have exceed the maximum units', 'info');
				}
		});
	}

	// Printing Proposal Slip Confirmation
	function clickPrintProposal(){
		$('.print_proposal').click(function(){
			var units 	= getTotalAddedUnits();
			if (units == 0) {
				swal('Add a Course!', 'Please add atleast 1 course', 'error');
				return;
			}

			swal({
	            title: 'Print Proposal Slip?',
	            text: "Printing Student's Proposal Slip",
	            icon: 'info',
	            buttons:{
	                confirm: {
	                    text : 'Print',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Create) => {
	        	if (Create)
	        		printingProposalSlip();
	        	$('.to_be_remove').remove();
	        });
		});
	}

	// Printing Student's Proposal Slip
	function printingProposalSlip(){
		var added_courses 	= getAddedCourses();

		// Create a form
		var mapForm = document.createElement("form");
		mapForm.target = "_blank";    
		mapForm.method = "POST";
		mapForm.action = "../pdf-generator/generate_proposal_slip.php";
		mapForm.setAttribute("class", "to_be_remove d-none");

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "sr_code";
		mapInput.value = history.state.id;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		var mapInput = document.createElement("input");
		mapInput.name = "added_courses";
		mapInput.value = added_courses;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Add the form to dom
		document.body.appendChild(mapForm);

		// Just submit
		mapForm.submit();
	}

	// Uploading Proposal Slip Confirmation
	function clickUploadProposal(){
		$('.upload_proposal').click(function(){
			var units 	= getTotalAddedUnits();
			if (units == 0) {
				swal('Add a Course!', 'Please add atleast 1 course', 'error');
				return;
			}

			$('#upload_proposal').modal('show');
		});
	}

	// Uploading Proposal Slip 
	function clickConfirmUpload() {
		$( "#formProposalUpload" ).submit(function( e ) {
			e.preventDefault();
			var request_id = 0;
			if (history.state.request_id != null) {
				request_id = history.state.request_id;
			}

			var added_courses 	= getAddedCourses();
			var description		= $('.c_description').val().trim();

			$.post( "../Tab_Student_Evaluate/student_evaluate_upload_proposal_slip.php", {
			sr_code: history.state.id,
			added_courses: added_courses,
			description: description,
			request_id: request_id
			}, function( data ) {
		  			$('.is-invalid').removeClass('is-invalid');
		  			if (data.hasOwnProperty('error')) {
		  				$('.c_'+data.error).addClass('is-invalid');
		  			}
		  			swal(data.title, data.message, data.alert);
			}, "json");
	    });
	}

	function removeInvalidClassOnType(){
		$('.c_description').keypress(function(e){
			$(this).removeClass('is-invalid');
		});
	}
}

function loadCourses(){
	$("#mainpanel").load("../Tab_Course/course.php", function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchCourses(history.state.page);
		else										// regular search
			searchCourses(); 

		if (history.state.id != undefined){			// reloaded page and see previous info
					courseInfo(history.state.id);}

		loadCreatingCourseForm();

		// Search trigger by Search Icon
		$("#course_search_icon").click(function(){
			searchCourses(); 
		});

		// Search trigger by Enter key
		$('#course_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchCourses(); 
		    }
		});
	});

	// replacing/storing the course history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'course';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'course', '');
	}
	
	// replacing/storing the course history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'course';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'course', '');
	}

	// Searching the Courses
	function searchCourses(page = 1){
		historyReplacePage(page=page);
		var search = $("#course_search_input").val().trim();
		$("#table_course").load("../Tab_Course/course_search.php", {
			"search": search, 
			"page": page
		}, function(){
			clickedCourse();
			clickedCoursePagination();
			$("#table_course").collapse('show');
		});
	}

	// going the another course page trigger
	function clickedCoursePagination(){
		$(".course_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchCourses(page=clickedPage);
		});
	}

	// Getting the info for viewing the selected course
	function clickedCourse(){
	    $("#course_table #rows").click(function(e){
	    	var course_id = $(this).find('td:eq(0)').text();
	    	courseInfo(course_id);
	    });	
	}

	// Viewing the info of the selected course
	function courseInfo(course_id){
		$("#info_course").load("../Tab_Course/course_info.php", {"ID":course_id},function(){
			courseDeleteConfirmation();
			loadEditingCourseForm();
			clickPrereqSearch();
			historyReplaceID(id=course_id);
			$("#info_course").collapse('show');
		});
	}

	function unloadCourseInfo(){
		$("#info_course").collapse('hide');
		historyReplaceID(id=0)
	}

	// Asking confirmation if sure on deleting the selected course
	function courseDeleteConfirmation(){
		var code = $(".info_course_code").text().trim();

		$(".course_delete").click(function(e){	
			swal({
                title: 'Are you sure?',
                text: "Deleting Course \""+ code +"\"",
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
                	var id = $(this).attr("id");
                	courseDelete(id,code);
                }
            });
		});
	}

	// Deleting the selected course
	function courseDelete(id, code){
		$.post( "../Tab_Course/course_delete.php", {
			id: id,
			code: code
		}, function( data ) {
  			swal(data.title, data.message, data.alert);
  			if(data.alert=="success"){
				unloadCourseInfo();
				$(".table_course_row_"+id).remove();
  			}
		}, "json");
	}

	// Opening the Course Editing form
	function loadEditingCourseForm(){
		$(".edit_course_open_modal").click(function(){
			$("#course_creating_modal").empty();
			var id = $("#info_course_id").text().trim();
			$("#course_editing_modal").load("../Tab_Course/course_edit.php",{
				id: id
			}, function(){
				add_prereq();
				removeAddedPrereq();
				editingConfirmation();
			});
		})
	}

	// Search Possible Pre reqs on add click
	function add_prereq(){
		$(".add_prereq").click(function(){
			searchPrereq();
			$(".success_add_prereq").remove();
		});
	}

	// Remove the added prereq at
	function removeAddedPrereq(){
		$(".delete-prereq").click(function(){
			var id = $(this).attr("id").trim();
			$(".item-prereq-"+id).remove();
			if ( $('#added_prereqs').children().length <= 0 ) {
			    $('#added_prereqs').append($.parseHTML('<tr class="item-prereq-none"><td colspan="3" class="text-nowrap">None</td></tr>'));
			}
		});
	}

	// Saving the Course that has been edited
	function editingCourse(){
	  	var id = $("#e_course_id").text().trim();
	  	var code = $("#e_course_code").val().trim();
	  	var title = $("#e_course_title").val().trim();
	  	var unit = $("#e_units").val().trim();
	  	var lec = $("#e_lecture").val().trim();
	  	var lab = $("#e_laboratory").val().trim();
	  	var req_standing = $("#e_req_standing").val().trim();
	  	var prereq_id = getAddedPrereqs();	

		$.post( "../Tab_Course/course_edit_save.php", {
			id: id,
			code: code,
			title: title,
			unit: unit,
			lec: lec,
			lab: lab,
			req_standing, req_standing,
			prereq_id, prereq_id
		}, function( data ) {
  			if(data.alert=="success"){
				$('#cancel_edit').click();
				var page = parseInt($(".course_pagination .active a").attr("id"));
				searchCourses(page=page);
				courseInfo(id);
				$('.modal-backdrop').remove();
  			}
  			$('.is-invalid').removeClass('is-invalid');
  			if (data.hasOwnProperty('error')) {
  				$('.e_course_'+data.error).addClass('is-invalid');
  			}
  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Confirmation before Saving the Edited Course
	function editingConfirmation(){
		$( "#formCourseEditing" ).submit(function( e ) {
		  	e.preventDefault();
			swal({
                title: 'Save Changes?',
                text: "Saving Course Info Changes",
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
            		editingCourse();
            });
        });
	}

	// Searching the unadded prereqs on editing
	function searchPrereq(page = 1){
		var search = $("#prereq_search_input").val().trim();
		var course_prereps = getAddedPrereqs();
		var id = $("#e_course_id").text().trim();
		$("#modal_table_prereq").load("../Tab_Course/prereq_search.php", {
			search: search,
			page: page,
			id: id,
			course_prereps: course_prereps
		}, function(){	
			add_a_Prereq();
			clickedPrereqPagination();
		});
	}

	// going the another course page trigger
	function clickedPrereqPagination(){
		$(".prereq_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchPrereq(page=clickedPage);
		});
	}

	// Search Triggers For PreReq Modal
	function clickPrereqSearch(){
		$("#prereq_search_icon").click(function(){
			searchPrereq(); 
		});

		$('#prereq_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchPrereq(); 
		    }
		});
	}

	// Getting the Prereqs that are already added (for sql purposes)
	function getAddedPrereqs(){
		var added_prereqs = [];
		$("#added_prereqs tr").each((index, elem) => {
		  	added_prereqs.push(elem.id);
		});
		if(added_prereqs=="")
			return ["0"];
		return added_prereqs;
	}

	// Adding the Choosed Prereq
	function add_a_Prereq(){
		$(".prereq_id_add").click(function(){
			var prereq_id = $(this).attr("id");
			var prereq_code = $(".prereq_code_"+prereq_id).text();
			var prereq_title = $(".prereq_title_"+prereq_id).text();

			$(this).children().attr('class', 'fad fa-spinner-third fa-spin');

			$.get( "../Tab_Course/added_prerequisite.php", {
				prereq_id: prereq_id,
				prereq_code: prereq_code,
				prereq_title: prereq_title
			}, function( data ) {
				$(".item-prereq-none").remove();
				$("#added_prereqs").append($(data));
				var page = parseInt($(".prereq_pagination .active a").attr("id"));
				searchPrereq(page = page);
				removeAddedPrereq();
				success_alert_prereq_add(prereq_code);
			});

		});
	}

	// Alert/Notif Success on Adding the PreReq
	function success_alert_prereq_add(prereq_code){
		$(".success_add_prereq").remove();
		$("#success_alert_add_prereq").append($.parseHTML(
			"<div class=\"form-control text-nowrap bg-lightgreen success_add_prereq\" >"+ prereq_code +" has been Successfully Added</div>"
		));
	}

	// Opening the Course Creating form
	function loadCreatingCourseForm(){
		$(".create_course_open_modal").click(function(){
			$("#course_editing_modal").empty();
			$("#course_creating_modal").load("../Tab_Course/course_create.php", function(){
				add_prereq();
				removeAddedPrereq();
				clickPrereqSearch();
				creationConfirmation();
			});
		});
	}	

	// Creating the Course
	function creatingCourse(){	
	  	var code = $("#c_course_code").val().trim();
	  	var title = $("#c_course_title").val().trim();
	  	var unit = $("#c_units").val().trim();
	  	var lec = $("#c_lecture").val().trim();
	  	var lab = $("#c_laboratory").val().trim();
	  	var req_standing = $("#c_req_standing").val().trim();
	  	var prereq_id = getAddedPrereqs();

		$.post( "../Tab_Course/course_create_save.php", {
			code: code,
			title: title,
			unit: unit,
			lec: lec,
			lab: lab,
			req_standing, req_standing,
			prereq_id, prereq_id
		}, function( data ) {
  			if(data.alert=="success"){
				$('#cancel_edit').click();
				var page = parseInt($(".course_pagination .active a").attr("id"));
				searchCourses(page=page);
				courseInfo(data.id);
				$('.modal-backdrop').remove();
  			}
  			$('.is-invalid').removeClass('is-invalid');
  			if (data.hasOwnProperty('error')) {
  				$('.c_course_'+data.error).addClass('is-invalid');
  			}
  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Confirmation before Creation
	function creationConfirmation(){
		$( "#formCourseCreation" ).submit(function( e ) {
			e.preventDefault();
			var code = $("#c_course_code").val().trim();
			swal({
                title: 'Create Course?',
                text: "Creating Course \""+ code +"\"",
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
            		creatingCourse();
            });
        });
	}
}
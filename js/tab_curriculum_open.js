function loadCurriculumOpen(){
	if (history.state.id == null) {
		history.replaceState(null, '', '');
		openingPage();
		return;
	}

	$("#mainpanel").load("../Tab_Curriculum_Open/curriculum_open.php", {
		id: history.state.id
	}, function(){
		backToCurriculumInfo();
		printCurriculumConfirmation();
	});

	function backToCurriculumInfo(){
		$('.curriculum_open_back').click(function(){
			var id = $(this).attr('id').trim();
			history.pushState({tab: 'curriculum', id: id}, 'curriculum', '');
			loadCurriculums();
		});
	}

	function printCurriculumConfirmation(){
		$(".curriculum_print").click(function(e){
			e.preventDefault();
			var id = $(".curriculum_print").attr('id').trim();
			var code = $(".curriculum_program").attr('id').trim();
			swal({
	            title: 'Print Curriculum?',
	            text: "Printing Curriculum \""+ code +"\"",
	            icon: 'warning',
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
	        		printingCurriculum(id);
	        	$('.to_be_remove').remove();
	        });
		});
	}

	function printingCurriculum(id){
		// Create a form
		var mapForm = document.createElement("form");
		mapForm.target = "_blank";    
		mapForm.method = "POST";
		mapForm.action = "../pdf-generator/generate_curriculum.php";
		mapForm.setAttribute("class", "to_be_remove d-none");

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "id";
		mapInput.value = id;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Add the form to dom
		document.body.appendChild(mapForm);

		// Just submit
		mapForm.submit();
	}
}

function loadCurriculumEditCourses(){
	if (history.state.id == null) {
		history.replaceState(null, '', '');
		openingPage();
		return;
	}

	$("#mainpanel").load("../Tab_Curriculum_Open/curriculum_open_edit.php", {
		id: history.state.id
	}, function(){
		backToCurriculumInfo();
		deleteCurriculumCourseConfirmation();
		openCurruculumAddingCourse();
		openCurriculumCourseDuplication();
	});

	function backToCurriculumInfo(){
		$('.curriculum_open_back').click(function(){
			var id = $(this).attr('id').trim();
			history.pushState({tab: 'curriculum', id: id}, 'curriculum', '');
			loadCurriculums();
		});
	}

	function openCurruculumAddingCourse(){
		$('.btn_adding_curriculum_course').click(function(){
			var year_sem = $(this).attr("id").trim();
			$('.add_course_modal_title').text("Curriculum Adding Course ("+$('.curriculum_title_year_and_sem_'+year_sem).text().trim()+")");
			$('.modal_table_add_course').attr('id',year_sem);

			searchAddCourse();
			clickAddCourseSearch();
		});
	}

	// Searching the unadded course on adding
	function searchAddCourse(page = 1){
		var search = $(".add_course_search_input").val().trim();
		var year_sem = $(".modal_table_add_course").attr("id").trim().split("_");
		var id   = $('.curriculum_add_course').attr('id').trim();
		var year = year_sem[0];
		var sem  = year_sem[1];

		$('.modal_table_add_course').load('../Tab_Curriculum_Open/curriculum_open_edit_search.php', {
			id: id,
			year: year,
			sem: sem,
			page: page,
			search: search
		}, function(){
			clickedAddCoursePagination();
			curriculumAddCourseConfirmation(year, sem);
		});
	}

	// going the another course page trigger
	function clickedAddCoursePagination(){
		$(".add_course_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchAddCourse(page=clickedPage);
		});
	}

	// Search Triggers For Adding Course Modal
	function clickAddCourseSearch(){
		$(".add_course_search_icon").click(function(){
			searchAddCourse(); 
		});

		$('.add_course_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchAddCourse(); 
		    }
		});
	}

	// Confirnation before adding the Course
	function curriculumAddCourseConfirmation(year, sem){
		$(".btn_course_id_add").click(function(e){	
			var id = $(this).attr('id').trim();
			var code = $(".add_course_code_"+id).text().trim();
			swal({
                title: 'Are you sure?',
                text: "Adding Course \""+ code +"\"",
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
                if (Add) {
                	curriculumAddingCourse(id, code, year, sem);
                }
            });
		});
	}

	// Adding Course to the Curriculum
	function curriculumAddingCourse(id, code, year, sem){
		var curriculum_id = $('.curriculum_add_course').attr('id').trim();

	  	$.post( "../Tab_Curriculum_Open/curriculum_open_edit_add_course.php", {
			id: id,
			code: code,
			curriculum_id: curriculum_id,
			year: year,
			sem: sem
		}, function( data ) {
	    	 	if(data.alert=="success"){
					var page = parseInt($(".add_course_pagination .active a").attr("id"));
					searchAddCourse(page=page);
					refreshSemester(curriculum_id, year, sem);
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	function refreshSemester(curriculum_id, year, sem){
		$('.tbody_courses_'+year+'_'+sem).load('../Tab_Curriculum_Open/curriculum_open_edit_persem.php', {
			id: curriculum_id,
			year: year,
			sem: sem
		}, function(){
			deleteCurriculumCourseConfirmation();
		});
	}

	function deleteCurriculumCourseConfirmation(){
		$(".delete_course").click(function(e){	
			var id = $(this).attr('id').trim();
			var code = $(".course_code_"+id).text().trim();

			var year_sem = $(this).parent('td').parent('tr').parent('tbody').attr("id").trim().split("_");
			var year = year_sem[0];
			var sem  = year_sem[1];

			swal({
                title: 'Are you sure?',
                text: "Remove Course \""+ code +"\"",
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
                if (Remove) {
                	deletingCurriculumCourse(id, code, year, sem);
                }
            });
		});
	}

	function deletingCurriculumCourse(id, code, year, sem){
		var curriculum_id = $('.curriculum_add_course').attr('id').trim();

		$.post( "../Tab_Curriculum_Open/curriculum_open_edit_delete.php", {
			curriculum_id: curriculum_id,
			id: id,
			code: code,
			year: year,
			sem: sem
		}, function( data ) {
  			swal(data.title, data.message, data.alert);
  			if(data.alert=="success"){
  				refreshSemester(curriculum_id, year, sem);
  			}
		}, "json");
	}

	function openCurriculumCourseDuplication(){
		$('.btn_curriculum_duplicate').click(function(){
			searchCurrucilumCourseDuplication();
			clickCurrucilumCourseDuplicationSearch();
		});
	}

	// Search Triggers For Adding Course Modal
	function clickCurrucilumCourseDuplicationSearch(){
		$(".duplicate_curriculum_search_icon").click(function(){
			searchCurrucilumCourseDuplication(); 
		});

		// Search trigger by Enter key
		$('.duplicate_curriculum_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchCurrucilumCourseDuplication(); 
		    }
		});
	}

	// Searching the unadded course on adding
	function searchCurrucilumCourseDuplication(page = 1){
		var search = $(".duplicate_curriculum_search_input").val().trim();

		$('.modal_table_duplicate_curriculum').load('../Tab_Curriculum/curriculum_search.php', {
			page: page,
			search: search,
			notIN: history.state.id
		}, function(){
			clickedCurriculumPagination();
			clickedCurriculum();
		});
	}

	// going the another curriculum page trigger
	function clickedCurriculumPagination(){
		$(".curriculum_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchCurrucilumCourseDuplication(page=clickedPage);
		});
	}

	// Getting the info for viewing the selected/clicked curriculum
	function clickedCurriculum(){
	    $(".curriculum_table tbody .rows").click(function(e){
	    	var id = $(this).attr('id').trim();

	    	curriculumInfo(id);
	    });	
	}

	// Viewing the info of the selected curriculum
	function curriculumInfo(id){
		$(".modal_info_duplicate_curriculum").load("../Tab_Curriculum/curriculum_info.php", {
			id: id,
			duplicate: true
		},function(){
			duplicateCurriculumCoursesConfirmation();
		});
	}

	// Curriculum Courses Duplication Confirmation before Procceding
	function duplicateCurriculumCoursesConfirmation(){
		$('.confirm_curriculum_ducplicate').click(function(){
			var id = $(this).attr('id').trim();
			$('.close_curriculum_duplicate').click();

			swal({
	            title: 'Duplicate Curriculum?',
	            text: "Curriculum Courses will be Duplicated and remove the current added courses",
	            icon: 'warning',
	            buttons:{
	                confirm: {
	                    text : 'Duplicate',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Duplicate) => {
	        	if (Duplicate)
	        		duplicatingCurriculumCourses(id);
	        });
		});
	}

	function duplicatingCurriculumCourses(id){
	  	$.post( "../Tab_Curriculum_Open/curriculum_open_edit_duplication_of_courses.php", {
			id: history.state.id,
			curriculum_id: id
		}, function( data ) {
	  			swal(data.title, data.message, data.alert);
	  			$('.modal-backdrop').remove();
	    	 	if(data.alert=="success"){
					loadCurriculumEditCourses();
	  			}
		}, "json");
	}
}
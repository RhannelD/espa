function loadCurriculums(){
	$("#mainpanel").load("../Tab_Curriculum/curriculum.php", function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchCurriculums(history.state.page);
		else										// regular search
			searchCurriculums(); 

		if (history.state.id != undefined){			// reloaded page and see previous info
			curriculumInfo(history.state.id);}

		loadCreatingCurriculumForm();

		// Search trigger by Search Icon
		$(".curriculum_search_icon").click(function(){
			searchCurriculums(); 
		});

		// Search trigger by Enter key
		$('.curriculum_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchCurriculums(); 
		    }
		});
	});

	// replacing/storing the curriculum history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'curriculum';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'curriculum', '');
	}
	
	// replacing/storing the curriculum history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'curriculum';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'curriculum', '');
	}

	// Searching the Curriculums
	function searchCurriculums(page = 1){
		historyReplacePage(page=page);
		var search = $(".curriculum_search_input").val().trim();
		$(".table_curriculum").load("../Tab_Curriculum/curriculum_search.php", {
			"search": search, 
			"page": page
		}, function(){
			clickedCurriculum();
			clickedCurriculumPagination();
			$(".table_curriculum").collapse('show');
		});
	}

	// going the another curriculum page trigger
	function clickedCurriculumPagination(){
		$(".curriculum_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchCurriculums(page=clickedPage);
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
		$(".info_curriculum").load("../Tab_Curriculum/curriculum_info.php", {
			id: id
		},function(){
			curriculumDeleteConfirmation();
			loadEditingCurriculumForm();
			loadEditingCurriculumCourse();
			historyReplaceID(id=id);
			openCurriculum();
			$(".info_curriculum").collapse('show');
		});
	}

	function unloadCurriculumInfo(){
		$(".info_curriculum").collapse('hide');
		historyReplaceID(id=0);
	}

	function openCurriculum(){
		$(".curriculum_open").click(function(e){	
			var id = $(this).attr("id");
			history.pushState({tab: 'curriculum_open', id: id}, 'curriculum_open', '');
			loadCurriculumOpen();
		});
	}

	// Asking confirmation if sure on deleting the selected curriculum
	function curriculumDeleteConfirmation(){
		$(".curriculum_delete").click(function(e){	
			var code = $(".info_curriculum_program").attr('id').trim();
			swal({
                title: 'Are you sure?',
                text: "Deleting Curriculum \""+ code +"\"",
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
                	curriculumDelete(id,code);
                }
            });
		});
	}

	// Deleting the selected course
	function curriculumDelete(id, code){
		$.post( "../Tab_Curriculum/curriculum_delete.php", {
			id: id,
			code: code
		}, function( data ) {
  			swal(data.title, data.message, data.alert);
  			if(data.alert=="success"){
				unloadCurriculumInfo();
				$(".curriculum_table_row_"+id).remove();
  			}
		}, "json");
	}

	// Opening the Curriculum Creating form
	function loadCreatingCurriculumForm(){
		$(".create_curriculum_open_modal").click(function(){
			$(".curriculum_editing").empty();
			$(".curriculum_creating").load("../Tab_Curriculum/curriculum_create.php", function(){
				loadSelectProgram();
				addReferenceInput();
				removeReferenceInput();
				creationCurriculumConfirmation();
			});
		});
	}	

	// loading/adding the programs for selection
	function loadSelectProgram(){
		$(".c_curriculum_department").on('change' , function(){
			var id = $(".c_curriculum_department").val().trim();
			$(".c_curriculum_program").load("../Tab_Curriculum/curriculum_load_select_program.php", {
				id: id
			});
		});
	}

	// Add another input reference
	function addReferenceInput(){
		$('.add_reference').click(function(){
			$.get('../Tab_Curriculum/curriculum_add_input_reference.php', function(data){
				$('.c_curriculum_references').append($(data));
				removeReferenceInput();
			});
		});
	}

	// Remove another input reference
	function removeReferenceInput(){
		$('.remove_reference').click(function(e){
			if ( $('.c_curriculum_references').children().length > 1 ) {
				$(this).parent('div').parent('div').remove();
			}
		});
	}

	// Confirmation before Creation
	function creationCurriculumConfirmation(){
		$( "#formCurriculumCreation" ).submit(function( e ) {
			e.preventDefault();
			var code = $(".c_curriculum_program option:selected").attr('id').trim();
			swal({
	            title: 'Create Curriculum?',
	            text: "Creating Curriculum \""+ code +"\"",
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
	        		creatingCurriculum();
	        });
	    });
	}

	// Creating the Curriculum
	function creatingCurriculum(){	
		var department = $(".c_curriculum_department").val().trim();
		var program = $(".c_curriculum_program").val().trim();
		var code = $(".c_curriculum_program option:selected").attr('id').trim();
	  	var track = $(".c_curriculum_track").val().trim();
	  	var academic_year = $(".c_curriculum_academic_year").val().trim();
	  	var references = getAddedReferences();
	  	
	  	$.post( "../Tab_Curriculum/curriculum_create_save.php", {
			department: department,
			program: program,
			code: code,
			track: track,
			academic_year: academic_year,
			references: references
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".curriculum_pagination .active a").attr("id"));
					searchCurriculums(page=page);
					curriculumInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				invalid_inputs(data.error);
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	function invalid_inputs(error){
		if(error=='reference'){
			var references = getAddedReferences();
			references = references.sort();
			for (var i = 0; i < references.length - 1; i++) {
			    if (references[i + 1] == references[i]) {
			    	$('.c_curriculum_reference').filter(function(){return this.value==references[i]}).addClass('is-invalid');
			    }
			}
			return;
		}
		$('.c_curriculum_'+error).addClass('is-invalid');
	}

	// Getting the getAddedReferences
	function getAddedReferences(){
		var added_references = [];
		$(".c_curriculum_references div input").each((index, elem) => {
			added_references.push($(elem).val());
		});
		if(added_references=="")
			return [""];
		return added_references;
	}

	// Opening the Curriculum Editing form
	function loadEditingCurriculumForm(){
		$(".edit_curriculum_open_modal").click(function(){
			var id = $(".info_curriculum_id").text().trim();
			$(".curriculum_creating").empty();
			$(".curriculum_editing").load("../Tab_Curriculum/curriculum_edit.php", {
				id: id
			}, function(){
				addReferenceInput();
				removeReferenceInput();
				editingCurriculumConfirmation();
			});
		});
	}	

	// Confirmation before Updating
	function editingCurriculumConfirmation(){
		$( "#formCurriculumEditing" ).submit(function( e ) {
			e.preventDefault();
			var code = $(".c_curriculum_program").attr('id').trim();
			swal({
	            title: 'Update Curriculum?',
	            text: "Updating Curriculum \""+ code +"\"",
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
	        		editingCurriculum();
	        });
	    });
	}

	// Updating the Curriculum
	function editingCurriculum(){	
		var id = $(".c_curriculum_id").text().trim();
	  	var track = $(".c_curriculum_track").val().trim();
	  	var references = getAddedReferences();
	  	
	  	$.post( "../Tab_Curriculum/curriculum_edit_save.php", {
			id: id,
			track: track,
			references: references
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".curriculum_pagination .active a").attr("id"));
					searchCurriculums(page=page);
					curriculumInfo(id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				invalid_inputs(data.error);
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Load editing Curriculum Courses
	function loadEditingCurriculumCourse(){
		$(".curriculum_edit_courses").click(function(){
			var id = $(this).attr('id').trim();
			history.pushState({tab: 'curriculum_edit_course', id: id}, 'curriculum_edit_course', '');
			loadCurriculumEditCourses();
		});
	}
}
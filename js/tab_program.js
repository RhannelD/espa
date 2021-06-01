function loadPrograms(){
	$("#mainpanel").load("../Tab_Program/program.php", function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchPrograms(history.state.page);
		else										// regular search
			searchPrograms(); 

		if (history.state.id != undefined)			// reloaded page and see previous info
			programInfo(history.state.id);

		loadCreatingProgramForm();

		// Search trigger by Search Icon
		$(".program_search_icon").click(function(){
			searchPrograms(); 
		});

		// Search trigger by Enter key
		$('.program_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchPrograms(); 
		    }
		});
	});

	// replacing/storing the program history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'program';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'program', '');
	}
	
	// replacing/storing the program history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'program';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'program', '');
	}

	// Searching the Programs
	function searchPrograms(page = 1){
		historyReplacePage(page=page);
		var search = $(".program_search_input").val().trim();
		$(".table_program").load("../Tab_Program/program_search.php", {
			"search": search, 
			"page": page
		}, function(){
			clickedProgram();
			clickedProgramPagination();
			$(".table_program").collapse('show');
		});
	}

	// going the another program page trigger
	function clickedProgramPagination(){
		$(".program_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchPrograms(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// Getting the info for viewing the selected/clicked program
	function clickedProgram(){
	    $(".program_table tbody .rows").click(function(e){
	    	var program_id = $(this).attr('id').trim();
	    	programInfo(program_id);
	    });	
	}

	// Viewing the info of the selected program
	function programInfo(program_id){
		$(".info_program").load("../Tab_Program/program_info.php", {
			id: program_id
		},function(){
			programDeleteConfirmation();
			loadEditingProgramForm();
			historyReplaceID(id=program_id);
			$(".info_program").collapse('show');
		});
	}

	function unloadProgramInfo(){
		$(".info_program").collapse('hide');
		historyReplaceID(id=0);
	}

	// Asking confirmation if sure on deleting the selected program
	function programDeleteConfirmation(){
		$(".program_delete").click(function(e){	
			var code = $(".info_program_code").text().trim();
			swal({
	            title: 'Are you sure?',
	            text: "Deleting Program \""+ code +"\"",
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
					programDelete(id, code);
	            }
	        });
		});
	}

	// Deleting the selected program
	function programDelete(id, code){
		$.post( "../Tab_Program/program_delete.php", {
			id: id,
			code: code
		}, function( data ) {
			swal(data.title, data.message, data.alert);
			if(data.alert=="success"){
				unloadProgramInfo();
				$(".program_table_row_"+id).remove();
			}
		}, "json");
	}

	// Opening the Program Creating form
	function loadCreatingProgramForm(){
		$(".create_program_open_modal").click(function(){
			$(".program_editing").empty();
			$(".program_creating").load("../Tab_Program/program_create.php", function(){
				creationProgramConfirmation();
			});
		});
	}	

	// Confirmation before Creation
	function creationProgramConfirmation(){
		$( "#formProgramCreation" ).submit(function( e ) {
			e.preventDefault();
			var code = $(".c_program_code").val().trim();
			swal({
	            title: 'Create Program?',
	            text: "Creating Program \""+ code +"\"",
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
	        		creatingProgram();
	        });
	    });
	}

	// Creating the Program
	function creatingProgram(){	
	  	var code = $(".c_program_code").val().trim();
	  	var title = $(".c_program_title").val().trim();
	  	var department = $(".c_program_department").val().trim();
	  	
	  	$.post( "../Tab_Program/program_create_save.php", {
			code: code,
			title: title,
			department: department
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".program_pagination .active a").attr("id"));
					searchPrograms(page=page);
					programInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_program_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Opening the Program Editing form
	function loadEditingProgramForm(){
		$(".edit_program_open_modal").click(function(){
			var id = $(".info_program_id").text().trim();
			$(".program_creating").empty();
			$(".program_editing").load("../Tab_Program/program_edit.php", {
				id: id
			}, function(){
				editingProgramConfirmation();
			});
		});
	}	

	// Confirmation before Editing
	function editingProgramConfirmation(){
		$( "#formProgramEditing" ).submit(function( e ) {
			e.preventDefault();
			var code = $(".c_program_code").val().trim();
			swal({
	            title: 'Edit Program?',
	            text: "Editing Program \""+ code +"\"",
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
	        		editingProgram();
	        });
	    });
	}

	// Creating the Program
	function editingProgram(){	
	  	var id = $(".c_program_id").text().trim();
	  	var code = $(".c_program_code").val().trim();
	  	var title = $(".c_program_title").val().trim();
	  	var department = $(".c_program_department").val().trim();
	  	
	  	$.post( "../Tab_Program/program_edit_save.php", {
	  		id: id,
			code: code,
			title: title,
			department: department
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".program_pagination .active a").attr("id"));
					searchPrograms(page=page);
					programInfo(id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_program_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

}

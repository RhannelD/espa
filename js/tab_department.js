function loadDepartments(){
	$("#mainpanel").load("../Tab_Department/department.php", function(){
		if (history.state.page != undefined)		// reloaded page and search recent page
			searchDepartments(history.state.page);
		else										// regular search
			searchDepartments(); 

		if (history.state.id != undefined){			// reloaded page and see previous info
			departmentInfo(history.state.id);}

		searchDepartments(); 
		loadCreatingDepartmentForm();

		// Search trigger by Search Icon
		$(".department_search_icon").click(function(){
			searchDepartments(); 
		});

		// Search trigger by Enter key
		$('.department_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchDepartments(); 
		    }
		});
	});

	// replacing/storing the department history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'department';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'department', '');
	}
	
	// replacing/storing the department history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'department';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'department', '');
	}

	// Searching the Departments
	function searchDepartments(page = 1){
		historyReplacePage(page = page);
		var search = $(".department_search_input").val().trim();
		$(".table_department").load("../Tab_Department/department_search.php", {
			"search": search, 
			"page": page
		}, function(){
			clickedDepartment();
			clickedDepartmentPagination();
			$(".table_department").collapse('show');
		});
	}

	// going the another department page trigger
	function clickedDepartmentPagination(){
		$(".department_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchDepartments(page=clickedPage);
		});
	}

	// Getting the info for viewing the selected course
	function clickedDepartment(){
	    $(".department_table tbody .rows").click(function(e){
	    	var department_id = $(this).attr('id').trim();
	    	departmentInfo(department_id);
	    });	
	}

	// Viewing the info of the selected department
	function departmentInfo(department_id){
		$(".info_department").load("../Tab_Department/department_info.php", {
			"id":department_id
		},function(){
			courseDeleteConfirmation();
			loadEditingDepartmentForm();
			loadEditingDepartmentLogoForm();
			historyReplaceID(id = department_id);
			$(".info_department").collapse('show');
		});
	}

	function unloadDepartmentInfo(){
		$(".info_department").collapse('hide');
		historyReplaceID(id = 0);
	}

	// Asking confirmation if sure on deleting the selected department
	function courseDeleteConfirmation(){
		$(".department_delete").click(function(e){	
			var code = $(".info_department_code").text().trim();
			swal({
	            title: 'Are you sure?',
	            text: "Deleting Department \""+ code +"\"",
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
	            	var logo = $(".info_department_logo").attr('id');
					departmentDelete(id, code, logo);
	            }
	        });
		});
	}

	// Deleting the selected department
	function departmentDelete(id, code, logo){
		$.post( "../Tab_Department/department_delete.php", {
			id: id,
			code: code,
			logo, logo
		}, function( data ) {
			swal(data.title, data.message, data.alert);
			if(data.alert=="success"){
				unloadDepartmentInfo();
				$(".department_table_row_"+id).remove();
			}
		}, "json");
	}

	// Opening the Course Creating form
	function loadCreatingDepartmentForm(){
		$(".create_department_open_modal").click(function(){
			$(".department_editing").empty();
			$(".department_logo_editing").empty();
			$(".department_creating").load("../Tab_Department/department_create.php", function(){
				loadLogo();
				$('.create_new_dean').collapse('hide');
				createNewDean();
				$('.create_new_head').collapse('hide');
				createNewHead();
				creationConfirmation();
			});
		});
	}	

	// Load the selected image (for create and edit)
	function loadLogo(){
		$(".c_department_logo").on('change' , function(){
			if(!this.files[0]){									// If not selected a file
				$(".dept_logo").text('Choose file');
				$('.dept_add_logo').attr('src', '../img/icon/Department.png');
				return;
			}
			var file = this.files[0];
			var fileType = file["type"];
			var validImageTypes = ["image/jpeg", "image/png"];

			if ($.inArray(fileType, validImageTypes) < 0) {		// Unselect the selected non-image file
				$('.dept_add_logo').attr('src', '../img/icon/Department.png');
				$(this).val("");
				$(".dept_logo").text('Choose file');
			    return;
			}
			var reader = new FileReader();
	        reader.onload = function (e) {
	            $('.dept_add_logo').attr('src', e.target.result);
	        }
			$(".dept_logo").text($(this).val().replace(/C:\\fakepath\\/i, ''));
	        reader.readAsDataURL(file);
		});
	}

	// Open/Close the form's Dept Dean's Creation
	function createNewDean(){
		$("#c_department_dean").change(function() {
			if ($(this).val() == "New") {
				$('.create_new_dean').collapse('show');
				$('.c_new_dean_name').attr('required', '');
				$('.dean_male').attr('required', '');
			} else {
				$('.create_new_dean').collapse('hide');
				$('.c_new_dean_name').removeAttr('required');
				$('.dean_male').removeAttr('required');
			}
		});
	}

	// Open/Close the form's Dept Head's Creation
	function createNewHead(){
		$("#c_department_head").change(function() {
			if ($(this).val() == "New") {
				$('.create_new_head').collapse('show');
				$('.c_new_head_name').attr('required', '');
				$('.head_male').attr('required', '');
			} else {
				$('.create_new_head').collapse('hide');
				$('.c_new_head_name').removeAttr('required');
				$('.head_male').removeAttr('required');
			}
		});
	}

	// Confirmation before Creation
	function creationConfirmation(){
		$( "#formDepartmentCreation" ).submit(function( e ) {
			e.preventDefault();
			var code = $("#c_department_code").val().trim();
			swal({
	            title: 'Create Department?',
	            text: "Creating Department \""+ code +"\"",
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
	        		creatingDepartmentDepartment();
	        });
	    });
	}

	// Creating the Department
	function creatingDepartmentDepartment(){	
		var file_data = $('.c_department_logo').prop('files')[0];   
		var file_type = $('.c_department_logo').val();
		file_type = file_type.substring(file_type.lastIndexOf('.') + 1).toLowerCase();
		var form_data = new FormData();                  
	    form_data.append('file', file_data);              
	    form_data.append('file_type', file_type);

	  	var logo = $(".c_department_logo").val().trim();
	  	var code = $(".c_department_code").val().trim();
	  	var title = $(".c_department_title").val().trim();
	  	var selected_dean = $(".c_department_dean").val().trim();
	  	var selected_head = $(".c_department_head").val().trim();
	  	var dean_name = null;
		var dean_gender = null;
	  	var head_name = null;
		var head_gender = null;

	  	if(selected_dean=='New'){
	  		var dean_name = $(".c_department_dean_name").val().trim();
	  		var dean_gender = $(".c_department_dean_gender:checked").val().trim();
	  	}
	  	if(selected_head=='New'){
	  		var head_name = $(".c_department_head_name").val().trim();
	  		var head_gender = $(".c_department_head_gender:checked").val().trim();
	  	}
	  	
		form_data.append('logo', logo);
		form_data.append('code', code);
		form_data.append('title', title);
		form_data.append('selected_dean', selected_dean);
		form_data.append('selected_head', selected_head);
		form_data.append('dean_name', dean_name);
		form_data.append('dean_gender', dean_gender);
		form_data.append('head_name', head_name);
		form_data.append('head_gender', head_gender);

		$.ajax({
	        url: '../Tab_Department/department_create_save.php',
	        dataType: 'json',
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: form_data,                         
	        type: 'post',
	        success: function(data){
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".department_pagination .active a").attr("id"));
					searchDepartments(page=page);
					departmentInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_department_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
	        }
	    });
	}

	// Opening the Department Creating form
	function loadEditingDepartmentForm(){
		$(".edit_department_open_modal").click(function(){
			$(".department_creating").empty();
			$(".department_logo_editing").empty();
			var id = $(".info_department_id").text().trim();
			$(".department_editing").load("../Tab_Department/department_edit.php", {
				id: id
			}, function(){
				loadLogo();
				$('.create_new_dean').collapse('hide');
				createNewDean();
				$('.create_new_head').collapse('hide');
				createNewHead();
				editingDepartmentConfirmation();
			});
		});
	}	

	// Confirmation before Editing
	function editingDepartmentConfirmation(){
		$( "#formDepartmentEditing" ).submit(function( e ) {
			e.preventDefault();
			var code = $("#c_department_code").val().trim();
			swal({
	            title: 'Edit Department?',
	            text: "Editing Department \""+ code +"\"",
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
	        		editingDepartment();
	        });
	    });
	}

	// Creating the Department
	function editingDepartment(){	
	  	var id = $(".c_department_id").text().trim();
	  	var code = $(".c_department_code").val().trim();
	  	var title = $(".c_department_title").val().trim();
	  	var selected_dean = $(".c_department_dean").val().trim();
	  	var selected_head = $(".c_department_head").val().trim();
	  	var dean_name = null;
		var dean_gender = null;
	  	var head_name = null;
		var head_gender = null;

	  	if(selected_dean=='New'){
	  		var dean_name = $(".c_department_dean_name").val().trim();
	  		var dean_gender = $(".c_department_dean_gender:checked").val().trim();
	  	}
	  	if(selected_head=='New'){
	  		var head_name = $(".c_department_head_name").val().trim();
	  		var head_gender = $(".c_department_head_gender:checked").val().trim();
	  	}
	  	
		$.post( "../Tab_Department/department_edit_save.php", {
			id: id,
			code: code,
			title: title,
			selected_dean: selected_dean,
			selected_head: selected_head,
			dean_name: dean_name,
			dean_gender: dean_gender,
			head_name: head_name,
			head_gender: head_gender
		}, function( data ) {
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".department_pagination .active a").attr("id"));
					searchDepartments(page=page);
					departmentInfo(id);
					$('.modal-backdrop').remove();
	  			}
	  			$('.is-invalid').removeClass('is-invalid');
	  			if (data.hasOwnProperty('error')) {
	  				$('.c_department_'+data.error).addClass('is-invalid');
	  			}
	  			swal(data.title, data.message, data.alert);
		}, "json");
	}

	// Opening the Department Creating Logo form
	function loadEditingDepartmentLogoForm(){
		$(".info_department_logo").click(function(){
			$(".department_creating").empty();
			$(".department_editing").empty();
			var id = $(".info_department_id").text().trim();
			$(".department_logo_editing").load("../Tab_Department/department_edit_logo.php", {
				id: id
			}, function(){
				loadLogo();
				editingLogoConfirmation();
			});
		});
	}	

	// Confirmation before Editing
	function editingLogoConfirmation(){
		$( "#formDepartmentLogoEditing" ).submit(function( e ) {
			e.preventDefault();
			var code = $(".info_department_code").text().trim();
			swal({
	            title: 'Edit Department Logo?',
	            text: "Editing Department \""+ code +"\" Logo",
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
	        		editingDepartmentLogo();
	        });
	    });
	}

	// Save new Logo
	function editingDepartmentLogo(){	
		var file_data = $('.c_department_logo').prop('files')[0];   
		var file_type = $('.c_department_logo').val();
		file_type = file_type.substring(file_type.lastIndexOf('.') + 1).toLowerCase();
		var form_data = new FormData();                  
	    form_data.append('file', file_data);              
	    form_data.append('file_type', file_type);

	    var id = $(".info_department_id").text().trim();
		form_data.append('id', id);

	    var code = $(".info_department_code").text().trim();
		form_data.append('code', code);

	    var logo = $(".dept_add_logo").attr('id').trim();
		form_data.append('logo', logo);

		$.ajax({
	        url: '../Tab_Department/department_edit_logo_save.php',
	        dataType: 'json',
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: form_data,                         
	        type: 'post',
	        success: function(data){
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					departmentInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			changeDepartmentIcon();
	  			swal(data.title, data.message, data.alert);
	        }
	    });
	}
}

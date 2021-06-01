function loadDepartments_chp_view(){
	$("#mainpanel").load("../Tab_Department/department_chp_view.php", function(){
		loadEditingDepartmentForm();
		loadEditingDepartmentLogoForm();
	});

	// Viewing the info of the selected department
	function departmentInfo(department_id){
		$(".info_department").load("../Tab_Department/department_info.php", {
			"id":department_id
		},function(){
			historyReplaceID(id = department_id);
			$(".info_department").collapse('show');
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

	// Opening the Department Creating form
	function loadEditingDepartmentForm(){
		$(".edit_department_open_modal").click(function(){
			$(".department_creating").empty();
			$(".department_logo_editing").empty();
			var id = $(".info_department_id").text().trim();
			$(".department_editing").load("../Tab_Department/department_edit.php", {
				id: id
			}, function(){
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
					loadDepartments_chp_view();
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
					loadDepartments_chp_view();
					$('.modal-backdrop').remove();
	  			}
	  			changeDepartmentIcon();
	  			swal(data.title, data.message, data.alert);
	        }
	    });
	}
}

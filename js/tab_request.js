function loadRequest(){
	var files_upload = [] 					// Variable for files selected at requesting

	// File types accepted on uploading
	var validFilePDF = ["application/pdf"];
	var validFileIMG = ["image/jpeg", "image/png"];
	var validFileTypes = $.merge( validFilePDF, validFileIMG );


	$("#mainpanel").load("../Tab_Request/request.php", function(){
		searchTriggers();
		loadCreatingProgramForm();

		if (history.state.page != undefined)		// reloaded page and search recent page
			searchRequest(history.state.page);
		else										// regular search
			searchRequest(); 

		if (history.state.id != undefined)			// reloaded page and see previous info
			requestInfo(history.state.id);
	});

	function searchTriggers() {
		// Search trigger by Search Icon
		$(".request_search_icon").click(function(){
			searchRequest(); 
		});

		// Search trigger by Enter key
		$('.request_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchRequest(); 
		    }
		});

		$('.input_order, .input_show').on('change', function(){
			searchRequest(); 
		});
	}

	// replacing/storing the request history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'request';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'request', '');
	}

	// replacing/storing the program request id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'request';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'request', '');
	}
	
	// Searching the Programs
	function searchRequest(page = 1){
		historyReplacePage(page=page);
		var search = $(".request_search_input").val().trim();
		var order  = $('.input_order').val().trim();
		var show  = $('.input_show').val().trim();

		$(".table_request").load("../Tab_Request/request_search.php", {
			search: search, 
			page: 	page,
			order: 	order,
			show: 	show
		}, function(){
			clickedHistoryPagination();
			clickedRequest();
			$(".table_request").collapse('show');
		});
	}

	// going the another request page trigger
	function clickedHistoryPagination(){
		$(".request_pagination	 .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchRequest(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// Getting the info for viewing the selected/clicked proposal
	function clickedRequest(){
	    $(".request_table tbody .rows").click(function(e){
	    	var request_id = $(this).attr('id').trim();
	    	requestInfo(request_id);
	    });	
	}

	// Viewing the info of the selected proposal
	function requestInfo(request_id){
		$(".info_request").load("../Tab_Request/request_info.php", {
			id: request_id
		},function(){
			requestDeleteConfirmation();
			historyReplaceID(id=request_id);
			openStudentEvaluate();
			denyRequest();
			$(".info_request").collapse('show');
			$('#request_info-tab').removeClass('disabled');
			$('#request_info-tab').click();
		});
	}

	function unloadProposalInfo(){
		$('#request_info-tab').addClass('disabled');
		$('#request-tab').click();
		$(".info_request").collapse('hide');
		historyReplaceID(id=0);
	}

	// Open Student Evaluation
	function openStudentEvaluate(){
		$(".request_evaluate").click(function(){	
			var id = $(this).attr("id");
			history.pushState({tab: 'student_evaluate', id: id, request_id: history.state.id}, 'student_evaluate', '');
			loadStudentEvaluate();
		});
	}

	// Opening the request Creating form
	function loadCreatingProgramForm(){
		$(".create_request_open_modal").click(function(){
			$(".request_creating").load("../Tab_Request/request_create.php", function(){
				loadSelectedFiles();
				sendRequestConfirmation();
			});
		});
	}	

	// Loading the Selected files on the request form
	function loadSelectedFiles(){
		$(".c_file").on('change', function(e){
			var files = e.target.files;
  			var filesLength = files.length;

  			$('.selected_file_preview').empty();

  			var files_large_size = [];
			for (var i = 0; i < filesLength; i++) {
				if ($.inArray(files[i]['type'], validFileTypes) < 0)
					continue;
				if (files[i].size >= 2000000) {
					files_large_size.push(files[i].name);
					continue;
				}
				files_upload[files[i]['name']] = files[i]
			}

			if (files_large_size.length > 0 ){
				swal('Files to large!', files_large_size.toString()+' exceeds 2mb', 'info');
			}

			loadSelectedFilesToUpload();

			if (Object.keys(files_upload).length != 0)
				$('.selected_file_preview').collapse('show');
		});
	}

	// Loading all off files stored in the array
	function loadSelectedFilesToUpload(){
		for (var item in files_upload) {
			var filetype = files_upload[item]['type'];
			
			if ($.inArray(filetype, validFileTypes) < 0) {
				return;		// equivalent of continue
			}

			var file_icon = 'fa-exclamation-circle';
			if($.inArray(filetype, validFilePDF) >= 0) {
				file_icon = 'fa-file-pdf';
			}
			if($.inArray(filetype, validFileIMG) >= 0) {
				file_icon = 'fa-file-image';
			}

			$(".selected_file_preview").append(
				'<div class="input-group mb pb-1 added_file_display" style="display:none;" id="'+item+'"> ' +
				'	<div class="input-group-prepend"> ' +
				'		<span class="input-group-text"> ' +
				'			<i class="fas '+ file_icon +'"></i> ' +
				'		</span> ' +
				'	</div> ' +
				'	<input type="text" class="form-control bg-white" disabled value="'+ files_upload[item].name +'"> ' +
				'	<div class="input-group-append"> ' +
				'		<a class="input-group-text bg-danger text-white remove_selected_file"> ' +
				'			<i class="fas fa-times"></i> ' +
				'		</a> ' +
				'	</div>' +
				'</div>'
			);
		};

		unloadSelectedFileToUpload();
		$('.added_file_display').fadeIn('slow');
	}

	// Deleting the selected file from array
	function unloadSelectedFileToUpload() {
		$('.remove_selected_file').click(function(){
			var container = $(this).parent().parent().parent();
			var index_on_array = $(this).parent().parent().attr('id');

			delete files_upload[index_on_array];

			$(this).parent().parent().fadeOut(function(){
				$(this).remove();

				if (Object.keys(files_upload).length == 0){
					$('.selected_file_preview').collapse('hide');
				};
			});
		});
	}

	function sendRequestConfirmation(){
		$( "#formRequestCreation" ).submit(function( e ) {
			e.preventDefault();
			
			swal({
	            title: 'Send Request?',
	            text: "Sending Request for Evaluation",
	            icon: 'info',
	            buttons:{
	                confirm: {
	                    text : 'Send',
	                    className : 'btn btn-success'
	                },
	                cancel: {
	                    visible: true,
	                    className: 'btn btn-info'
	                }
	            }	
	        }).then((Create) => {
	        	if (Create)
	        		sendingRequest();
	        });
	    });
	}

	// Creating the Program
	function sendingRequest(){	
		var message = $(".c_message").val().trim();

		var form_data = new FormData();                  
	    form_data.append('message', message);  
		
		for (var item in files_upload) {
			form_data.append("files[]", files_upload[item]);
		}

		$.ajax({
	        url: '../Tab_Request/request_create_save.php',
	        dataType: 'json',
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: form_data,                    
	        type: 'post',
	        success: function(data){
	        	if(data.alert=="success"){
					$('#cancel_edit').click();
					var page = parseInt($(".request_pagination .active a").attr("id"));
					searchRequest(page=page);
					requestInfo(data.id);
					$('.modal-backdrop').remove();
	  			}
	  			swal(data.title, data.message, data.alert);
	        }
	    });
	}

	// Asking confirmation if sure on deleting the selected request
	function requestDeleteConfirmation(){
		$(".request_delete").click(function(e){	
			swal({
	            title: 'Are you sure?',
	            text: "Deleting Request",
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
					departmentDelete();
	            }
	        });
		});
	}

	// Deleting the selected request
	function departmentDelete(){
		$.post( "../Tab_Request/request_delete.php", {
			id: history.state.id
		}, function( data ) {
			swal(data.title, data.message, data.alert);
			if(data.alert=="success"){
				$(".request_table_row_"+history.state.id).remove();
				unloadProposalInfo();
			}
		}, "json");
	}

	// For Denying the Student's Request
	function denyRequest(){
		$('#formDenyRequest').submit(function( e ) {
			e.preventDefault();
			var description = $('.c_message').val().trim();

			$.post( "../Tab_Request/request_deny_save.php", {
				request_id: history.state.id,
				description: description
			}, function( data ) {
		  			$('.is-invalid').removeClass('is-invalid');
		  			if (data.hasOwnProperty('error')) {
		  				$('.c_'+data.error).addClass('is-invalid');
		  			}
		  			if(data.alert=="success"){
						$('#cancel_edit').click();
						var page = parseInt($(".request_pagination .active a").attr("id"));
						searchRequest(page=page);
						requestInfo( history.state.id);
						$('.modal-backdrop').remove();
		  			}
		  			swal(data.title, data.message, data.alert);
			}, "json");
		});
	}
}

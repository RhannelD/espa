function loadReport(){
	$("#mainpanel").load("../Tab_Report/report.php", function(){
		searchTriggers();
		printReportConfirmation();

		if (history.state.page != undefined)		// reloaded page and search recent page
			searchReport(history.state.page);
		else										// regular search
			searchReport(); 
	});

	function searchTriggers() {
		// Search trigger by Search Icon
		$(".report_search_icon").click(function(){
			searchReport(); 
		});

		// Search trigger by Enter key
		$('.report_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchReport(); 
		    }
		});

		$('.input_order, .input_department, .input_rows, .input_report_type').on('change', function(){
			searchReport(); 
		});
	}

	// replacing/storing the report history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'report';

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'report', '');
	}

	// Searching the Report
	function searchReport(page = 1){
		historyReplacePage(page=page);
		var search 		= $(".report_search_input").val().trim();
		var order  		= $('.input_order').val().trim();
		var department  = $('.input_department').val().trim();
		var rows   		= $('.input_rows').val().trim();
		var report   	= $('.input_report_type').val().trim();

		$(".table_report").load("../Tab_Report/report_search.php", {
			search: 	search, 
			page: 		page,
			department: department,
			order: 		order,
			rows: 		rows,
			report: 	report
		}, function(){
			clickedreportPagination();
			viewStudentInfo();
			$(".table_report").collapse('show');
		});
	}

	// going the another histpry page trigger
	function clickedreportPagination(){
		$(".report_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchReport(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// view Student info
	function viewStudentInfo(){
		$('.student_information').click(function(e){
			e.preventDefault();

			var Sr_code = $(this).attr('id').trim();

			history.pushState({tab: 'student', id: Sr_code}, 'student', '');
			loadStudents();
		});
	}


	function printReportConfirmation(){
		$('.print_report').click(function(){
			swal({
	            title: 'Print Report?',
	            text: "",
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
	        		printReport();
	        	$('.to_be_remove').remove();
	        });
		});
	}

	// Printing report
	function printReport(){
		// Create a form
		var mapForm = document.createElement("form");
		mapForm.target = "_blank";    
		mapForm.method = "POST";
		mapForm.action = "../Tab_Report/report_print.php";
		mapForm.setAttribute("class", "to_be_remove d-none");

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "dept";
		mapInput.value = $('.input_department').val().trim();;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "search";
		mapInput.value = $(".report_search_input").val().trim();

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "order";
		mapInput.value = $('.input_order').val().trim();

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "department";
		mapInput.value = $('.input_department').val().trim();

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "rows";
		mapInput.value = $('.input_rows').val().trim();

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "report";
		mapInput.value = $('.input_report_type').val().trim();

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "page";
		mapInput.value = history.state.page;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Add the form to dom
		document.body.appendChild(mapForm);

		// Just submit
		mapForm.submit();
	}
}

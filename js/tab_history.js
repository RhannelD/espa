function loadHistory(){
	$("#mainpanel").load("../Tab_History/history.php", function(){
		show_date();
		searchTriggers();

		if (history.state.page != undefined)		// reloaded page and search recent page
			searchHistory(history.state.page);
		else										// regular search
			searchHistory(); 
	});

	function searchTriggers() {
		// Search trigger by Search Icon
		$(".history_search_icon").click(function(){
			searchHistory(); 
		});

		// Search trigger by Enter key
		$('.history_search_input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        searchHistory(); 
		    }
		});

		$('.input_order, .input_show, .input_date').on('change', function(){
			searchHistory(); 
		});
	}

	// Show Date picker if needed
	function show_date() {
		$('.input_show').on('change', function(){
			var value = $(this).val().trim();
			switch (value) {
				case 'after':
				case 'before':
					$('.input_date').removeAttr('disabled');
					return;
			}
			$('.input_date').attr('disabled', true);
		});
	}

	// replacing/storing the history history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'history';

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'history', '');
	}

	// Searching the Programs
	function searchHistory(page = 1){
		historyReplacePage(page=page);
		var search = $(".history_search_input").val().trim();
		var order  = $('.input_order').val().trim();
		var show   = $('.input_show').val().trim();
		var date   = $('.input_date').val().trim();

		$(".table_history").load("../Tab_History/history_search.php", {
			search: search, 
			page: 	page,
			order: 	order,
			show: 	show,
			date: 	date
		}, function(){
			clickedHistoryPagination();
			$(".table_history").collapse('show');
		});
	}

	// going the another histpry page trigger
	function clickedHistoryPagination(){
		$(".history_pagination .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchHistory(page=clickedPage);
			historyReplacePage(page = page);
		});
	}
}

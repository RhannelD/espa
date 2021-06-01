function loadProposalSlip(){
	$("#mainpanel").load("../Tab_Proposal_Slip/proposal.php", function(){
		show_date();
		searchTriggers();

		if (history.state.page != undefined)		// reloaded page and search recent page
			searchHistory(history.state.page);
		else										// regular search
			searchHistory(); 

		if (history.state.id != undefined)			// reloaded page and see previous info
			proposalInfo(history.state.id);
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
				case 'date':
					$('.input_date').removeAttr('disabled');
					return;
			}
			$('.input_date').attr('disabled', true);
		});
	}

	// replacing/storing the history history page
	function historyReplacePage(page=null){
		var state_var = {};
		state_var['tab'] = 'proposal';

		if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (page != null)
			state_var['page'] = page;
		else if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'proposal', '');
	}

	// replacing/storing the program history id 
	function historyReplaceID(id=null){
		var state_var = {};
		state_var['tab'] = 'proposal';

		if (id != null)
			state_var['id'] = id;
		else if (history.state.id != undefined)
			state_var['id'] = history.state.id;

		if (history.state.page != undefined)
			state_var['page'] = history.state.page;

		history.replaceState(state_var, 'proposal', '');
	}
	
	// Searching the Programs
	function searchHistory(page = 1){
		historyReplacePage(page=page);
		var search = $(".history_search_input").val().trim();
		var order  = $('.input_order').val().trim();
		var show   = $('.input_show').val().trim();
		var date   = $('.input_date').val().trim();


		$(".table_proposal").load("../Tab_Proposal_Slip/proposal_search.php", {
			search: search, 
			page: 	page,
			order: 	order,
			show: 	show,
			date: 	date
		}, function(){
			clickedHistoryPagination();
			clickedProposal();
			$(".table_proposal").collapse('show');
		});
	}

	// going the another histpry page trigger
	function clickedHistoryPagination(){
		$(".proposal_pagination	 .page-item a").click(function(){	
			var clickedPage = parseInt($(this).attr("id"));
			searchHistory(page=clickedPage);
			historyReplacePage(page = page);
		});
	}

	// Getting the info for viewing the selected/clicked proposal
	function clickedProposal(){
	    $(".proposal_table tbody .rows").click(function(e){
	    	var proposal_id = $(this).attr('id').trim();
	    	proposalInfo(proposal_id);
	    });	
	}

	// Viewing the info of the selected proposal
	function proposalInfo(proposal_id){
		$(".info_proposal").load("../Tab_Proposal_Slip/proposal_info.php", {
			id: proposal_id
		},function(){
			proposalDeleteConfirmation();
			historyReplaceID(id=proposal_id);
			$(".info_proposal").collapse('show');
		});
	}

	function unloadProposalInfo(){
		$(".info_proposal").collapse('hide');
		historyReplaceID(id=0);
	}

	// Asking confirmation if sure on deleting the selected proposal
	function proposalDeleteConfirmation(){
		$(".proposal_delete").click(function(e){	
			swal({
	            title: 'Are you sure?',
	            text: "Deleting Proposal Slip",
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
					proposalDelete(id);
	            }
	        });
		});
	}

	// Deleting the selected proposal
	function proposalDelete(id){
		$.post( "../Tab_Proposal_Slip/proposal_delete.php", {
			id: id
		}, function( data ) {
			swal(data.title, data.message, data.alert);
			if(data.alert=="success"){
				unloadProposalInfo();
				$(".proposal_table_row_"+id).remove();
			}
		}, "json");
	}
}

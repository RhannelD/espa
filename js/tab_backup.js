function loadBackup(){
	$("#mainpanel").load("../Tab_Backup/backup.php", function(){
		backupDatabase();
	});

	function backupDatabase(){
		$('.btn_backup').click(function(e){
			e.preventDefault();
			
			$.ajax({
		        url: '../Tab_Backup/backup_database.php',
		        method: 'POST',
		        data: { download: true} ,
		        xhrFields: {
		            responseType: 'blob'
		        },
		        success: function (data, textStatus, request) {
		        	var filename = "";
				    var disposition = request.getResponseHeader('Content-Disposition');
				    if (disposition && disposition.indexOf('attachment') !== -1) {
				        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
				        var matches = filenameRegex.exec(disposition);
				        if (matches != null && matches[1]) { 
				          filename = matches[1].replace(/['"]/g, '');
				        }
				    }

		            var a = document.createElement('a');
		            var url = window.URL.createObjectURL(data);
		            a.href = url;
		            a.download = filename;
		            document.body.append(a);
		            a.click();
		            a.remove();
		            window.URL.revokeObjectURL(url);

		            swal('Backup Successfully', '', 'success');
		        }
		    });
		});
	}
}
function loadDashboard(){
	$("#mainpanel").load("../Tab_Dashboard/dashboard.php", function(){
		loadChartGradesLine();
		loadChartStudentRequest();
		loadChartStudentGender();
		loadChartStudentGrade();
		loadChartStudentInc();
		loadChartStudentFailed();
		loadChartStudentDropped();
	});

	function loadChartGradesLine(){
		$.post( "../Tab_Dashboard/dashboard_line_data_records.php"
		, function( data ) {
			if(data.none){
				return;
			}

			new Chart("grades_line", {
			  	type: "line",
			  	data: {
				    labels: data.xValues,
				    datasets: [{ 
				    	label: 'Clean',
					    data: data.yValues['Clean'],
					    borderColor: "#00aba9",
					    backgroundColor: "#00aba9",
					    fill: false,
					    tension: 0.3
				    }, { 
				    	label: 'Dropped',
					    data: data.yValues['Dropped'],
					    borderColor: "#2b5797",
					    backgroundColor: "#2b5797",
					    fill: false,
					    tension: 0.3
				    }, { 
				    	label: 'Failed',
					    data: data.yValues['Failed'],
					    borderColor: "#b91d47",
					    backgroundColor: "#b91d47",
					    fill: false,
					    tension: 0.3
				    }, { 
				    	label: 'Inc',
					    data: data.yValues['Inc'],
					    borderColor: "#003333",
					    backgroundColor: "#003333",
					    fill: false,
					    tension: 0.3
				    }]
			  	},
			  	options: {
			  		responsive: true,
				    title: {
				      display: true,
				      text: "Students Performance Per Sem"
				    },
				    maintainAspectRatio: false,
	                scales: {
	                    yAxes: [{
	                        display: true,
	                        ticks: {
	                            beginAtZero: true,
	                            steps: 1,
	                            stepValue: 1,
	                            ticks: {
					                stepSize: 1
					            }
	                        }
	                    }]
	                },
	                tooltips: {
				      	mode: 'index',
				      	intersect: false,
				    },
					hover: {
				      mode: 'nearest',
				      intersect: true
				    }
			  	}
			});
			}, "json");
	}


	function loadChartStudentRequest(){
		$.post( "../Tab_Dashboard/dashboard_data_request.php"
		, function( data ) {
			if(data.none){
				return;
			}
			var barColors = [
			  "#00aba9",
			  "#b91d47",
			  "#2b5797"
			];

			new Chart("chart_student_request", {
			  type: "pie",
			  data: {
			    labels: data.xValues,
			    datasets: [{
			      backgroundColor: barColors,
			      data: data.yValues
			    }]
			  },
			  options: {
			    title: {
			      display: true,
			      text: "Students Request"
			    }
			  }
			});
		}, "json");
	}

	function loadChartStudentGender(){
		$.post( "../Tab_Dashboard/dashboard_data_gender.php"
		, function( data ) {
			if(data.none){
				return;
			}
			var barColors = [
			  "#00aba9",
			  "#b91d47"
			];

			new Chart("chart_student_gender", {
			  type: "pie",
			  data: {
			    labels: data.xValues,
			    datasets: [{
			      backgroundColor: barColors,
			      data: data.yValues
			    }]
			  },
			  options: {
			    title: {
			      display: true,
			      text: "Students Count by Gender"
			    }
			  }
			});
		}, "json");
	}

	function loadChartStudentGrade(){
		$.post( "../Tab_Dashboard/dashboard_data_grade.php"
		, function( data ) {
			if(data.none){
				return;
			}
			var barColors = [
			  "#00aba9",
			  "#003333",
			  "#2b5797",
			  "#b91d47"
			];

			new Chart("chart_student_grades", {
			  type: "pie",
			  data: {
			    labels: data.xValues,
			    datasets: [{
			      backgroundColor: barColors,
			      data: data.yValues
			    }]
			  },
			  options: {
			    title: {
			      display: true,
			      text: "Students Grade Records"
			    }
			  }
			});
		}, "json");
	}

	function loadChartStudentInc(){
		$.post( "../Tab_Dashboard/dashboard_data_inc.php"
		, function( data ) {
			if(data.none){
				return;
			}
			var barColors = [
			  "#00aba9",
			  "#003333"
			];

			new Chart("chart_student_inc", {
			  type: "pie",
			  data: {
			    labels: data.xValues,
			    datasets: [{
			      backgroundColor: barColors,
			      data: data.yValues
			    }]
			  },
			  options: {
			    title: {
			      display: true,
			      text: "Students with INC"
			    }
			  }
			});
		}, "json");
	}

	function loadChartStudentFailed(){
		$.post( "../Tab_Dashboard/dashboard_data_failed.php"
		, function( data ) {
			if(data.none){
				return;
			}
			var barColors = [
			  "#00aba9",
			  "#b91d47"
			];

			new Chart("chart_student_failed", {
			  type: "pie",
			  data: {
			    labels: data.xValues,
			    datasets: [{
			      backgroundColor: barColors,
			      data: data.yValues
			    }]
			  },
			  options: {
			    title: {
			      display: true,
			      text: "Students with Failed Grade"
			    }
			  }
			});
		}, "json");
	}

	function loadChartStudentDropped(){
		$.post( "../Tab_Dashboard/dashboard_data_dropped.php"
		, function( data ) {
			if(data.none){
				return;
			}
			var barColors = [
			  "#00aba9",
			  "#2b5797"
			];

			new Chart("chart_student_dropped", {
			  type: "pie",
			  data: {
			    labels: data.xValues,
			    datasets: [{
			      backgroundColor: barColors,
			      data: data.yValues
			    }]
			  },
			  options: {
			    title: {
			      display: true,
			      text: "Students with Dropped"
			    }
			  }
			});
		}, "json");
	}
}

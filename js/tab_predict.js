function loadStudentPredict(){
	if (history.state.id == null) {
		history.replaceState(null, '', '');
		openingPage();
		return;
	}

	$("#mainpanel").load("../Tab_Student_Predict_Track/student_predict.php", {
		sr_code: history.state.id
	}, function(){
		backToStudentInfo();
	});

	// Back to Student Tab
	function backToStudentInfo(){
		$('.student_open_back').click(function(){
			var id = $(this).attr('id').trim();
			history.pushState({tab: 'student', id: id}, 'student', '');
			loadStudents();
		});
	}
}

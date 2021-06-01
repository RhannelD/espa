function loadStudentGrade(){
	if (history.state.id == null) {
		history.replaceState(null, '', '');
		openingPage();
		return;
	}

	$("#mainpanel").load("../Tab_Student_Grade/student_grade.php", {
		sr_code: history.state.id
	}, function(){
		backToStudentInfo();
		printCurriculumConfirmation();
		openStudentAddGrade();
		printCurriculumGradeConfirmation();
	});

	// Back to Student Tab
	function backToStudentInfo(){
		$('.student_open_back').click(function(){
			var id = $(this).attr('id').trim();
			history.pushState({tab: 'student', id: id}, 'student', '');
			loadStudents();
		});
	}

	// Print Student Curriculum Confirmation
	function printCurriculumConfirmation(){
		$(".student_curriculum_print").click(function(e){
			e.preventDefault();
			var id = $(".student_curriculum_print").attr('id').trim();
			swal({
	            title: 'Print Blank Curriculum?',
	            text: "Printing Black Curriculum for \""+ id +"\"",
	            icon: 'warning',
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
	        		printingCurriculum(id);
	        	$('.to_be_remove').remove();
	        });
		});
	}

	// Print Student Curriculum without grade
	function printingCurriculum(id){
		// Create a form
		var mapForm = document.createElement("form");
		mapForm.target = "_blank";    
		mapForm.method = "POST";
		mapForm.action = "../pdf-generator/generate_curriculum.php";
		mapForm.setAttribute("class", "to_be_remove d-none");

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "sr_code";
		mapInput.value = id;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Add the form to dom
		document.body.appendChild(mapForm);

		// Just submit
		mapForm.submit();
	}

	// Print Stduent Curriculum with Grade Confirmation
	function printCurriculumGradeConfirmation(){
		$(".student_curriculum_grade_print").click(function(e){
			e.preventDefault();
			var id = $(".student_curriculum_print").attr('id').trim();
			swal({
	            title: 'Print Blank Curriculum?',
	            text: "Printing Black Curriculum for \""+ id +"\"",
	            icon: 'warning',
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
	        		printingCurriculumGrade(id);
	        	$('.to_be_remove').remove();
	        });
		});
	}

	// Print Student Curriculum with Grade
	function printingCurriculumGrade(id){
		// Create a form
		var mapForm = document.createElement("form");
		mapForm.target = "_blank";    
		mapForm.method = "POST";
		mapForm.action = "../pdf-generator/generate_curriculum.php";
		mapForm.setAttribute("class", "to_be_remove d-none");

		// Create an input
		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "sr_code";
		mapInput.value = id;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		var mapInput = document.createElement("input");
		mapInput.type = "text";
		mapInput.name = "grade";
		mapInput.value = true;

		// Add the input to the form
		mapForm.appendChild(mapInput);

		// Add the form to dom
		document.body.appendChild(mapForm);

		// Just submit
		mapForm.submit();
	}

	// Open Student Grades
	function openStudentAddGrade(){
		$(".student_add_grade").click(function(e){	
			var id = $(this).attr("id");
			history.pushState({tab: 'student_grade_add', id: id}, 'student_grade_add', '');
			loadStudentGradeAdd();
		});
	}
}

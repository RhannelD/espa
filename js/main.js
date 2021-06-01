 $(document).ready(function(){
 	openingPage();

	function openingPage(){
		if (history.state == null) {
			if ($.isFunction(window.loadDashboard)){
				history.replaceState({tab: 'dashboard'}, 'dashboard', '');
			} else {
				history.replaceState({tab: 'request'}, 'request', '');
			}
		}
		loadPage(history.state);
	}

	window.onpopstate = function (e) {
		loadPage(e.state);
	}

	function loadPage(state){
		switch (state.tab) {
			case 'program':
				if ($.isFunction(window.loadPrograms))
					loadPrograms();
				break;
			case 'course':
				if ($.isFunction(window.loadCourses))
					loadCourses();
				break;
			case 'department':
				if ($.isFunction(window.loadDepartments)) 
					loadDepartments();
				break;
			case 'department_chp_view':
				if ($.isFunction(window.loadDepartments_chp_view)) 
					loadDepartments_chp_view();
				break;
			case 'officer':
				if ($.isFunction(window.loadOfficers))
					loadOfficers();
				break;
			case 'curriculum':
				if ($.isFunction(window.loadCurriculums))
					loadCurriculums();
				break;
			case 'curriculum_open':
				if ($.isFunction(window.loadCurriculumOpen))
					loadCurriculumOpen();
				break;
			case 'curriculum_edit_course':
				if ($.isFunction(window.loadCurriculumEditCourses))
					loadCurriculumEditCourses();
				break;
			case 'student':
				if ($.isFunction(window.loadStudents))
					loadStudents();
				break;
			case 'student_grade':
				if ($.isFunction(window.loadStudentGrade))
					loadStudentGrade();
				break;
			case 'student_grade_add':
				if ($.isFunction(window.loadStudentGradeAdd))
					loadStudentGradeAdd();
				break;
			case 'student_evaluate':
				if ($.isFunction(window.loadStudentEvaluate))
					loadStudentEvaluate();
				break;
			case 'student_predict':
				if ($.isFunction(window.loadStudentPredict))
					loadStudentPredict();
				break;
			case 'history':
				if ($.isFunction(window.loadHistory))
					loadHistory();
				break;
			case 'report':
				if ($.isFunction(window.loadReport))
					loadReport();
				break;
			case 'account':
				if ($.isFunction(window.loadAccount))
					loadAccount();
				break;
			case 'proposal':
				if ($.isFunction(window.loadProposalSlip))
					loadProposalSlip();
				break;
			case 'request':
				if ($.isFunction(window.loadRequest))
					loadRequest();
				break;
			case 'dashboard':
				if ($.isFunction(window.loadDashboard))
					loadDashboard();
				break;
			case 'backup':
				if ($.isFunction(window.loadBackup))
					loadBackup();
				break;
			default:
				loadCurriculums();
				break;
		}
	}

	$("#tab_dashboard").click(function(){	
		if (history.state.tab != 'dashboard') {
			history.pushState({tab: 'dashboard'}, 'dashboard', '');
			loadDashboard();
		}
	});

	$("#tab_program").click(function(){	
		if (history.state.tab != 'program') {
			history.pushState({tab: 'program'}, 'program', '');
			loadPrograms();
		}
	});

	$("#tab_course").click(function(){
		if (history.state.tab != 'course') {
			history.pushState({tab: 'course'}, 'course', '');
			loadCourses();
		}
	});
	
	$("#tab_department").click(function(){
		if (history.state.tab != 'department') {
			history.pushState({tab: 'department'}, 'department', '');
			loadDepartments();
		}
	});

	$("#tab_department_chp_view").click(function(){
		if (history.state.tab != 'department_chp_view') {
			history.pushState({tab: 'department_chp_view'}, 'department_chp_view', '');
			loadDepartments_chp_view();
		}
	});

	$("#tab_department_officer").click(function(){
		if (history.state.tab != 'officer') {
			history.pushState({tab: 'officer'}, 'officer', '');
			loadOfficers();
		}
	});

	$("#tab_curriculum").click(function(){
		if (history.state.tab != 'curriculum') {
			history.pushState({tab: 'curriculum'}, 'curriculum', '');
			loadCurriculums();
		}
	});

	$("#tab_student_curriculum").click(function(){
		if (history.state.tab != 'student_grade') {	
			$.post( "../Standard_Functions/get_student_srcode.php"
			, function( data ) {
				var id = data.id;
				history.pushState({tab: 'student_grade', id: id}, 'student_grade', '');
				loadStudentGrade();
			}, "json");
		}
	});

	$("#tab_student_predict").click(function(){
		if (history.state.tab != 'student_grade') {	
			$.post( "../Standard_Functions/get_student_srcode.php"
			, function( data ) {
				var id = data.id;
				history.pushState({tab: 'student_predict', id: id}, 'student_predict', '');
				loadStudentPredict();
			}, "json");
		}
	});

	$("#tab_student").click(function(){
		if (history.state.tab != 'student') {
			history.pushState({tab: 'student'}, 'student', '');
			loadStudents();
		}
	});

	$("#tab_history").click(function(){
		if (history.state.tab != 'history') {
			history.pushState({tab: 'history'}, 'history', '');
			loadHistory();
		}
	});

	$("#tab_report").click(function(){
		if (history.state.tab != 'report') {
			history.pushState({tab: 'report'}, 'report', '');
			loadReport();
		}
	});

	$("#tab_account").click(function(){
		if (history.state.tab != 'account') {
			history.pushState({tab: 'account'}, 'account', '');
			loadAccount();
		}
	});

	$("#tab_proposal").click(function(){
		if (history.state.tab != 'proposal') {
			history.pushState({tab: 'proposal'}, 'proposal', '');
			loadProposalSlip();
		}
	});

	$("#tab_request").click(function(){
		if (history.state.tab != 'request') {
			history.pushState({tab: 'request'}, 'request', '');
			loadRequest();
		}
	});

	$("#tab_backup").click(function(){
		if (history.state.tab != 'backup') {
			history.pushState({tab: 'backup'}, 'backup', '');
			loadBackup();
		}
	});

	$('.sign_out').click(function(){
		$(this).children().attr('class', 'fad fa-spinner-third fa-spin');

		$.post( "../main/main_sign_out.php", function( data ) {
			if(data.alert == "success"){
				window.location.href = data.panel;
				return;
			}
			$('.sign_out').children().attr('class', 'fas fa-sign-out');
	  		swal(data.title, data.message, data.alert);
		}, "json");
	});	
});

function changeDepartmentIcon(){
	var src = $('.top-right-icon').attr('src');
	$('.top-right-icon').attr('src',src);
}

function changeAccountNameDisplay(name){
	$('.account_info_name').text(name);
}


function showingNotShowingPassword(){
	$(".show_password").on('click', function(event) {
        event.preventDefault();
        if($(this).parent().siblings('input').attr("type") == "password"){
            $(this).parent().siblings('input').attr('type', 'text');
            $(this).children('i').removeClass( "fa-eye-slash" );
            $(this).children('i').addClass( "fa-eye" );
            return;
        }
        $(this).parent().siblings('input').attr('type', 'password');
        $(this).children('i').addClass( "fa-eye-slash" );
        $(this).children('i').removeClass( "fa-eye" );
    });
}  

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/jquery.validate.min.js"></script>
    <script src="../bootstrap-4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="../library-chartjs/Chart.min.js"></script>

    <script src="../js/main.js"></script>
    <?php  
    if (in_array($UserType, array('ADM'))) {
        ?>
        <script src="../js/tab_department.js"></script>
        <?php
    }
    if (in_array($UserType, array('CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_department_chp_view.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP'))) {
        ?>
        <script src="../js/tab_department_officer.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_curriculum.js"></script>
        <script src="../js/tab_curriculum_open.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_program.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_course.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_student.js"></script>
        <script src="../js/tab_student_grade_add.js"></script>
        <script src="../js/tab_student_evaluate.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL', 'STD'))) {
        ?>
        <script src="../js/tab_student_grade.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_history.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL', 'STD'))) {
        ?>
        <script src="../js/tab_proposal_slip.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL', 'STD'))) {
        ?>
        <script src="../js/tab_request.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_dashboard.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM'))) {
        ?>
        <script src="../js/tab_backup.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL', 'STD'))) {
        ?>
        <script src="../js/tab_predict.js"></script>
        <?php
    }
    if (in_array($UserType, array('ADM', 'CHP', 'EVL'))) {
        ?>
        <script src="../js/tab_report.js"></script>
        <?php
    }
    ?>
    <script src="../js/tab_account.js"></script>
    
    <!-- Menu Toggle Script -->
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>

</body>

</html>
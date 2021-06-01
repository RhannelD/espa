<?php  
    include "../database/UserAuth.php";
    include '../database/verify_if_user_has_logout.php';
?>

<div class="container container-fluid">
    <hr>
    <div class="row">
        <div class="col-12">
            <canvas id="grades_line" style="width:100%; max-height: 300px"></canvas>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3">
            <canvas id="chart_student_request" style="width:100%"></canvas><hr>
        </div>
        <div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3">
            <canvas id="chart_student_grades" style="width:100%"></canvas><hr>
        </div>
        <div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3">
            <canvas id="chart_student_gender" style="width:100%"></canvas><hr>
        </div>
        <div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3">
            <canvas id="chart_student_inc" style="width:100%"></canvas><hr>
        </div>
        <div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3">
            <canvas id="chart_student_failed" style="width:100%"></canvas><hr>
        </div>
        <div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3">
            <canvas id="chart_student_dropped" style="width:100%"></canvas><hr>
        </div>
    </div>
</div>
<?php  
    if (!empty($_REQUEST['course'])) {
        $course_data = $_REQUEST['course'];
    }
?>

<tr class='course_added' id='<?php echo $course_data['Course_ID']; ?>'>
    <td class='text-nowrap'><?php echo $course_data['Course_Code']; ?></td>
    <td class='text-nowrap'><?php echo $course_data['Course_Title']; ?></td>
    <td class='text-nowrap text-center course_added_units'><?php echo $course_data['Units']; ?></td>
    <td class='text-nowrap text-center'><?php echo $course_data['Lecture']; ?></td>
    <td class='text-nowrap text-center'><?php echo $course_data['Laboratory']; ?></td>
    <td class='text-nowrap text-center'>
        <button type='button' class='btn btn-danger btn-sm remove_course_added'>
            <i class='fas fa-trash'></i>
            Remove
        </button>
    </td>
</tr>
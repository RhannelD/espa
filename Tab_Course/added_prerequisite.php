<?php 
	if(empty($_REQUEST['prereq_id'])) {
		exit();
	}
	$id = $_REQUEST['prereq_id']; 
	$code = $_REQUEST['prereq_code']; 
	$title = $_REQUEST['prereq_title']; 
	
?>
<tr class="item-prereq-<?php echo htmlspecialchars($id); ?>" id="<?php echo htmlspecialchars($id); ?>">
	<td class="text-nowrap"><?php echo htmlspecialchars($code); ?></td>
	<td class="text-nowrap"><?php echo htmlspecialchars($title); ?></td>
	<td class="text-nowrap text-danger delete-prereq" id="<?php echo $id; ?>"><i class="fas fa-trash"></i></td>
</tr>

<?php
$sql = "UPDATE `{$db_tb_incomings}` SET 'saved' = '$_POST['$saved']' WHERE name_to = '$row['name_to']';";
$result = $db->db_query($sql)
or error(GENERAL_ERROR, 'Could not delete incomings information.', '', __FILE__, __LINE__, $sql);
?>
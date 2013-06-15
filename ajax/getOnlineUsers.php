<?php

function getOnlineUsers()
{
    global $db, $db_tb_sid, $db_tb_user, $config_counter_timeout, $user_fremdesitten, $user_allianz;

    $returnData                    = array();
    $returnData['numOnlineMember'] = 0;
    $returnData['aOnlineMember']   = array();

    $sql = "SELECT DISTINCT `id` FROM `{$db_tb_sid}` WHERE `date`>" . (CURRENT_UNIX_TIME - $config_counter_timeout);
    if (!$user_fremdesitten) {
        $sql .= " AND (SELECT `allianz` FROM `{$db_tb_user}` WHERE `{$db_tb_sid}`.`id`=`{$db_tb_user}`.`id`)='" . $user_allianz . "';";
    }
    $result = $db->db_query($sql);
    while ($row = $db->db_fetch_array($result)) {
        $returnData['numOnlineMember']++;
        $returnData['aOnlineMember'][] = $row['id'];
    }

    $returnData['strOnlineMember'] = implode(", ", $returnData['aOnlineMember']);

    return $returnData;
}
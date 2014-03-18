<?php

function delete_user($strUsername)
{
    global $user_status;
    global $db, $db_tb_user, $db_tb_punktelog, $db_tb_schiffe, $db_tb_preset, $db_tb_lager, $db_tb_ressuebersicht, $db_tb_research2user, $db_tb_group_user, $db_tb_group_sort, $db_tb_gebaeude_spieler, $db_tb_bestellung, $db_tb_user_research;

    $strUsername = $db->escape($strUsername);

    doc_title('Account löschen');

    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    $sql = "DELETE FROM " . $db_tb_user . " WHERE sitterlogin='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_punktelog . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_schiffe . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_preset . " WHERE fromuser='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_lager . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_ressuebersicht . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_research2user . " WHERE userid='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_group_user . " WHERE user_id='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_group_sort . " WHERE user_id='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_gebaeude_spieler . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_bestellung . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    $sql = "DELETE FROM " . $db_tb_user_research . " WHERE user='" . $strUsername . "';";
    $db->db_query($sql);

    doc_message('Account ' . $strUsername . ' gelöscht!');

}
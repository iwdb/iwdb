<?php
/*
 * File and License information
 */

/**
 * short description
 *
 * long
 * description
 *
 * @author     masel <masel789@googlemail.com>
 * @copyright  masel <masel789@googlemail.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU GPL version 2 or any later version
 * @package
 * @subpackage
 */

if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}
/*****************************************************************************/
?>
<tr>
    <td class="windowbg1">
        <br>
        <?php
        include("help/rules.htm");
        ?>
        <br>
        <br>
        <form method="POST" action="index.php?action=rules" enctype="multipart/form-data">
            <input type="submit" value="akzeptieren" name="accept_rules">
        </form>
        <br>
    </td>
</tr>
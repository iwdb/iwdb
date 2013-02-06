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
 * @author masel <masel789@googlemail.com>
 * @copyright masel <masel789@googlemail.com>
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
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="body">
<table style="width: 100%">
    <tr>
        <td class="windowbg1">
            <?php
            include("help/rules.htm");
            ?>
            <br>
            <form method="POST" action="index.php?action=rules" enctype="multipart/form-data">
                <input type="submit" value="akzeptieren" name="accept_rules">
            </form>
        </td>
    </tr>
</body>
</html>
<?php

function errorHandler($errno, $errstr)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return '';
    }

    //ToDo: Limit logging?

    switch ($errno) {
        case E_USER_ERROR:
            $logfilename = 'error_' . microtime(true) . '.log';

            echo "<div class='system_error'>ERROR: $errstr<br>";
            echo "Aborting...</div><br>";
            echo "<div class='system_notification'>Weitere Informationen sind im Ordner log in der Datei '$logfilename' zu finden.</div>";

            file_put_contents(LOG_PATH . $logfilename, print_r(debug_backtrace(), true), LOCK_EX);
            exit(1);
            break;

        case E_WARNING:
        case E_USER_WARNING:
            $logfilename = 'warning_' . microtime(true) . '.log';

            echo "<div class='system_warning'>WARNING: $errstr<br>";
            echo "<div class='system_notification'>Weitere Informationen sind im Ordner log in der Datei '$logfilename' zu finden.</div>";

            file_put_contents(LOG_PATH . $logfilename, print_r(debug_backtrace(), true), LOCK_EX);
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            $logfilename = 'notice_' . microtime(true) . '.log';
            file_put_contents(LOG_PATH . $logfilename, print_r(debug_backtrace(), true), LOCK_EX);
            break;

        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $logfilename = 'deprecated_' . microtime(true) . '.log';
            file_put_contents(LOG_PATH . $logfilename, print_r(debug_backtrace(), true), LOCK_EX);
            break;

        default:
            $logfilename = 'unknown_error_' . microtime(true) . '.log';

            echo "<div class='system_warning'>Unknown error type: [$errno] $errstr<br>";
            echo "<div style='system_notification'>Weitere Informationen sind im Ordner log in der Datei '$logfilename' zu finden.</div>";

            file_put_contents(LOG_PATH . $logfilename, print_r(debug_backtrace(), true), LOCK_EX);
            break;
    }

    /* Don't execute PHP internal error handler */

    return true;
}
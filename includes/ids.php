<?php

ini_set('include_path', dirname(__FILE__));

require_once './includes/IDS/Init.php';

$aParameters = array(
    'GET'    => $_GET,
    'POST'   => $_POST,
    'COOKIE' => $_COOKIE
);

$objIDSInit                                     = IDS_Init::init(dirname(__FILE__) . '/IDS/Config/Config.ini');
$objIDSInit->config['General']['base_path']     = dirname(__FILE__) . '/IDS/';
$objIDSInit->config['General']['use_base_path'] = true;

$objIDSInit->config['Logging']['path']          = '../log/ids/phpids.log';

$objIDS       = new IDS_Monitor($aParameters, $objIDSInit);
$objIDSResult = $objIDS->run();

if (!$objIDSResult->isEmpty()) {

    if ((!empty($phpids_log_impact)) AND ($objIDSResult->getImpact() >= $phpids_log_impact)) {

        $time = microtime(true);

        $report = array(
            'ip'         => REMOTE_IP,
            'time'       => date('d.M.Y H:i:s', (int)$time),
            'impact'     => $objIDSResult->getImpact(),
            'IDS_result' => (string)$objIDSResult
        );

        file_put_contents(LOG_PATH . 'ids/ids_impact_'.$objIDSResult->getImpact().'_'.$time.'.log.php', '<?php $report = ' . var_export($report, true) . ';', FILE_APPEND|LOCK_EX);
    }

    if ((!empty($phpids_block_impact)) AND ($objIDSResult->getImpact() >= $phpids_block_impact)) {
        exit('Hacking attempt...');
    }

}
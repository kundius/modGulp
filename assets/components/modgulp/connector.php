<?php
// For debug
ini_set('display_errors', 1);
ini_set('error_reporting', -1);
// Load MODX config
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var modGulp $modGulp */
$modGulp = $modx->getService('modgulp', 'modGulp', $modx->getOption('modgulp_core_path', null,
        $modx->getOption('core_path') . 'components/modgulp/') . 'model/modgulp/'
);
$modx->lexicon->load('modgulp:default');

// handle request
$corePath = $modx->getOption('modgulp_core_path', null, $modx->getOption('core_path') . 'components/modgulp/');
$path = $modx->getOption('processorsPath', $modGulp->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
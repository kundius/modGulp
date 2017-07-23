<?php
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
		    $modGulp = $modx->getService('modgulp','modGulp',$modx->getOption('modgulp_core_path',null,$modx->getOption('core_path').'components/modgulp/').'model/modgulp/');
        $modx->controller->addLexiconTopic('modgulp:default');
        $modx->controller->addHtml("<script>var modGulpConfig = " . $modx->toJSON($modGulp->config) . ";</script>");
        $modx->controller->addCss($modGulp->config['cssUrl'] . 'mgr/main.css');
        $modx->controller->addJavascript($modGulp->config['jsUrl'] . 'mgr/widgets/gulp.button.js');
        break;
}

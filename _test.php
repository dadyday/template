<?php
	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once('cfg.php');
	require_once('class.template.php');


	$oTmpl = new TemplateBase('test');
	$oTmpl->display();

	echo dump($oTmpl);
?>
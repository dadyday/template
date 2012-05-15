<?php
	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once('cfg.php');
	require_once('class.templateparser.php');
	
	class Base implements ITemplateBase {
		
		function getTemplateSource($name) {
			$text = 'content of "'.$name.'"';
			if ($name == 'w') $text = 'wrapstart #content; wrapend';
			return $text;
		}
	}
		
	$oBase = new Base;


	try {		
		$oParser = new TemplateParser($oBase);
		echo $oParser->parse('#wrap:w; #embed:test; #end;');
		
		$oParser = new TemplateParser($oBase);
		echo $oParser->parse('#wrap:w; #bla:test; #end;');
	}
	catch(TemplateException $e) {
		echo $e->debug();
	}

?>
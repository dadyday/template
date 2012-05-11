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
			$oSrc = new Src($text);
			return $oSrc;
		}
	}
		
	class Src implements ITemplateSource {

		function __construct($text) {
			$this->oSource = new ParseSource($text);
		}
		
		function getParseSource() {	
			return $this->oSource;
		}
	}

	$oBase = new Base;


	try {
		$oParser = new TemplateParser($oBase);
		$oSrc = new Src('#wrap:w; #embed:test; #end;');
		echo $oParser->parse($oSrc);
		
		$oParser = new TemplateParser($oBase);
		$oSrc = new Src('#wrap:w; #bla:test; #end;');
		echo $oParser->parse($oSrc);
	}
	catch(TemplateException $e) {
		echo $e->debug();
	}

?>
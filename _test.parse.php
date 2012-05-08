<?php
	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once('cfg.php');
	require_once('class.templateparser.php');
	
	class Base implements ITemplateBase {
		
		function getTemplateSource($name) {
			$text = 'abc $a; '.$name;
			if ($name == 'w') $text = 'wrap #content; wrapend';
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

	$oParser = new TemplateParser($oBase);
	$result = $oParser->parse(new Src('#wrap:w; start #embed:test; end #end;'));
	echo dump($result);

?>
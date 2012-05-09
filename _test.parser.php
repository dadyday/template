<?php
	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once('cfg.php');
	require_once('class.parser.php');

	class TestParser extends Parser {
		
		function __construct() {
			parent::__construct($this);
			//$this->setHandlerObject($this);
			//$this->addHandler('_text', '_text');
			$this->addTokenHandler('/<(\w+)\s*\/>/', '_tagSingle');
			$this->addTokenHandler('/<(\w+)\s*>/', '_tagOpen');
			$this->addTokenHandler('/<\/(\w+)\s*>/', '_tagClose');
			
			$this->oRoot = new stdClass();
			$this->oRoot->aChild = array();
			$this->oNode = $this->oRoot;
		}
		
		function _text($text) {
			$this->addText($text);
			return '"'.$text.'"';
		}
		function _tagOpen($name, $attribs = '') {
			$this->openNode($name);
			return '['.$name.':'.$this->parse();
		}
		function _tagSingle($name, $attribs = '') {
			$this->openNode($name);
			$this->closeNode();
			return '['.$name.']';
		}
		function _tagClose($name) {
			$this->closeNode();
			$this->stop();
			return ']';
		}
		
		
		var $oRoot = null;
		var $oNode = null;
		function openNode($name) {
			$oNode = new stdClass();
			$oNode->name = $name;
			$oNode->oParent = $this->oNode;
			$oNode->aChild = array();
			$this->oNode->aChild[] = $oNode;
			$this->oNode = $oNode;
		}
		function closeNode() {
			$this->oNode = $this->oNode->oParent;
		}
		function addText($text) {
			$this->oNode->aChild[] = $text;
		}
	}


	$text = 'text <tag>0<test/> content <inner /> </tag> text';
	
	try {
		$oParser = new TestParser();
		$parsed = $oParser->parse($text);	
		
		echo dump($parsed);
		echo dump($oParser);
	}
	catch(Exception $e) {
		echo pre($e->getMessage());
	}
	
	
	
	
	

?>
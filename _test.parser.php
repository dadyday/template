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
			
			$this->newSection();
			$this->addTokenHandler('/<(\w+)\s*/', '_tagOpen');
			$this->addTokenHandler('/<\/(\w+)\s*>/', '_tagClose');
			$this->saveSection('tags');

			$this->newSection();
			$this->addTokenHandler('/(\w+)="([^"]*)"\s*/', '_tagAttrib');
			$this->addTokenHandler('/>/', '_tagEnd');
			$this->saveSection('attribs');

			$this->setSection('tags');
			
			$this->oRoot = new stdClass();
			$this->oRoot->aChild = array();
			$this->oNode = $this->oRoot;
		}
		
		var $aTokenDefList = array();
		var $aTokenDefStack = array();
		
		function newSection() {
			$this->aTokenDef = array();
		}
		function saveSection($name) {
			$this->aTokenDefList[$name] = $this->aTokenDef;
		}
		function setSection($name) {
			$this->aTokenDef = $this->aTokenDefList[$name];
		}
		
		function parseSection($name) {
			array_push($this->aTokenDefStack, $this->aTokenDef);
			$this->setSection($name);
			$result = $this->parse();
			$this->aTokenDef = array_pop($this->aTokenDefStack);
			return $result;
		}
		
		
		var $ind = 1;
		function getInd($off = 0) {
			return "\n".str_repeat('__', $this->ind+$off);
		}
		
		function _tagOpen($name) {
			$this->openNode($name);
			$this->ind++;
			$attr = $this->parseSection('attribs');
			$cont = $this->parseSection('tags');
			$this->ind--;
			$this->closeNode();
			return $this->getInd().$name.'('.$attr.') {'.$cont.$this->getInd().'};';
		}
		function _tagAttrib($name, $value) {
			$this->addAttrib($name, $value);
			return $name.'='.$value;
		}
		function _tagEnd() {
			$this->stop();
			return false;
		}
		function _text($text) {
			$this->addText($text);
			return $this->getInd().'"'.$text.'"';
		}
		function _tagClose($name) {
			$this->stop();
			return false;
		}
		
		
		var $oRoot = null;
		var $oNode = null;
		function openNode($name) {
			$oNode = new stdClass();
			$oNode->name = $name;
			$oNode->oParent = $this->oNode;
			$oNode->aAttrib = array();
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
		function addAttrib($name, $value) {
			$this->oNode->aAttrib[$name] = $value;
		}
	}


	$text = 'text <tag>0<test/> content <inner /> </tag> text';
	$text = '<html><body color="red">text!</body></html>';
	
	try {
		$oParser = new TestParser();
		$oParser->logging = 1;
		$parsed = $oParser->parse($text);	
		
		echo '<pre>'.$parsed.'</pre>';
		echo dump($oParser->oRoot,null,3);
		echo dump($oParser);
		echo dump(get_included_files());
	}
	catch(Exception $e) {
		echo pre($e->getMessage());
	}
	
	
	
	
	

?>
<?php


	class TemplateFile implements ITemplateSource {

		var $oBase;
		var $name = '';
		
		var $sourceFileName = '';
		var $parsedFileName = '';
		
		var $oSource = null;
		var $parsed = null;
		
		function __construct(ITemplateBase $oBase, $name) {
			$this->oBase = $oBase;
			$this->name = $name;
		}
		
	// processing and ITemplateBase hooking
		function loadSource() {
			$this->sourceFileName = $this->oBase->getSourceFileName($this->name);
			if (!file_exists($this->sourceFileName)) throw new TemplateException($this, 'source file "%s" not found', $this->sourceFileName);
			$source = file_get_contents($this->sourceFileName);
			return $source;
		}
		
		function parse($source) {                                  
			$this->oParser = $this->oBase->getParserObject();
			$this->oSource = new ParseSource($source);
			$parsed = $this->oParser->parse($this);	
			return $parsed;
		}
		
		function saveParsed($parsed) {
			$this->parsedFileName = $this->oBase->getParsedFileName($this->name);
			file_put_contents($this->parsedFileName, $parsed);
		}
		
		function parseFile() {
			try {
				$source = $this->loadSource();
				$parsed = $this->parse($source);
				$this->saveParsed($parsed);
			}
			catch(Exception $e) {
				$this->oBase->handleException($e);
				//echo dump($this);
			}
			return $this->parsedFileName;
		}
		
	// callback fpr class Parser
		function getParseSource() {
			return $this->oSource;
		}
		
	// error handling
		function debug(Exception $e) {
			echo dump($e);
			echo dump($this);
		}
		
		function debugSourceLine() {
			$file = $this->sourceFileName;
			$src = $this->oSource->source;
			$pos = $this->oSource->matchPos;
			$len = $this->oSource->matchLen;
			
			$line = $this->oSource->getLine($pos, $row, $col);
			
			$l = substr($line, 0, $col); 
			$m = substr($line, $col, $len);
			$r = substr($line, $col+$len); 
			return sprintf("file: %s #%d\npos:  %s[%s] #%d\nline: %s%s%s", $file, $row+1, str_repeat(' ',$col), str_repeat('-',$len-2), $col+1, $l, $m, $r);
		}
		
	}

?>
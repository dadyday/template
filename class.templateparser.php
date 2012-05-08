<?php
	require_once('class.parser.php');
	require_once('class.templatelinker.php');
	require_once('class.templateparam.php');
	require_once('class.templateblock.php');
	require_once('class.templateexception.php');

	require_once(PATH_LIB_COMMON . 'init.tools_regexp.php');
	require_once('init.interface.php');
	
	
	class TemplateParser {

	// instance
		var $oBase = null;
		var $oParser = null;
		var $oSource = null;
		var $aSourceStack = array();
		var $oBlockStack = null;

		function __construct(ITemplateBase $oBase) {
			$this->oBase = $oBase;
			$this->_logging = method_exists($oBase, 'logOpen');
			$this->oParser = new Parser($this);
			$this->oBlockStack = new BlockStack($this);
			$this->initToken();
		}
		
		function initToken() {
			$this->oParser->addTokenHandler('/\$(\w[\w\.\$-]*);?/', '_var');
			$this->oParser->addTokenHandler('/\$(\w[\w\.\$-]*)(\:|\s+)((?:\\\\.|.)*?);/', '_var');
			
			$this->oParser->addTokenHandler('/(?:^\s*)?#([\w]+)()();(\r?\n)?/m', '_func');
			$this->oParser->addTokenHandler('/(?:^\s*)?#([\w]+)(\:|\s+)((?:\\\\.|.)*?);(\r?\n)?/m', '_func');
			//$this->oParser->addTokenHandler('/(?:^\s*)?#([\w]+)(?:(\:)([^;]*))?;(\r?\n)?/m', '_func');
			//$this->oParser->addTokenHandler('/(?:^\s*)?#([\w]+)( )([^#]*);(\r?\n)?/m', '_func');	// haml style
			
			$this->oParser->addTokenHandler('/(#[a-f0-9]{3})\b/i', '_text');
			$this->oParser->addTokenHandler('/(#[a-f0-9]{6})\b/i', '_text');
			
			$this->oParser->addTokenHandler('/\$(\$)/', '_text');
			$this->oParser->addTokenHandler('/#(#)/', '_text');
		}
		
	// hooking
		function getTemplateSource($name) {
			return $this->oBase->getTemplateSource($name);
		}
		
		function logOpen() {
			if (!$this->_logging) return;
			$aArgs = func_get_args();
			return call_user_method_array('logOpen', $this->oBase, $aArgs);
		}
		function logClose() {
			if (!$this->_logging) return;
			$aArgs = func_get_args();
			return call_user_method_array('logClose', $this->oBase, $aArgs);
		}
		
		
		function parse(ITemplateSource $oSource = null) {
			if ($oSource) array_push($this->aSourceStack, $oSource->getParseSource());
			
			
			$oParseSource = end($this->aSourceStack);
			$parsed = $this->oParser->parse($oParseSource);
			//echo dump($this->aSourceStack);
			//$parsed = TemplateLinker::run($parsed);
			array_pop($this->aSourceStack);				
			return $parsed;
		}

		
	// parsing 
/*
		function parseFile(ITemplateFile $oTemplate) {
			$this->logOpen('parsing template '.$oTemplate->name);
			$this->oTemplate = $oTemplate;
			$parsed = $this->parse($oTemplate->getSource());
			$parsed = TemplateLinker::run($parsed);
			$this->logClose('parse end template '.$oTemplate->name);
			return $parsed;
		}
		function parseText($text) {
			$this->logOpen('parsing text');
			$parsed = $this->oParser->parse($text);
			$parsed = TemplateLinker::run($parsed);
			$this->logClose('parse end text '.$oTemplate->name);
			return $parsed;
		}
		
		protected function parse($oSource = null) {
			if (is_null($oSource)) $oSource = $this->oTemplate->oSource;
			if (!is_a($oSource, 'ParseSource')) $oSource = new ParseSource($oSource);
			$parsed = $this->oParser->parse($oSource);
			return $parsed;
		}
		protected function parseInline(ITemplateFile $oTemplate) {
			$oldTemplate = $this->oTemplate;
			$parsed = $this->parseFile($oTemplate);
			$this->oTemplate = $oldTemplate;
			return $parsed;
		}

		protected function xxx__getNewInst($template) {
			$class = get_class($this);
			$oParser = new $class($this->oBase);
			$oParser->oParent = $this;
			$oParser->oBlockStack = $this->oBlockStack;
			$oParser->oTemplate = $this->oBase->getTemplateFile($template);
			return $oParser;
		}
		protected function xxx__parseNewInst($template = null) {
			$oParser = $this->getNewInst($template);
			$this->logOpen('parsing new instance '.$template);
			$ret = $oParser->parse();
			$this->logClose('parse end new instance '.$template);
			return $ret;
		}
*/	
	
	
	
	// parser primär handler
	// ************************************************************
		function _text($text) {
			return $text;
		}
		
		function _func($func, $delimiter = '', $params = null, $lf = '') {
			$aParams = $this->_splitParams($params, $delimiter);
			
			$this->inline = true;
			$ret = $this->_invoke($func, $aParams, $result);
			if (!$this->inline) $result .= $lf;
			
			if (!$ret) throw new TemplateException($this, 'func "%s/%d" not found', $func, count($aParams));
			return $result;
		}
		
	// dispatcher
		function _invoke($func, $aParams = array(), &$result) {
			$ret = false;
			$paramCount = count($aParams);
			$log = "func: $func/$paramCount";
			for (; $paramCount >= 0; $paramCount--) {
				if ($funcDef = $this->_find($func . $paramCount)) break; 
			};
			if (!$funcDef) $funcDef = $this->_find($func);
			if ($funcDef) {
				$this->logOpen("$log -> ".$funcDef[1]);
				$result = $this->_call($funcDef, $aParams);
				$ret = true;
				$this->logClose();
			}
			return $ret;
		}
		
		function _splitParams($params, $delimiter = ':') {
			switch ($delimiter) {
				case ':':	$aParams = preg_split('/,/', $params); break;
				case ' ':	$aParams = preg_split('/ /', $params); break;
				default:	$aParams = array(); //preg_split('/'.$delimiter.'/', $params); break;
			};
			foreach($aParams as $i => $param) $aParams[$i] = preg_replace('|\\\\(.)|', '\\1', $param);
			return $aParams;
		}
		function _joinParams($aParams, $secound = false) {
			$params = '';
			foreach($aParams as $i => $param) {
				if (empty($param)) continue;
				if ($params || $secound) $params .= ', ';
				$params .= $this->asPhp($param);
			}
			return $params;
		}
		
		function _find($func) {
			if ($funcDef = $this->_exists($this, $func)) return $funcDef;
			//if ($funcDef = $this->_exists($this, $func . 'Start')) return $funcDef;
			if ($funcDef = $this->_exists($this, $func . 'Func')) return $funcDef;
			//if ($funcDef = $this->_exists($this->oTemplate, $func)) return $funcDef;
			if ($funcDef = $this->_exists(null, $func)) return $funcDef;
			return false;
		}
		
		function _exists($obj, $func) {
			if (is_array($func)) list($obj, $func) = $func;
			if (is_null($obj) && function_exists($func)) return array(null, $func);
			return method_exists($obj, $func) ? array($obj, $func) : false;
		}
		
		function _call($funcDef, $aParams = array()) {
			if (is_string($funcDef)) $funcDef = array($this, $funcDef);
			if (is_null($funcDef[0])) $funcDef = $funcDef[1];
			return call_user_func_array($funcDef, $aParams);
		}
		
	// test
		function testCont($param) {
			$aParam = func_get_args();
			$ret = '';
			foreach($aParam as $param) {
				$param = $this->asCont($param);
				$ret .= $param;
			}
			$this->inline = 0;
			return $ret;
		}

		
	// block funcs
		function contentFunc() {
			if (!$oBlock = $this->oBlockStack->get()) throw new TemplateException($this, 'no block defined for #content');
			if ($this->_invoke($oBlock->type . 'Content', array(), $result)) return $result;
			throw new Exception(sprintf('func "%sContent/%d" not found', $oBlock->type, 0));
			return false;
		}
		
		function elseFunc() {
			if (!$oBlock = $this->oBlockStack->get()) throw new TemplateException($this, 'no block defined for #else');
			if ($this->_invoke($oBlock->type . 'Else', array(), $result)) return $result;
			throw new Exception(sprintf('func "%sElse/%d" not found', $oBlock->type, 0));
			return false;
		}
		
		function endFunc() {
			if (!$oBlock = $this->oBlockStack->get()) throw new TemplateException($this, 'no block defined for #end');
			if ($this->_invoke($oBlock->type . 'End', array(), $result)) return $result;
			//throw new Exception(sprintf('func "%sEnd/%d" not found', $func, 0));
			$this->oBlockStack->pop();
			return false;
		}
		

	
	// if
		function if1($condition) {
			$condition = $this->asPhp($condition);
			
			$oBlock = $this->oBlockStack->push('if');
			$oBlock->elseBlock = '';
			$oBlock->elseIfBlock = array();
			$oBlock->ifBlock = $this->parse();
			
			$code = "\nif($condition):" . $this->toHtml($oBlock->ifBlock);
			foreach ($oBlock->elseIfBlock as $elseif) {
				list($cond, $cont) = $elseif;
				$code .= "\nelseif($cond):" . $this->toHtml($cont);
			}
			if ($oBlock->elseBlock) {
				$code .= "\nelse:" . $this->toHtml($oBlock->elseBlock);
			}
			$code .= "\nendif;";
			return $this->toPhp($code);
		}
		function if2($condition, $ifContent, $elseContent = null) {
			$condition = $this->asPhp($condition);
			$ifContent = $this->asCont($ifContent);
			$elseContent = $this->asCont($elseContent);
			
			$code = "\nif($condition):" . $this->toHtml($ifContent);
			if ($elseContent) {
				$code .= "\nelse:" . $this->toHtml($elseContent);
			}
			$code .= "\nendif;";
			return $this->toPhp($code);
		}
		
		function ifElse0() {
			$oBlock = $this->oBlockStack->get('if');
			$oBlock->elseBlock = $this->parse();
			return false;
		}
		
		function elseif1Func($condition) {
			$condition = $this->asPhp($condition);
			
			$oBlock = $this->oBlockStack->get('if');
			$oBlock->elseIfBlock[] = array($condition, $this->parse());
			return false;
		}
		

	
	// include funcs
		// dynamisch includen
		function inc($tmpl) {
			$tmpl = $this->asVal($tmpl);
			$code = 'require($ctx->getTemplate(' . $tmpl . '));';
			$this->inline = false;
			return $this->toPhp($code);
		}
		
		// statisch einbetten
		function embed($tmpl) {
			$tmpl = $this->asCont($tmpl);
			$this->inline = false;
			$oSource = $this->getTemplateSource($tmpl);
			return $this->parse($oSource);
		}
		
		function wrap($tmpl) {
			$oBlock = $this->oBlockStack->push('wrap');
			$cont = $this->parse();
			
			$oBlock = $this->oBlockStack->push('wrap');
			$oBlock->wrapContent = $cont;
			$oSource = $this->getTemplateSource($tmpl);
			$cont = $this->parse($oSource);
			return $cont;
		}
		function wrapContent() {
			$oBlock = $this->oBlockStack->get('wrap');
			return $oBlock->wrapContent;
		}
/*		// statisch umhüllen
		function wrap($tmpl) {
			//$tmpl = $this->asVal($tmpl);
			$oBlock = $this->oBlockStack->push('wrap');
			//$oBlock->wrapContent = $this->parse();
			$this->wrapContent = $this->parse();
			//$cont = $this->parseNewInst($tmpl);
			$oTemplate = $this->getTemplateFile($tmpl);
			$cont = $this->parseFile($oTemplate);
			$this->oBlockStack->pop();
			return $cont;
		}
		function wrapContent() {
			$oBlock = $this->oBlockStack->get('wrap');
			//return $oBlock->wrapContent;
			return $this->oParent->wrapContent;
		}
		function wrapEnd() {
			return false;
		}
*/
		
		// slots
		//static $aSlot = array();
		function slot($name, $default = null) {
			$code = 'require($ctx->getSlot(' . $this->asVal($name) . ', ' . $this->asVal($default) . '));';
			$this->inline = false;
			return $this->toPhp($code);
		}
		function setSlot($name, $tmpl) {
			$code = '$ctx->setSlot(' . $this->asVal($name) . ', ' . $this->asVal($tmpl) . ');';
			return $this->toPhp($code);
		}
		
	// macro
		function defMacro($name) {
			$name = $this->asPhp($name);
			$aParams = array_slice(func_get_args(), 1);
			
			$oBlock = $this->oBlockStack->push('defMacro');
			$block = $this->toHtml($this->parse());
			$this->oBlockStack->pop();
			
			$params = '';
			foreach($aParams as $param) {
				if ($params) $params .= ', ';
				$params .= $this->asVar($param);
			}
			return $this->toPhp(
				"function {$name}Macro($params) {\n".
				"	\$ctx = TemplateContext::\$oInstance;\n".
				"	extract((array) \$ctx->aVar, EXTR_SKIP|EXTR_REFS);\n".
				"	$block \n".
				"};");
		/* closures ab v5.3
			return $this->toPhp(
				"\$ctx->addMacro('$name', function($params) use (\$ctx) {\n".
				"	extract(\$ctx->aVar, EXTR_SKIP|EXTR_REFS);\n".
				"	$block \n".
				"});");
		*/
		}
		
		function macro($name) {  
			$name = $this->asPhp($name);
			$aParams = array_slice(func_get_args(), 1);
			
			$params = '';
			foreach($aParams as $param) $params .= ', '.$this->asVal($param);
			$code = "\$ctx->macro('$name'{$params}); extract(\$ctx->aVar);";
			return $this->toPhp($code);
		}
		
	// var funcs
		function _var($name, $delimiter = '', $params = null) {
			$code = $this->asPhp('$'.$name);
			$aParams = $this->_splitParams($params, $delimiter);
			if (!empty($aParams)) {
				$rule = $aParams[0];
				$aParams = array_slice($aParams, 1);
				$params = '';
				foreach($aParams as $param) $params .= ', '.$this->asVal($param);
				$code = "\$ctx->format('$rule', {$code}{$params})";
			}
			return $this->toEcho($code);
		}
		
		function setFunc($var, $value = null) {
			$var = $this->asPhp($var);
			$varName = $this->asVarName($var);
			$value = $this->asVal($value);			
			$aParams = array_slice(func_get_args(), 1);
			if (count($aParams) > 1) {
				foreach ($aParams as $i => $param) $aParams[$i] = $this->asVal($param);
				$value = 'array(' . join(',',$aParams) . ')';
			}
			//$code = "__Var::set('$varName', $var = $value);";
			$code = "\$ctx->__set('$varName', $var = $value);";
			return $this->toPhp($code);
		}

		function defFormat($name) {
			$name = $this->asPhp($name);
			$aParams = array_slice(func_get_args(), 1);
			
			$oBlock = $this->oBlockStack->push('defFormat');
			$block = $this->toHtml($this->parse());
			$this->oBlockStack->pop();
			
			$params = '';
			foreach($aParams as $param) {
				if ($params) $params .= ', ';
				$params .= $this->asVar($param);
			}
			return $this->toPhp(
				"function {$name}Format($params) {\n".
				"	\$ctx = TemplateContext::\$oInstance;\n".
				"	ob_start();\n".
				"	$block \n".
				"	return ob_get_clean();\n".
				"};");
		/* closures ab v5.3
			return $this->toPhp(
				"\$ctx->addFormat('$name', function($params) use (\$ctx) {\n".
				"	extract(\$ctx->aVar);\n".
				"	ob_start();\n".
				"	$block \n".
				"	return ob_get_clean();\n".
				"});");
		*/
		}
		
		function dumpVars() {
			return $this->toEcho('dump($this)');
		}
		
		
		
	// loops
   		// for
		function forFunc($var, $from, $to, $delta = null) {
			$this->logOpen('loop for start');   
			$var = $this->asVar($var);
			$from = $this->asPhp($from);
			$to = $this->asPhp($to);
			$delta = is_null($delta) ? 1 : $this->asVal($delta);
			$oBlock = $this->oBlockStack->push('for');
			
			$init = 	"\$__a = new LoopForObject($from, $to, $delta);";
			$start = 	"foreach(\$__a as $var): ";
			$end = 		"endforeach; ";
			
			return $this->buildLoop($init, $start, $end);
		}
		
		// each
		function eachFunc($array, $index, $value = null) {
			$this->logOpen('loop each start');   
			$array = $this->asPhp($array);
			$index = $this->asVar($index);
			$init = 	"\$__a = new LoopEachObject($array);";
			$start = 	"foreach(\$__a as $index): ";
			$end = 		"endforeach;";
			
			if (!is_null($value)) {
				$value = $this->asVar($value);
				$start = "foreach(\$__a as $index => $value): ";
			};
			return $this->buildLoop($init, $start, $end);
		}
		
		// for
		function loopFunc($count = 100) {
			$this->logOpen('loop for start');   
			$count = $this->asVal($count);
			$oBlock = $this->oBlockStack->push('loop');
			
			$init = 	"\$__a = new LoopForObject(0, $count);";
			$start = 	"foreach(\$__a as $var): ";
			$end = 		"endforeach; ";
			
			return $this->buildLoop($init, $start, $end);
		}
	
	// Loop feature helper
		function addLoopFeature($type) {
			$oBlock = $this->oBlockStack->get('loop');
			return $oBlock->addFeature($type);
		}
		function getLoopFeature() {
			$oBlock = $this->oBlockStack->get('loop');
			return $oBlock->getFeature();
		}
		function startLoopFeature($type) {
			$oFeature = $this->addLoopFeature($type);
			$this->loopStart();
			return $oFeature;
		}
		function getLoopCondition($cond, $ifContent, $elseContent = null) {
			//$ifContent = $this->asCont($ifContent);
			//$elseContent = $this->asCont($elseContent);
			$oBlock = $this->oBlockStack->get('loop');
			$ret = $oBlock->getCondition($cond, $this->toHtml($ifContent), $this->toHtml($elseContent));
			return $this->toPhp($ret);
		}
		function addLoopCondition($pos, $cond, $ifContent, $elseContent = null) {
			$ifContent = $this->asCont($ifContent);
			$elseContent = $this->asCont($elseContent);
			$oBlock = $this->oBlockStack->get('loop');
			$oBlock->addCondition($pos, $cond, $this->toHtml($ifContent), $this->toHtml($elseContent));
		}
		function addLoopCode($pos, $code) {
			$oBlock = $this->oBlockStack->get('loop');
			$oBlock->addCode($pos, $code);
		}
		function buildLoop($init, $start, $end) {
			//Log::section('loop start');   
			$oBlock = $this->oBlockStack->push('loop', new BlockLoop($init, $start, $end));
			$content = 	$this->toHtml($this->parse());
			//Log::line($oBlock);
			$content = $oBlock->build($content);
			$this->oBlockStack->pop();
			$this->logClose('loop end');
			return $this->toPhp($content);
		}
		function loopStart() {
			$oFeature = $this->getLoopFeature();
			$oFeature->_content = $this->parse();
		}
		function loopContent() {
			$oFeature = $this->getLoopFeature();
			$content = $this->parse();
			if (!empty($oFeature->_hasElse)) {          
				$oFeature->_beforeElseContent = $oFeature->_elseContent;
				$oFeature->_afterElseContent = $content;
				$oFeature->_elseContent = null;
			}
			else {
				$oFeature->_beforeContent = $oFeature->_content;
				$oFeature->_afterContent = $content;
				$oFeature->_content = null;
			}
			return false;
		}
		function loopElse() {
			$oFeature = $this->getLoopFeature();
			$oFeature->_hasElse = true;
			$content = $this->parse();
			$oFeature->_hasElse = false;
			$oFeature->_elseContent = $content;
			return false;
		}
		function loopEnd() {
			$oFeature = $this->getLoopFeature();
			return false;
		}
		
	// Loop features
		// before
		function before1($content) {
			$this->addLoopFeature('before');
			$this->addLoopCondition('before', '$__a->isPos(0)', $content);
		}
		function before0() {
			$oData = $this->startLoopFeature('before');
			$this->addLoopCondition('before', '$__a->isPos(0)', $oData->_content);
		}
		// after
		function after1($content) {
			$this->addLoopFeature('after');
			$this->addLoopCondition('after', '$__a->isPos(-1)', $content);
		}
		function after0() {
			$oData = $this->startLoopFeature('after');
			$this->addLoopCondition('after', '$__a->isPos(-1)', $oData->_content);
		}
		// around
		function around1($before, $after = null) {
			$this->addLoopFeature('around');
			$this->addLoopCondition('before', '$__a->isPos(0)', $before);
			$this->addLoopCondition('after', '$__a->isPos(-1)', $after);
		}
		function around0() {
			$oData = $this->startLoopFeature('around');
			$this->addLoopCondition('before', '$__a->isPos(0)', $oData->_content, $oData->_elseContent);
			$this->addLoopCondition('after', '$__a->isPos(-1)', $oData->_afterContent, $oData->_afterElseContent);
		}
		// between
		function between1($between, $last = null) {
			$this->addLoopFeature('between');
			$this->addLoopCondition('after', '!$__a->isPos(-1)', $between, $last);
		}
		function between0() {
			$oData = $this->startLoopFeature('between');
			$this->addLoopCondition('before', '!$__a->isPos(-1)', $oData->_beforeContent, $oData->_beforeElseContent);
			$this->addLoopCondition('after', '!$__a->isPos(-1)', $oData->_afterContent, $oData->_afterElseContent);
			$this->addLoopCondition('after', '!$__a->isPos(-1)', $oData->_content, $oData->_elseContent);
			//return $this->getLoopCondition('!$__a->isPos(-1)', $oData->_content, $oData->_elseContent);
		}
		// empty
		function empty1($content, $elseContent = null) {
			$this->addLoopFeature('empty');
			$this->addLoopCondition('init', '$__a->isEmpty()', $content, $elseContent);
		}
		function empty0() {
			$oData = $this->startLoopFeature('empty');
			$this->addLoopCondition('init', '$__a->isEmpty()', $oData->_content, $oData->_elseContent);
		}
		// every
		function every1($before, $after = null) {
			$this->addLoopFeature('every');
			//$this->addLoopCondition('before', '$__a->isValid()', $before);
			//$this->addLoopCondition('after', '$__a->isValid()', $after);
			$this->addLoopCondition('before', '1', $before);
			$this->addLoopCondition('after', '1', $after);
		}
		function every0() {
			$oData = $this->startLoopFeature('every');
			//$cont = $this->getLoopCondition('$__a->isValid()', $oData->_content);
			//$this->addLoopCondition('before', '$__a->isValid()', $oData->_beforeContent);
			//$this->addLoopCondition('after', '$__a->isValid()', $oData->_afterContent);
			$cont = $this->getLoopCondition('1', $oData->_content);
			$this->addLoopCondition('before', '1', $oData->_beforeContent);
			$this->addLoopCondition('after', '1', $oData->_afterContent);
			return $cont;
		}
		// even
		function even1($even, $odd = null) {
			return $this->getLoopCondition('$__a->isNth(2,0)', $even, $odd);
		}
		function even0() {
			$oData = $this->startLoopFeature('even');
			$this->addLoopCondition('before', '$__a->isNth(2,0)', $oData->_beforeContent, $oData->_beforeElseContent);
			$this->addLoopCondition('after', '$__a->isNth(2,0)', $oData->_afterContent, $oData->_afterElseContent);
			return $this->getLoopCondition('$__a->isNth(2,0)', $oData->_content, $oData->_elseContent);
		}
		// odd
		function odd1($odd, $even = null) {
			return $this->getLoopCondition('$__a->isNth(2,1)', $odd, $even);
		}
		function odd0() {
			$oData = $this->startLoopFeature('odd');
			$this->addLoopCondition('before', '$__a->isNth(2,1)', $oData->_beforeContent, $oData->_beforeElseContent);
			$this->addLoopCondition('after', '$__a->isNth(2,1)', $oData->_afterContent, $oData->_afterElseContent);
			return $this->getLoopCondition('$__a->isNth(2,1)', $oData->_content, $oData->_elseContent);
		}
		// nth
		function nth2($modulo, $if, $else = null) {
			$aCase = array_slice(func_get_args(), 2);
			$else = array_pop($aCase);
			
			$n = -1;
			$ret = $this->getLoopCondition('$__a->isNth('.$modulo.',0)', $this->asCont($if));
			foreach ($aCase as $n => $case) {
				$ret .=	$this->getLoopCondition('$__a->isNth('.$modulo.','.($n+1).')', $this->asCont($case));
			}
			$ret .=	$this->getLoopCondition('$__a->getNth('.$modulo.') > '.($n+1), $this->asCont($else));
			return $ret;
		}
		function nth1($modulo) {
			$oData = $this->startLoopFeature('nth');
			$this->addLoopCondition('before', '!$__a->isNth('.$modulo.', 0)', $oData->_beforeContent, $oData->_beforeElseContent);
			$this->addLoopCondition('after', '!$__a->isNth('.$modulo.', 0)', $oData->_afterContent, $oData->_afterElseContent);
			return $this->getLoopCondition('!$__a->isNth('.$modulo.', 0)', $oData->_content, $oData->_elseContent);
		}
		// group
		function group2($modulo, $before, $after = null) {              
			$this->addLoopFeature('group');
			$this->addLoopCondition('before', '$__a->isGroupBegin('.$modulo.')', $before);
			$this->addLoopCondition('after', '$__a->isGroupEnd('.$modulo.')', $after);
		}
		function group1($modulo) {
			$oData = $this->startLoopFeature('group');
			$this->addLoopCondition('before', '$__a->isGroupBegin('.$modulo.')', $oData->_beforeContent, $oData->_beforeElseContent);
			$this->addLoopCondition('after', '$__a->isGroupEnd('.$modulo.')', $oData->_afterContent, $oData->_afterElseContent);
			return $this->getLoopCondition('$__a->isGroupBegin('.$modulo.')', $oData->_content, $oData->_elseContent);
		}
		function fill2($modulo, $content, $elseContent = null) {
			$this->addLoopFeature('fill');
			$this->addLoopCode('init', '$__a->addGroup('.$modulo.');');
			return $this->getLoopCondition('!$__a->isValid()', $this->asCont($content), $this->asCont($elseContent));
		}
		function fill1($modulo) {
			$oData = $this->startLoopFeature('fill');
			$this->addLoopCode('init', '$__a->addGroup('.$modulo.');');
			$this->getLoopCondition('before', '!$__a->isValid()', $oData->_beforeContent, $oData->_beforeElseContent);
			$this->getLoopCondition('after', '!$__a->isValid()', $oData->_afterContent, $oData->_afterElseContent);
			return $this->getLoopCondition('!$__a->isValid()', $oData->_content, $oData->_elseContent);
		}
		
	
	
		
	// other
	/*
		function setFunc($var, $value = null) {
			$aParams = array_slice(func_get_args(), 1);
			$value = $this->asVal($value);			
			if (count($aParams) > 1) {
				foreach ($aParams as $i => $param) $aParams[$i] = $this->asVal($param);
				$value = 'array(' . join(',',$aParams) . ')';
			}
			$var = $this->asVar($var);
			$code = "$var = $value;";
			return $this->toPhp($code);
		}
//*/
		
		
	// paramhelper
		static function asPhp($value) {
			$oParser = new TemplatePhpParam();
			return $oParser->run($value);
		}
		static function asCont($value) {
			$oParser = new TemplateContentParam();
			return $oParser->run($value);
		}
		static function asVal($value) {
			$oParser = new TemplateValueParam();
			return $oParser->run($value);
		}
		static function asVar($value) {
			$oParser = new TemplateVarParam();
			return $oParser->run($value);
		}
		static function asVarName($value) {
			$value = self::asVar($value);
			return regexpGet('/\$(\w+)/', $value, 1);
		}
		
	// ausgabehelper
		static function toHtml($html) {
			return TemplateLinker::toHtml($html);
		}
		static function toEcho($value) {
			return TemplateLinker::toEcho($value);
		}
		static function toPhp($code) {
			return TemplateLinker::toPhp($code);
		}
	}

/*
	
	class Block {
		var $type = '';
	}
	
	class BlockStack {
		var $oParser;
		var $aBlock = array();
		
		function __construct($oParser) {
			$this->oParser = $oParser;
		}
		
		function &push($type, $obj = null) {
			$oBlock = is_null($obj) ? new Block() : $obj;
			$oBlock->type = $type;
			array_push($this->aBlock, $oBlock);
			return $oBlock;
		}
		function &get($type = null) {
			$oBlock = end($this->aBlock);
			if (!is_null($type) && $type != $oBlock->type) throw new TemplateException($this->oParser, 'block type "%s" found instead of "%s"', $oBlock->type, $type);
			return $oBlock;
		}
		function &pop($type = null) {
			$oBlock = array_pop($this->aBlock);
			if (!is_null($type) && $type != $oBlock->type) throw new TemplateException($this->oParser, 'block type "%s" found instead of "%s"', $oBlock->type, $type);
			return $oBlock;
		}
	}
*/
	
//	require_once('class.template.loop.php');
?>
<?php

	class TemplateParam {
		
		function replaceVars($param, $before='', $after='') {
		/* closure ab v5.3
			$func = function($aMatch) use($before, $after) {       
				$var = $aMatch[1];
				$var = preg_replace('/\.(\$[\w-]+)/', '[\\1]', $var);
				$var = preg_replace('/\.([\w-]+)/', '["\\1"]', $var);
				//echo "{$aMatch[1]} = $var<br>";
				return $before.'$'.$var.$after;
			};
		*/
			$func = create_function(
				'$aMatch, $before="'.$before.'", $after="'.$after.'"', ' 
				$var = $aMatch[1];
				$var = preg_replace(\'/\.(\$[\w-]+)/\', \'[\\1]\', $var);
				$var = preg_replace(\'/\.([\w-]+)/\', \'["\\1"]\', $var);
				//echo "{$aMatch[1]} = $var<br>";
				return $before.\'$\'.$var.$after;
				');
			$param = preg_replace_callback('/\{?\$((\w+\b)(\.([\w\$-]+\b))*)\}?;?/', $func, $param);
			//echo $param;
			return $param;
		}
		
		function run($param) {
			return $param;
		}
	}
	
	class TemplatePhpParam extends TemplateParam {
		function run($param) {
			$param = $this->replaceVars($param);
			return $param;
		}
	};
	
	class TemplateContentParam extends TemplateParam {
		function run($param) {
			$param = $this->replaceVars($param,'<%e','%>');
			return $param;
		}
	};
	
	class TemplateValueParam extends TemplateParam {
		function run($param) {
			$param = $this->replaceVars($param);
			return '"'.$param.'"';
		}
	};

	class TemplateVarParam extends TemplateParam {
	};

	
?>
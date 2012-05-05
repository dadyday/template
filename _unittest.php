<?php

	require_once('../cfg.php');
	require_once(PATH_LIBS . 'common/cfg.php');
	require_once(PATH_LIB_COMMON . 'class.log.php');
	require_once(PATH_LIB_COMMON . 'init.tools_debug.php');
	
	require_once(PATH_LIBS . 'template2/cfg.php');
	require_once(PATH_LIB_TEMPLATE . 'class.template.php');

	
	define('PATH_UNITTEST_TEST', dirname(__FILE__).'/_unittest/');
	define('PATH_UNITTEST_TMPL', dirname(__FILE__).'/_unittest/tmpl/');
	define('PATH_UNITTEST_CACHE', dirname(__FILE__).'/_unittest/cache/');
	
	
	//TemplateParser::$_templateClass = 'TestTemplate';
	//TemplateLinker::$_debugMode = @$_GET['debug'];

	class TestTemplate extends TemplateBase {
			
		static $base = '';
		function getSourceFileName($name) {
			return PATH_UNITTEST_TMPL . 'tmpl.'.self::$base.'.'.$name.'.php';
		}
		function getParsedFileName($name) {
			return PATH_UNITTEST_CACHE . '_cache.'.self::$base.'.'.$name.'.php';
		}
		
		static function runTests() {
			$hDir = opendir(PATH_UNITTEST_TEST);
			while ($file = readdir($hDir)) {
				$filePath = PATH_UNITTEST_TEST.'/'.$file;
				if (is_dir($filePath)) continue;
				if (substr($file,-4) != '.php') continue;
				if ($file{0} == '_') continue;
				//if ($name != 'embed') continue;
				
				$name = basename($file, '.php');
				echo $file;
				self::runTest($filePath);
			};
			closedir($hDir);
		}
		
		static function runTest($testFile) {
			self::$base = basename($testFile, '.php');
			include($testFile);
			$aVars = get_defined_vars();
			unset($aVars['testFile']);
			unset($aVars['aFile']);
			unset($aVars['result']);
			
			foreach ($aFile as $file => $content) {
				$file = PATH_UNITTEST_TMPL . 'tmpl.'.self::$base.'.'.$file.'.php';
				file_put_contents($file, $content);
			}
			
			$_oTmpl = new TestTemplate('index');
			//echo dump($aVars);
			ob_start();
			$_oTmpl-> display($aVars);
			$real = ob_get_clean();
			
			$realC = trim(preg_replace('/\r?\n/', '<b>&crarr;</b>', htmlentities($real)));
			$resultC = trim(preg_replace('/\r?\n/', '<b>&crarr;</b>', htmlentities($result)));
			$realC = trim(preg_replace('/\t/', '<b>&rarr;	</b>', $realC));
			$resultC = trim(preg_replace('/\t/', '<b>&rarr;	</b>', $resultC));
			$realCc = trim(preg_replace('/\s+/', '', $real));
			$resultCc = trim(preg_replace('/\s+/', '', $result));
			
			if ($realC == $resultC)
				echo ' <font color="green">OK</font><br>';
			else {
				if ($realCc == $resultCc) {
					echo ' <font color="orange">OK</font>: just whitespaces<hr>';
					echo
						'<pre>' . ($realC) . '<br>'.
						'' . ($resultC) . '</pre><hr>';
				}
				else  {
					echo ' <font color="red">ERROR</font>:<hr>';
					echo
						'<pre>' . ($real) . '<hr>'.
						'' . ($result) . '</pre><hr>';
				}
			}
		}

	}

	//runTests('_unittest/tools/');
	TestTemplate::runTests();


	
	
	function runTests($path) {
		return;
		$hDir = opendir($path);
		while ($file = readdir($hDir)) {
			$filePath = $path.'/'.$file;
			$ext = end(preg_split('/\./', $file));
			$name = basename($file, '.php');
			if (is_dir($filePath)) continue;
			if ($ext != 'php') continue;
			
			echo $file . '<br>';
			include($filePath);
			echo '<hr>';
		};
		closedir($hDir); 
	}
	
	
?>
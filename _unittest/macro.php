<?php

$aFile['index'] = <<<'TEXT'
#defMacro:test,$b;$b#end;
#macro:test,$a;,#macro:test,$b;,#macro:test,$c;;
#defMacro:test,$b;$b,$c#set:$z,$b;#end;
#macro:test,$a;,$z,#macro:test,$b;,$z,#macro:test,$c;,$z;;
TEXT;

$result = <<<'TEXT'
42,21,7;
42,7,42,21,7,21,7,7,7;
TEXT;

$a = 42;
$b = 21;
$c = 7;
$z = 0;

?>
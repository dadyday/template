<?php

$aFile['index'] = '
#defMacro:test,$b;$b#end;
#macro:test,$a;,#macro:test,$b;,#macro:test,$c;;
#defMacro:test2,$b;$b,$c#set:$z,$b;#end;
#macro:test2,$a;,$z,#macro:test2,$b;,$z,#macro:test2,$c;,$z;;
';

$result = '
42,21,7;
42,7,42,21,7,21,7,7,7;
';

$a = 42;
$b = 21;
$c = 7;
$z = 0;

?>
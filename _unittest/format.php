<?php

$aFile['index'] = '-
#defFormat:bold,$val;<b>$val</b>#end;
$a:bold;
#defFormat:emph,$val,$e;<$e:empty,b;>$val</$e:empty,b;>#end;
#set:$e,;$a:emph,$e;
#set:$e,i;$a:emph,$e;
#set:$e,u;$a:emph,$e;
';

$result = '-
<b>42</b>
<b>42</b>
<i>42</i>
<u>42</u>
';

function emptyFormat($val, $default) { 
	return !empty($val) ? $val : $default; 
};

$a = 42;
$b = 21;
$c = 7;
$z = 0;

?>
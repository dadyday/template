<?php

$aFile['index'] = '-
#defFormat:bold,$val;<b>$val</b>#end;
$a:bold;
#defFormat:emph,$val;<$e:empty,b;>$val</$e:empty,b;>#end;
#set:$e,;$a:emph;
#set:$e,i;$a:emph;
#set:$e,u;$a:emph;
';

$result = '-
<b>42</b>
<b>42</b>
<i>42</i>
<u>42</u>
';

function emptyFormat($ctx, $val, $default) { return !empty($val) ? $val : $default; };

$a = 42;
$b = 21;
$c = 7;
$z = 0;

?>
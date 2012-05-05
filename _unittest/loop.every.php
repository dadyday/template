<?php

$aFile['index'] = '
1(#for:$n,1,5;#every:[,];$n;#end;)
2(#for:$n,1,5;#every;[#content;]#end;$n;#end;)
';


$result = '
1([1][2][3][4][5])
2([1][2][3][4][5])
';

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
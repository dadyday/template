<?php

$aFile['index'] = <<<'TEXT'
1(#for:$n,1,5;#every:[,];$n;#end;)
2(#for:$n,1,5;#every;[#content;]#end;$n;#end;)
TEXT;


$result = <<<'TEXT'
1([1][2][3][4][5])
2([1][2][3][4][5])
TEXT;

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
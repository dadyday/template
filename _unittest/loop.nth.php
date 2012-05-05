<?php

$aFile['index'] = <<<'TEXT'
1(#for:$n,1,5;#nth:3,a;$n;,#end;)
2(#for:$n,1,5;#nth:3,a,b;$n;,#end;)
3(#for:$n,1,5;#nth:3,a,b,;$n;,#end;)
4(#for:$n,1,5;#nth:3,a,b,c;$n;,#end;)
5(#for:$n,1,5;#nth:4,a,b,c;$n;,#end;)
TEXT;


$result = <<<'TEXT'
1(a1,2,3,a4,5,)
2(a1,b2,b3,a4,b5,)
3(a1,b2,3,a4,b5,)
4(a1,b2,c3,a4,b5,)
5(a1,b2,c3,c4,a5,)
TEXT;

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
<?php

$aFile['index'] = <<<'TEXT'
(#for:$n,1,3;#before;:#end;$n;#end;)
(#for:$n,1,0;#before;:#end;$n;#end;)
(#each:$aTest1,$n;#before;:#end;$n;#end;)
(#each:$aTest2,$n;#before;:#end;$n;#end;)
(#for:$n,1,3;#before;*#end;
#each:$aTest1,$m;#before;$n;#end;$m;#end;#end;)
TEXT;


$result = <<<'TEXT'
(:123)
()
(:abc)
()
(*1abc2abc3abc)
TEXT;

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
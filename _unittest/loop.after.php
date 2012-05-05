<?php

$aFile['index'] = '
(#for:$n,1,3;#after;;#end;$n;#end;)
(#for:$n,1,0;#after;;#end;$n;#end;)
(#each:$aTest1,$n;#after;;#end;$n;#end;)
(#each:$aTest2,$n;#after;;#end;$n;#end;)
(#for:$n,1,3;#after;*#end;
(#for:$m,1,3;#after;:$n#end;$m;#end;)#end;)
(#for:$n,1,3;#after;*#end;
(#each:$aTest1,$m;#after;:$n#end;$m;#end;)#end;)
';


$result = '
(123;)
()
(abc;)
()
((123:1)(123:2)(123:3)*)
((abc:1)(abc:2)(abc:3)*)
';

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
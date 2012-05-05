<?php

$aFile['index'] = '
(#for:$n,1,3;#around;[#content;]#end;$n;#end;)
(#for:$n,1,3;#around:[,];$n;#end;)
(#for:$n,1,0;#around;:#content;;#end;$n;#end;)
(#each:$aTest1,$n;#around;:#content;;#end;$n;#end;)
(#each:$aTest2,$n;#around;:#content;;#end;$n;#end;)
#for:$n,1,3;#around;[#content;]#end;
(#each:$aTest1,$m;#around;$n;:#content;;#end;$m;#end;)#end;
';


$result = '
([123])
([123])
()
(:abc;)
()
[(1:abc;)(2:abc;)(3:abc;)]';

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
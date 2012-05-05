<?php

$aFile['index'] = <<<'TEXT'
(#for:$n,1,3;#empty;-#end;$n;#end;)
(#for:$n,1,0;#empty;-#end;$n;#end;)
(#each:$a1,$n;$n;#empty;-#end;#end;)
(#each:$a2,$n;#empty;-#end;$n;#end;)
TEXT;


$result = <<<'TEXT'
(123)
(-)
(123)
(-)
TEXT;

$a1 = array(1,2,3);
$a2 = array();

?>
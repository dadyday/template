<?php

$aFile['index'] = <<<'TEXT'
(#for:$n,1,5;#even;e#end;$n;#end;)
(#for:$n,1,5;#even;e#else;o#end;$n;#end;)
(#for:$n,1,5;#even;#else;o#end;$n;#end;)
(#for:$n,1,5;#even:e;$n;#end;)
(#for:$n,1,5;#even:e,o;$n;#end;)
(#for:$n,1,5;#even:,o;$n;#end;)

(#for:$n,1,5;#odd;o#end;$n;#end;)
(#for:$n,1,5;#odd;o#else;e#end;$n;#end;)
(#for:$n,1,5;#odd;#else;e#end;$n;#end;)
(#for:$n,1,5;#odd:o;$n;#end;)
(#for:$n,1,5;#odd:o,e;$n;#end;)
(#for:$n,1,5;#odd:,e;$n;#end;)
TEXT;


$result = <<<'TEXT'
(e12e34e5)
(e1o2e3o4e5)
(1o23o45)
(e12e34e5)
(e1o2e3o4e5)
(1o23o45)

(1o23o45)
(e1o2e3o4e5)
(e12e34e5)
(1o23o45)
(e1o2e3o4e5)
(e12e34e5)
TEXT;

$aTest1 = array('a','b','c');
$aTest2 = array();

?>
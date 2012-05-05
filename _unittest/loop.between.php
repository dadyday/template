<?php

$aFile['index'] = '
#each:$aTest,$to,$a;
(#for:$n,1,$to;$n;#between ,;#end;)
(#for:$n,1,$to;$n;#between , \;;#end;)
(#for:$n,1,$to;$n;#between;,#end;#end;)
(#for:$n,1,$to;$n;#between;,#else;;#end;#end;)
(#each:$a,$n;$n;#between ,;#end;)
(#each:$a,$n;$n;#between , \;;#end;)
(#for:$n,1,$to;$n;#between;#content;,#end;#end;)
(#for:$n,1,$to;$n;#between;#content;,#else;;#end;#end;)
(#for:$n,1,$to;$n;#between;#content;,#else;#content;;#end;#end;)
---
#end;
';


$result = '
()
()
()
()
()
()
()
()
()
---
(1)
(1;)
(1)
(1;)
(1)
(1;)
(1)
(1;)
(1;)
---
(1,2)
(1,2;)
(1,2)
(1,2;)
(1,2)
(1,2;)
(1,2)
(1,2;)
(1,2;)
---
(1,2,3)
(1,2,3;)
(1,2,3)
(1,2,3;)
(1,2,3)
(1,2,3;)
(1,2,3)
(1,2,3;)
(1,2,3;)
---

';

$aTest = array(array(), array(1), array(1,2), array(1,2,3));

?>
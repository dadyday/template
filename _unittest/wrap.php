<?php

$aFile['index'] = <<<TEXT
---#wrap:wrap0;www#end;---
---#wrap:wrap0;#wrap:wrap0;#wrap:wrap2;xxx#end;#end;#end;---
---#wrap:wrap1;yyy#end;---
---#wrap:wrap1;#wrap:wrap2;zzz#end;#end;---
TEXT;

$aFile['wrap0'] = <<<TEXT
[#content;]
TEXT;

$aFile['wrap1'] = <<<TEXT
#wrap:wrap0;(#content;)#end;
TEXT;
	
$aFile['wrap2'] = <<<TEXT
{#content;}
TEXT;


$result = <<<TEXT
---[www]---
---[[{xxx}]]---
---[(yyy)]---
---[({zzz})]---
TEXT;

?>
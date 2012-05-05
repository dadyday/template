<?php

$aFile['index'] = <<<'TEXT'
(#loop;#looptest:a{$n}z,x{$n}y; #end;)
(#loop;$n;:#looptest:first[;:$n;#looptest:];#end;)
(#loop;$n;:#looptest:first[,other[;:$n;]#end;)
(#loop;$n;:#looptest;first[#end;:$n;#looptest;]#end;#end;)
(#loop;$n;:#looptest;first[#else;other[#end;:$n;]#end;)
(#loop;$n;:#looptest;first[#content;]#end;:$n;#end;)
(#loop;$n;:#looptest;first[#content;]#else;other[#content;]#end;:$n;#end;)
(#loop;
	$n;:
#looptest;
	#looptest:first,never;[#content;]
#else;
	#looptest:never,other;[#content;]
#end;
	:$n;
#end;)
TEXT;

/*
$aFile['index'] = <<<'TEXT'
(#loop;
	$n;:
#looptest;
	#looptest:first,never;[#content;]
#else;
	#looptest:never,other;[#content;]
#end;
	:$n;
#end;)
TEXT;
//*/

$result = <<<'TEXT'
(a1z x2y x3y x4y x5y)
(1:first[:1]2::23::34::45::5)
(1:first[:1]2:other[:2]3:other[:3]4:other[:4]5:other[:5])
(1:first[:1]2::23::34::45::5)
(1:first[:1]2:other[:2]3:other[:3]4:other[:4]5:other[:5])
(first[1::1]2::23::34::45::5)
(first[1::1]other[2::2]other[3::3]other[4::4]other[5::5])
(first[1::1]other[2::2]other[3::3]other[4::4]other[5::5])
TEXT;

$aTest = array(1,2,3);

?>
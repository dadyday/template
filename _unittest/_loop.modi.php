<?php

$aFile['index'] = '
kurz
#for:$n,1,3;
	#test:	eins:;
	($n;)
	#test::eins;
#end;

kurzelse
#for:$n,1,3;
	#test:	eins:,	anders:;
	($n;)
	#test::eins,:anders;
#end;

lang
#for:$n,1,3;
#test;
	eins:
#end;
	($n;)
#test;
	:eins
#end;
#end;

langelse
#for:$n,1,3;
#test;
	eins:
#else;
	anders:
#end;
	($n;)
#test;
	:eins
#else;
	:anders
#end;
#end;

hülle
#for:$n,1,3;
#test;
	eins: #content; :eins
#end;
	($n;)
#end;

hülleelse
#for:$n,1,3;
#test;
	eins: #content;	:eins
#else;
	anders:	#content; :anders
#end;
	($n;)
#end;
';

$aFile['index'] = '
1!#for:$n,1,3;#looptest: (;a$n;$n;z#looptest:); #end;!
2!#for:$n,1,3;#looptest: (, [; a$n;$n;z #looptest:),]; #end;!
3!#for:$n,1,3;#looptest; (#end; a$n;$n;z #looptest;)#end; #end;!
4!#for:$n,1,3;#looptest; (#else; [#end; a$n;$n;z #looptest;)#else;]#end; #end;!
5!#for:$n,1,3; a$n; #looptest; (#content;) #end; $n;z #end;!
6!#for:$n,1,3; a$n; #looptest; (#content;) #else; [#content;] #end; $n;z #end;!
7!#for:$n,1,3;#looptest;#else; [#end; a$n; #looptest; (#content;) #end; $n;z #looptest;#else;] #end;#end;!
8!#for:$n,1,3;#looptest; (#end; a$n; #looptest;#else; [#content;] #end; $n;z #looptest;) #end;#end;!
';


$result = '
1!(a11z) a22z a33z!
2!(a11z)[a22z][a33z]!
3!(a11z) a22z a33z!
4!(a11z)[a22z][a33z]!
5!(a11z) a22z a33z!
6!(a11z)[a22z][a33z]!
7!(a11z)[a22z][a33z]!
8!(a11z)[a22z][a33z]!
';

?>
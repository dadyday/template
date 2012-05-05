<?php

$aFile['index'] = <<<'TEXT'
#for:$nn,0,7;
	#for:$item,1,$nn;
		#around;
<table border="1" style="width: 200px;">
	#content;
</table>
		#end;
		#group:6;
	<tr><td colspan="3">head $nn;</td></tr>
	#content;
		#end;
		#group:3;
	<tr>
		#content;
	</tr>
		#end;
		#every;
		<td style="text-align: #nth:3,left,center,right;; background: #even:#fcc,#ccf;;">#content;</td>
		#end;
		#fill:6,-,$item;
	#end;
#end;
TEXT;
/*


	#every;
		<td style="
			text-align: #nth:3,left,center,right;;
			background: #even:red,blue;;
		">
			#content;
		</td>
	#end;
			$item;	
*/

$result = <<<'TEXT'
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 1</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">-</td>
		<td style="text-align: right; background: #fcc;">-</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">-</td>
		<td style="text-align: center; background: #fcc;">-</td>
		<td style="text-align: right; background: #ccf;">-</td>
	</tr>
</table>
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 2</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">2</td>
		<td style="text-align: right; background: #fcc;">-</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">-</td>
		<td style="text-align: center; background: #fcc;">-</td>
		<td style="text-align: right; background: #ccf;">-</td>
	</tr>
</table>
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 3</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">2</td>
		<td style="text-align: right; background: #fcc;">3</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">-</td>
		<td style="text-align: center; background: #fcc;">-</td>
		<td style="text-align: right; background: #ccf;">-</td>
	</tr>
</table>
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 4</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">2</td>
		<td style="text-align: right; background: #fcc;">3</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">4</td>
		<td style="text-align: center; background: #fcc;">-</td>
		<td style="text-align: right; background: #ccf;">-</td>
	</tr>
</table>
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 5</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">2</td>
		<td style="text-align: right; background: #fcc;">3</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">4</td>
		<td style="text-align: center; background: #fcc;">5</td>
		<td style="text-align: right; background: #ccf;">-</td>
	</tr>
</table>
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 6</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">2</td>
		<td style="text-align: right; background: #fcc;">3</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">4</td>
		<td style="text-align: center; background: #fcc;">5</td>
		<td style="text-align: right; background: #ccf;">6</td>
	</tr>
</table>
<table border="1" style="width: 200px;">
	<tr><td colspan="3">head 7</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">1</td>
		<td style="text-align: center; background: #ccf;">2</td>
		<td style="text-align: right; background: #fcc;">3</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">4</td>
		<td style="text-align: center; background: #fcc;">5</td>
		<td style="text-align: right; background: #ccf;">6</td>
	</tr>
	<tr><td colspan="3">head 7</td></tr>
	<tr>
		<td style="text-align: left; background: #fcc;">7</td>
		<td style="text-align: center; background: #ccf;">-</td>
		<td style="text-align: right; background: #fcc;">-</td>
	</tr>
	<tr>
		<td style="text-align: left; background: #ccf;">-</td>
		<td style="text-align: center; background: #fcc;">-</td>
		<td style="text-align: right; background: #ccf;">-</td>
	</tr>
</table>

TEXT;

$a1 = array(1,2,3);
$a2 = array();

?>
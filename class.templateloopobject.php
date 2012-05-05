<?php

	abstract class LoopObject implements Iterator {
		
		static function getInst($param1, $param2 = null) {
			return $param1;	
		
			$type = gettype($param1);
			switch($type) {
				case 'array': 	
				case 'object':	return new LoopEachObject($param1);
				default:
					if (is_numeric($param1)) {
						if (is_null($param2)) { $param2 = $param1; $param1 = 0; };
						return new LoopForObject($param1, $param2);	
					}
					return new LoopEachObject(array($param1));
			}
		}
		
		var $pos = 0;	// 0 indiz. position
		var $length = 0; // anz. gültiger steps
		var $count = 0; // anz. durchläufe (incl. groupfills)
		var $modulo = 1;
		
		function getIterator() {
			return $this;
		}
		function isEmpty() {
			return $this->count <= 0;
		}
		function isValid() {
			return $this->pos < $this->length;
		}
		function isPos($pos) {
			if ($pos >= 0) return $this->pos == $pos;
			return $this->pos == $this->count+$pos;
		}
		function getNth($modulo) {
			return $this->pos % $modulo;
		}
		function isNth($modulo, $pos) {
			if ($pos >= 0) return $pos == ($this->pos % $modulo);
			return 0; // todo: zB 3. pos von  hinten 
		}
		
	// group // 0/3 0r0, 1/3 0r1, 2/3 0r2, 3/3 1r0, 4/3 1r1
		function addGroup($modulo) {
			$this->modulo *= $modulo;                             
			$this->count = ceil($this->count / $this->modulo) * $this->modulo;
		}
		function isGroupBegin($modulo) {
			return !($this->pos % $modulo);
		}
		function isGroupEnd($modulo) {
			return !(($this->pos+1) % $modulo) || ($this->pos+1) >= $this->count;
		}
		
	// Iterator
		function valid() {
			return $this->pos < $this->count;
		}
		function next() {
			$this->pos++;
		}
		function rewind() {
			$this->pos = 0;
		}		

	}
	
	class LoopEachObject extends LoopObject {
		var $oList = null;
		
		function __construct($value) {
			$this->oList = is_a($value, 'Iterator') ? $value : new ArrayIterator($value);
			$this->count = $this->length = count($this->oList);
		}
		
	// Iterator
		function current() {
			if ($this->isValid()) return $this->oList->current();
			return null;
		}
		function key() {
			if ($this->isValid()) return $this->oList->key();
			return '';
		}
		function next() {
			$this->oList->next();
			parent::next();
		}
		function rewind() {
			$this->oList->rewind();
			parent::rewind();
		}
	}
	
	class LoopForObject extends LoopObject {
		var $from = 0;
		var $to = 0;
		var $step = 1;
		
		function __construct($from, $to, $step = 1) {
			$this->from = $from;
			$this->to = $to;
			$this->count = $this->length = ceil(($to - $from +1) / $step);
		}
	// Iterator
		function current() {
			if ($this->isValid()) return $this->from + ($this->pos * $this->step);
			return null;
		}
		function key() {
			if ($this->isValid()) return $this->pos;
			return '';
		}
	}
	
?>
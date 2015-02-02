<?php
trait TProtoHTMLDump {
	public function dump($echo = true, $offset = 0) {
		$res = '';
		for ($i = 0; $i < $offset; $i++) {
			$res .= "  ";
		}
		$res .= ($this->tag) ? $this->tag : 'NULL';
		$res .= $this->dumpAttr();
		$res .= "\n";
		foreach ($this as $child) {
			if ($child instanceof ProtoHTMLObject) {
				$res .= $child->dump(false, $offset + 1);
			}
			else {
				for ($i = 0; $i < $offset + 1; $i++) {
					$res .= "  ";
				}
				$res .= "'$child'\n";
			}
		}
		if ($echo) {
			echo $res;
		}
		return $res;
	}
	private function dumpAttr() {
		$res = '';
		foreach ($this->attr as $name => $value) {
			if ($res) {
				$res .= ', ';
			}
			if (is_object($value)) {
				$value = $value->dump();
			}
			$res .= "$name = '$value'";
		}
		if (!$res) {
			return '';
		}
		return " ($res)";
	}
}
?>
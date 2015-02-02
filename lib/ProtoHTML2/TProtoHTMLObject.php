<?php
trait TProtoHTMLObject {
	public function template() {
		$argv = func_get_args();
		$obj = call_user_func_array(array('ProtoHTML', 'template'), $argv);
		$this->append($obj);
		return $this;
	}
}
?>
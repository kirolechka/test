<?php
trait TProtoHTMLParse {
	private static function __getAttrKey($html, &$offset) {
		$res = '';
		while ($offset < strlen($html) && $html[$offset] == ' ') {
			++$offset;
		}
		while ($offset < strlen($html)) {
			if ($html[$offset] == '>') {
				break;
			}
			if ($html[$offset] == ' ') {
				break;
			}
			if ($html[$offset] == '=') {
				break;
			}
			$res .= $html[$offset];
			++$offset;
		}
		return $res;
	}
	private static function __getAttrVal($html, &$offset) {
		$res = '';
		$flag = false;
		$quote = '';
		while ($offset < strlen($html) && $html[$offset] == ' ' || $html[$offset] == '=') {
			if ($html[$offset] == '=') {
				$flag = true;
			}
			++$offset;
		}
		if (!$flag) {
			return null;
		}
		if ($html[$offset] == '"' || $html[$offset] == "'") {
			$quote = $html[$offset];
			++$offset;
		}
		while ($offset < strlen($html)) {
			if ($html[$offset] == '>') {
				break;
			}
			if (!$quote && $html[$offset] == ' ') {
				break;
			}
			if ($html[$offset] == $quote) {
				break;
			}
			$res .= $html[$offset];
			++$offset;
		}
		return $res;
	}
	private static function __getTagAttr($html, &$offset) {
		$res = array();
		if ($html[$offset] == '>') {
			return $res;
		}
		while ($offset < strlen($html)) {
			$key = self::__getAttrKey($html, $offset);
			$val = self::__getAttrVal($html, $offset);
			if ($val === null) {
				$val = true;
			}
			$res[$key] = $val;
			if ($html[$offset] == '>') {
				break;
			}
			++$offset;
		}
		return $res;
	}
	private static function __getData($html, &$offset, $stop) {
		$res = '';
		if (!is_array($stop)) {
			$stop = array($stop);
		}
		while ($offset < strlen($html)) {
			foreach ($stop as $st) {
				if (substr($html, $offset, strlen($st)) == $st) {
					break(2);
				}
			}
			$res .= $html[$offset];
			++$offset;
		}
		return $res;
	}
	public static function parseElement($html, &$offset = 0) {
		$res = null;
		if (substr($html, $offset, 4) == '<!--') {
			$offset += 4;
			$data = self::__getData($html, $offset, '-->');
			$res = new ProtoHTMLComment($data);
			$offset += 3;
		}
		if (!$res && substr($html, $offset, 2) == '</') {
			$offset += 2;
			$res = self::__getData($html, $offset, '>');
			++$offset;
		}
		if (!$res && $html[$offset] == '<') {
			++$offset;
			$tag = self::__getData($html, $offset, array('>', ' '));
			$res = new ProtoHTMLObject($tag);
			$attr = self::__getTagAttr($html, $offset);
			foreach ($attr as $k => $v) {
				$res->$k = $v;
			}
			++$offset;
		}
		if (!$res) {
			$data = self::__getData ($html, $offset, '<');
			$res = new ProtoHTMLData($data);
		}
		return $res;
	}
	public static function parse(ProtoHTMLObject $parent, $html, &$offset = 0) {
		while ($offset < strlen($html)) {
			$temp = $offset;
			$child = self::parseElement($html, $temp);
			if (is_string($child) && $parent->tag == $child) {
				$offset = $temp;
			}
			if (is_string($child)) {
				break;
			}
			if ($child instanceof ProtoHTMLObject && $child->condSingle() && $child->tag == $parent->tag) {
				break;
			}
			$parent->append($child);
			$offset = $temp;
			if ($child instanceof ProtoHTMLObject && ($child->tag == 'script')) {
				$child->text(self::__getData($html, $offset, '</script>'));
				$offset += 9;
			}
			if ($child instanceof ProtoHTMLObject && ($child->tag == 'style')) {
				$child->text(self::__getData($html, $offset, '</style>'));
				$offset += 8;
			}
			if ($child instanceof ProtoHTMLObject && !$child->single() && $child->tag != 'script' && $child->tag != 'style') {
				self::parse($child, $html, $offset);
			}
		}
		return;
	}
}
?>
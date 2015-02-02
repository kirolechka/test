<?php
trait ProtoHTMLParseViaDOM {
	public static function parse($html) {
		$dom = new DOMDocument();
		$dom->loadHTML($html);
		$dom = $dom->documentElement;
		$html = self::parseDOM($dom);
		$res = new ProtoHTMLChildren();
		$res->append($html->head
						->children);
		$res->append($html->body
						->children);
		return $res;
	}
	public static function parseDOM($obj) {
		if ($obj instanceof DOMElement) {
			$res = new ProtoHTMLObject($obj->tagName);
			if ($obj->attributes->length > 0) {
				foreach ($obj->attributes as $attribute) {
					$res->set($attribute->name, $attribute->value);
				}
			}
		}
		if ($obj instanceof DOMText) {
			$res = $obj->nodeValue;
			if (!$res) {
				$res = ' ';
			}
		}
		if ($obj instanceof DOMCdataSection) {
			$res = $obj->nodeValue;
			if (!$res) {
				$res = ' ';
			}
		}
		if ($obj->childNodes)
		foreach ($obj->childNodes as $node) {
			$data = self::parseDOM($node);
			if ($data instanceof ProtoHTMLObject) {
				$res->append($data);
			}
			if (is_string($data) && $data) {
				$res->appendRaw($data);
			}
		}
		return $res;
	}
}
?>
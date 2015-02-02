<?php
trait TProtoHTMLExtra {
	public static function css($url) {
		return (new ProtoHTMLObject('link'))->set('rel', 'stylesheet')
											->set('type', 'text/css')
											->set('href', $url);
	}
	public static function icon($url) {
		return (new ProtoHTMLObject('link'))->set('rel', 'shortcut icon')
											->set('href', $url);
	}
	public static function js($url) {
		return (new ProtoHTMLObject('script'))->set('type', 'text/javascript')
											->set('src', $url);
	}
	public static function a($text, $url) {
		return (new ProtoHTMLObject('a'))->set('href', $url)
										->text($text);
	}
	public static function h1($text) {
		return (new ProtoHTMLObject('h1'))->text($text);
	}
	public static function h2($text) {
		return (new ProtoHTMLObject('h2'))->text($text);
	}
	public static function h3($text) {
		return (new ProtoHTMLObject('h3'))->text($text);
	}
	public static function h4($text) {
		return (new ProtoHTMLObject('h4'))->text($text);
	}
	public static function h5($text) {
		return (new ProtoHTMLObject('h5'))->text($text);
	}
	public static function h6($text) {
		return (new ProtoHTMLObject('h6'))->text($text);
	}
	public static function input($type, $name, $value = null, $placeholder = null) {
		$input = new ProtoHTMLObject('input');
		$input->type = $type;
		$input->name = $name;
		if ($value !== null) {
			$input->value = $value;
		}
		if ($placeholder !== null) {
			$input->placeholder = $placeholder;
		}
		return $input;
	}
	public static function hidden($name, $value) {
		return self::input('hidden', $name, $value);
	}
	public static function submit($text) {
		return (new ProtoHTMLObject('input'))->set('type', 'submit')
											->set('value', $text);
	}
	public static function button($text, $onclick) {
		return (new ProtoHTMLObject('input'))->set('type', 'button')
											->set('value', $text)
											->set('onclick', $onclick);
	}
	public static function menu($class = null) {
		return new ProtoHTMLMenu($class);
	}
	public static function bradcrumbs($class = 'bradcrumbs') {
		return new ProtoHTMLBradcrumbs($class);
	}
	public static function tabs($class = 'tabs') {
		return new ProtoHTMLTabs($class);
	}
	public static function select($name = null) {
		return new ProtoHTMLSelect($name);
	}
	public static function dir($path) {
		$path = realpath($path);
		$base = realpath($_SERVER['DOCUMENT_ROOT']);
		$dir = scandir($path);
		if (!$dir) {
			trigger_error ("Directory $dir is not exist or access denied", E_USER_WARNING);
			return;
		}
		$children = new ProtoHTMLChildren();
		foreach ($dir as $fn) {
			$fn = $path . '/' . $fn;
			if (is_file($fn)) {
				$url = substr ($fn, strlen($base));
				$ext = pathinfo($fn, PATHINFO_EXTENSION);
				switch ($ext) {
					case 'js': 
						$children[] = self::js($url); 
					break;
					case 'css': 
						$children[] = self::css($url); 
					break;
				}
			}
		}
		return $children;
	}
}
?>
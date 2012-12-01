<?php

namespace Sixohthree;

class Container {
	public $widgets;
	public $taxonomies;
	public $posttypes;
	public $shortcodes;

	public function __construct() {
		$this->widgets = new \stdClass;
		$this->taxonomies = new \stdClass;
		$this->posttypes = new \stdClass;
		$this->shortcodes = new \stdClass;
	}//end __construct
}//end class \Sixohthree\Container

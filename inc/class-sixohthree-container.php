<?php

namespace Sixohthree;

class Container {
	public $widgets;
	public $taxonomies;
	public $posttypes;

	public function __construct() {
		$this->widgets = new \stdClass;
		$this->taxonomies = new \stdClass;
		$this->posttypes = new \stdClass;
	}//end __construct
}//end class \Sixohthree\Container

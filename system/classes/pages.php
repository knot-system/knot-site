<?php


class Pages {

	public $pages;

	function __construct() {

		global $core;

		$database = new Database( '', true, 'page.txt' );
		$objects = $database->get();

		$pages = array();
		foreach( $objects as $object ) {
			$page = new Page( $object );
			$pages[$page->id] = $page;
		}

		$this->pages = $pages;

		return $this;
	}

	function get( $page_id = false ) {

		if( ! $page_id ) return $this->pages;

		if( ! array_key_exists($page_id, $this->pages) ) return false;

		return $this->pages[$page_id];
	}

}

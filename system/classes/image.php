<?php


class Image {

	private $image_path;

	function __construct( $image_path) {

		$this->image_path = $image_path;

	}


	function get_html_embed( $type = false, $target_width = false ) {

		// TODO

		$embed = get_image_html( $this->image_path, $type, $target_width );

		return $embed;
	}


	function display() {

		// TODO

		handle_image_display( $this->image_path );
	}

	
}

<?php

class File {

	public $id;
	public $filename;
	public $raw_content;
	public $url;
	public $fields = array();

	function __construct( $eigenheim, $filename, $raw_content = false ) {

		// if we provide $raw_content, we need to create the file
		// TODO: check if we want to handle it like this, or if we need another way to create or read a file

		$this->eigenheim = $eigenheim; // TODO: how do we want to handle this?

		$filepath = $eigenheim->abspath.'content/'.$filename;

		if( $raw_content ) {
			$return = $this->create( $filepath, $raw_content );
			return $return;;
		}

		if( ! file_exists( $filepath) ) {
			$eigenheim->debug( 'file not found', $filepath );
			return false;
		}


		// fill out ->id

		$filepath_exp = explode( '/', $filepath );

		$id = $filepath_exp[count($filepath_exp)-2];
		$id_exp = explode('_', $id);

		$this->id = end($id_exp);


		// fill out ->filename
		$this->filename = $filename;


		// fill out ->raw_content
		$this->raw_content = file_get_contents( $filepath );


		// fill out ->url
		$this->url = url( $this->id );


		// fill out ->fields:

		$this->data = array();

		$file_content = str_replace("\r\n", "\n", $this->raw_content);

		$fields = explode( "\n\n----\n\n", $file_content );

		if( ! is_array($fields) ) $fields = array();

		$data = array();

		foreach( $fields as $field ) {

			$pos = strpos( $field, ':' );

			if( $pos === false ) continue;

			$field_name = substr( $field, 0, $pos );
			$field_content = substr( $field, $pos+1 );

			$field_name = strtolower(trim($field_name));
			$field_content = trim($field_content);

			$data[$field_name] = $field_content;

		}

		$this->fields = $data;

	}


	function create( $filepath, $raw_content ) {

		global $eigenheim;

		if( file_exists($filepath) ) {
			$eigenheim->debug( 'file exists already', $filepath );
			return false;
		}

		$return = file_put_contents( $filepath, $raw_content );

		if( $return !== false ) return true; // return true, even if 0 bytes were written

		return false;
	}


	function get_content() {
		return $this->raw_content;
	}


	function get_id() {
		return $this->id;
	}


	function get_fields() {
		return $this->fields;
	}


}

<?php

// Core Version: 0.1.0

class File {

	public $id;
	public $filename;
	public $timestamp;
	public $raw_content;
	public $url;
	public $fields = array();
	public $sort;

	function __construct( $core, $filename, $raw_content = false ) {

		// if we provide $raw_content, we need to create the file - TODO: check if we want to handle it like this, or if we need another way to create or read a file

		$this->eigenheim = $core; // TODO: how do we want to handle this?

		$filepath = $core->abspath.'content/'.$filename;

		if( $raw_content ) {
			$return = $this->create( $filepath, $raw_content );
			return $return;
		}

		if( ! file_exists( $filepath) ) {
			$core->debug( 'file not found', $filepath );
			return false;
		}


		// fill out ->id

		$filepath_exp = explode( '/', $filepath );

		$id = $filepath_exp[count($filepath_exp)-2];
		$id_exp = explode('_', $id);

		$this->id = end($id_exp);

		$sort = false;
		if( count($id_exp) > 1 ) $sort = str_replace("_".$this->id, "", $id);

		// fill out ->filename
		$this->filename = $filename;


		// fill out ->raw_content
		$this->raw_content = file_get_contents( $filepath );


		// fill out ->url
		$this->url = url( $this->id );


		// fill out ->fields:

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

		// timestamp fallback: file creation date
		if( empty($data['timestamp']) ) $data['timestamp'] = filemtime($filepath);

		$this->fields = $data;


		// fill out ->timestamp
		$this->timestamp = $data['timestamp'];


		// fill out ->sort
		if( ! $sort ) $sort = $data['timestamp']."_".$this->id;
		$this->sort = $sort;

	}


	function create( $filepath, $raw_content ) {

		global $core;

		if( file_exists($filepath) ) {
			$core->debug( 'file exists already', $filepath );
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

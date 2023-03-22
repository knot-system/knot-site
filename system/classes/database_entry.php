<?php


class Database_Entry {

	private $file;

	public $filename;
	public $filepath;
	public $org_filepath;

	public $slug;

	function __construct( $org_filepath ) {

		global $core;

		$this->org_filepath = $org_filepath;

		$filepath = $core->abspath.'content/'.$org_filepath;
		$this->filepath = $filepath;

		$filepath_exp = explode('/', $filepath);
		$filename = end($filepath_exp);
		$this->filename = $filename;


		$this->file = new File( $filepath );

		if( ! $this->file->exists() ) {
			$core->debug( 'file not found', $org_filepath );
			return false;
		}


		$org_filepath_exp = explode( '/', $org_filepath );

		$id = $org_filepath_exp[count($org_filepath_exp)-2];
		$id_exp = explode('_', $id);
		$this->slug = end($id_exp);
	}

	function get_id() {
		return $this->slug;
	}

	function get_fields() {

		$fields = $this->file->get();

		if( ! $fields ) $fields = [];

		if( ! isset($fields['timestamp']) ) {
			$timestamp = filemtime($this->filepath);
			$fields['timestamp'] = $timestamp;
		}

		return $fields;
	}

}

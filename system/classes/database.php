<?php


class Database {

	public $objects = array();

	function __construct( $folderpath_input, $recursive = false, $filename = false ) {

		global $core;

		$folderpath = $core->abspath.'content/'.$folderpath_input;

		$folder = new Folder( $folderpath );
		$files = $folder->get_content( $recursive, 'file' );

		if( ! count($files) ) return $this;

		$objects = [];
		foreach( $files as $file_path ) {

			$file = new Database_Entry( $folderpath_input.$file_path );

			if( $filename ) {
				if( strtolower($filename) != strtolower($file->filename) ) continue;
			}


			$org_filepath_exp = explode( '/', $file->org_filepath );
			$id = $org_filepath_exp[count($org_filepath_exp)-2];
			$id_exp = explode('_', $id);
			if( count($id_exp) > 1 ) {
				$sort = str_replace("_".$file->slug, "", $id);
			} else {
				$timestamp = filemtime($file->filepath);
				$sort = $timestamp."_".$file->slug;
			}

			$objects[$sort] = $file;

		}

		ksort( $objects );

		$this->objects = $objects;

		return $this;
	}


	function get() {
		return $this->objects;
	}


	function reverse() {
		$this->objects = array_reverse($this->objects);

		return $this;
	}

}

<?php

class Page {

	public $id;
	public $url;
	public $fields = array();

	function __construct( $file ) {

		$data = $file->get_fields();

		if( ! $data ) return false;

		if( ! isset($data['content']) ) return false;

		$id = $file->get_id();
		$this->id = $id;

		$content_html = $data['content'];

		$content_text = strip_tags($content_html); // TODO: revisit this in the future

		$text = new Text($content_html);
		$content_html = $text->cleanup()->get();

		$title = ucwords($id);
		if( ! empty($data['title']) ) $title = $data['title'];

		$url = $file->url;
		$this->url = $url;

		$this->fields = array(
			'id' => $id,
			'title' => $title,
			'content_html' => $content_html,
			'content_text' => $content_text,
			'permalink' => $url
		);

		return $this;

	}

	function get() {
		return $this->fields;
	}

}

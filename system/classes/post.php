<?php

class Post {

	public $id;
	public $fields = array();
	private $raw_data;

	function __construct( $file ) {

		$data = $file->get_fields();

		if( ! $data ) return false;

		if( ! isset($data['content']) ) return false;

		$this->eigenheim = $file->eigenheim; // TODO: how do we want to handle this?

		$this->raw_data = $data;
		$this->raw_file = $file;

		$id = $data['id'];
		$this->id = $id;

		return $this;
	}

	function initialize() {

		$data = $this->raw_data;

		$author = false;
		$author_information = get_author_information();
		if( ! empty( $author_information['display_name'] ) ) $author = $author_information['display_name'];

		$content_html = $data['content'];

		$content_text = strip_tags( $content_html ); // TODO: revisit this in the future

		$text = new Text($content_html);
		$content_html = $text->cleanup()->get();

		$link_preview = $text->get_link_preview();


		$image_html = false;
		$image_url = false;

		if( ! empty( $data['photo']) ) {
			$post_folder = trailing_slash_it(pathinfo( $this->raw_file->filename, PATHINFO_DIRNAME ));

			if( file_exists($this->eigenheim->abspath.'content/'.$post_folder.$data['photo']) ) {
				$image_path = $post_folder.$data['photo'];
				$image_html = get_image_html( $image_path );
				$image_url = url('content/'.$image_path, false);
			}

		}


		$title = '';
		if( ! empty($data['name']) ) $title = $data['name'];

		$tags = array();
		if( ! empty($data['category']) ) $tags = json_decode( $data['category'] ); 
		if( ! is_array($tags) ) $tags = array();

		$timestamp = $data['timestamp'];

		$permalink = url('post/'.$this->id.'/');

		$date_published = date( 'c', $timestamp );

		$date_modified = $date_published; // TODO: add modified date

		// this is the structure that the json feed wants for a post, see https://www.jsonfeed.org/version/1.1/ (with some additional fields we use elsewhere)
		$this->fields = array(
			'id' => $this->id,
			'title' => $title,
			'author' => $author,
			'permalink' => $permalink,
			'content_html' => $content_html,
			'content_text' => $content_text,
			'link_preview' => $link_preview,
			'image_html' => $image_html,
			'image' => $image_url,
			'tags' => $tags,
			'date_published' => $date_published,
			'date_modified' => $date_modified,
			'timestamp' => $timestamp,
		);

		return $this;
	}

	function get() {
		return $this->initialize()->fields;
	}

}

<?php

class Post {

	public $id;
	public $fields = array();

	function __construct( $file ) {

		global $eigenheim;

		$data = $file->get_fields();

		if( ! $data ) return false;

		if( ! isset($data['content']) ) return false;

		$author_information = get_author_information();
		$author = false;
		if( ! empty( $author_information['display_name'] ) ) $author = $author_information['display_name'];

		$content_html = $data['content'];

		$content_text = strip_tags( $content_html ); // TODO: revisit this in the future

		$text = new Text($content_html);
		$content_html = $text->text_cleanup()->get();

		$image = false;
		if( ! empty( $data['photo']) ) {
			$post_folder = trailing_slash_it(pathinfo( $file->filename, PATHINFO_DIRNAME ));

			if( file_exists($eigenheim->abspath.'content/'.$post_folder.$data['photo']) ) {
				$image_path = $post_folder.$data['photo'];
				$image_html = get_image_html( $image_path );
		
				$content_html = '<p>'.$image_html.'</p>'.$content_html;
			}

		}

		$title = '';
		if( ! empty($data['name']) ) $title = $data['name'];

		$tags = array();
		if( ! empty($data['category']) ) $tags = json_decode( $data['category'] ); 
		if( ! is_array($tags) ) $tags = array();

		$timestamp = $data['timestamp'];
		$id = $data['id'];
		$this->id = $id;

		$permalink = url('post/'.$id.'/');

		$date_published = date( 'c', $timestamp );

		$date_modified = $date_published; // TODO: add modified date

		// this is the structure that the json feed wants for a post, see https://www.jsonfeed.org/version/1.1/ (with some additional fields we use elsewhere)
		$this->fields = array(
			'id' => $id,
			'title' => $title,
			'author' => $author,
			'permalink' => $permalink,
			'content_html' => $content_html,
			'content_text' => $content_text,
			'tags' => $tags,
			'date_published' => $date_published,
			'date_modified' => $date_modified,
			'timestamp' => $timestamp,
			'image' => $image,
		);

		return $this;
	}

	function get() {
		return $this->fields;
	}

}

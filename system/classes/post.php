<?php


class Post {

	public $id;
	public $fields = array();
	private $raw_data;

	function __construct( $file ) {

		$data = $file->get_fields();

		if( ! $data ) return false;

		if( ! isset($data['content']) ) return false;

		$this->raw_data = $data;
		$this->raw_file = $file;

		$id = $data['id'];
		$this->id = $id;

		$tags = array();
		if( ! empty($data['category']) ) $tags = json_decode( $data['category'] ); 
		if( ! is_array($tags) ) $tags = array();
		$this->tags = $tags;

		$slug = $file->slug;
		$this->slug = $slug;

		return $this;
	}

	function initialize() {

		$data = $this->raw_data;

		$author = false;
		$author_information = get_author_information();
		if( ! empty( $author_information['display_name'] ) ) $author = $author_information['display_name'];

		$content_html = $data['content'];

		$content_text = strip_tags( $content_html ); // TODO: revisit this in the future

		$link_preview = false;
		if( $content_html ) {
			$text = new Text($content_html);
			$content_html = $text->cleanup()->get();

			$link_preview = $text->get_link_preview();
		}


		$image_html = false;
		$image_url = false;


		if( ! empty( $data['photo']) ) {


			$post_folder = trailing_slash_it(pathinfo( $this->raw_file->org_filepath, PATHINFO_DIRNAME ));

			global $core;
			if( file_exists($core->abspath.'content/'.$post_folder.$data['photo']) ) {
				$image_path = 'content/'.$post_folder.$data['photo'];

				$image = new Image( $image_path );
				$image_html = $image->get_html_embed();

				$image_url = url($image_path, false);
			}

		}


		$title = '';
		if( ! empty($data['name']) ) $title = $data['name'];

		$timestamp = $data['timestamp'];

		$permalink = url('post/'.$this->slug.'/');

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
			'tags' => $this->tags,
			'date_published' => $date_published,
			'date_modified' => $date_modified,
			'timestamp' => $timestamp,
		);

		if( doing_feed() ) {
			// json feed: remove obsolete fields:
			unset($this->fields['link_preview']);
			unset($this->fields['image_html']);
			unset($this->fields['timestamp']);
		}

		return $this;
	}

	function get() {
		return $this->initialize()->fields;
	}

}

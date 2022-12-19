<?php

Class Link {

	public $id;
	public $url;
	public $short_url;

	private $cache;

	function __construct( $input, $use_id = false ) {

		if( $use_id ) {

			$id = $input;

			$this->id = $id;
			$this->cache = new Cache( 'link', $id, true );

			$data = $this->cache->getData();

			if( ! $data ) {
				throw new Exception( 'no data' );
			}

			$data = json_decode($data);

			$this->url = $data->url;

		} else {

			$url = $input;

			$this->url = $url;
			$this->cache = new Cache( 'link', $url );
			$this->id = 'link-'.$this->cache->hash;

			$data = $this->cache->getData();
			if( ! $data ) {
				// create cache file with basic info
				$data = [
					'id' => $this->id,
					'url' => $url
				];
				$this->updatePreview( $data );
			}

		}

		$this->short_url = str_replace(array('https://','http://'), '', $this->url);
		$this->short_url = un_trailing_slash_it($this->short_url);
		
		return $this;
	}


	function getPreview() {

		$cache_content = $this->cache->getData();

		if( ! $cache_content ) return false;

		$data = json_decode($cache_content, true);


		$preview_title = '<span class="link-preview-title">'.$this->short_url.'</span>';
		$preview_image = '';
		$preview_description = '';
		if( ! empty($data['preview_image']) ) $preview_image = '<span class="link-preview-image">'.$data['preview_image'].'</span>';
		if( ! empty($data['title']) ) $preview_title = '<span class="link-preview-title">'.$data['title'].'</span>';
		if( ! empty($data['description']) ) $preview_description = '<span class="link-preview-description">'.$data['description'].'</span>';
		$preview_html = $preview_image.'<span class="link-preview-text">'.$preview_title.$preview_description;


		$data['preview_html'] = $preview_html;
		$data['preview_html_hash'] = get_hash( $preview_html );

		return $data;
	}


	function updatePreview( $data ) {

		$json = json_encode( $data );

		$this->cache->addData($json);

		return $this;
	}


	function getLinkInfo() {

		$url = $this->url;

		$html = request_get_remote( $url );

		$title = $this->extract_information( $html, '/<title>(.*?)<\/title>/is', $this->short_url );

		$description = $this->extract_information( $html, '/<meta.*?name="description".*?content="(.*?)".*?>/is' );
		if( ! $description ) {
			$description = $this->extract_information( $html, '/<meta.*?property="og:description".*?content="(.*?)".*?>/is' );
		}
		if( ! $description ) {
			$description = $this->extract_information( $html, '/<meta.*?property="twitter:description".*?content="(.*?)".*?>/is' );
		}

		$preview_image = $this->extract_information( $html, '/<meta.*?property="og:image".*?content="(.*?)".*?>/is' );
		if( ! $preview_image ) {
			$preview_image = $this->extract_information( $html, '/<meta.*?property="twitter:image".*?content="(.*?)".*?>/is' );
		}

		// TODO: maybe we want to get information from other meta tags as well. revisit this in the future

		if( $preview_image ) {
			// cache remote image locally
			$preview_image_name = explode('/', $preview_image);
			$preview_image_name = end($preview_image_name);
			$preview_image_cache = new Cache( 'remote-image', $preview_image_name );
			$preview_image_cache->get_remote_file( $preview_image );

			$preview_image = get_image_html( $preview_image_cache->hash, 'remote' );
		}

		$data = [
			'id' => $this->id,
			'url' => $url,
			'title' => $title,
			'preview_image' => $preview_image,
			'description' => $description,
			'last_refresh' => time()
		];

		$this->updatePreview( $data );

		return $this;
	}


	function extract_information( $html, $pattern, $return = false ) {

		if( preg_match( $pattern, $html, $matches ) ) {
			$return = $matches[1];
		}

		return $return;
	}


};
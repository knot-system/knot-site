<?php

Class Link {

	public $url;
	public $id;

	function __construct( $url ) {

		$this->url = $url;
		$this->short_url = str_replace(array('https://','http://'), '', $url);

		$this->cache = new Cache( 'link', $url );

		$this->id = 'link-'.$this->cache->hash;

		return $this;
	}


	function getPreview() {

		$cache_content = $this->cache->getData();

		if( ! $cache_content ) return false;

		$json = json_decode($cache_content);

		// TODO: check last_refresh, and maybe call getLinkInfo() ??

		return $json;
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

		$preview_image = $this->extract_information( $html, '/<meta.*?property="og:image".*?content="(.*?)".*?>/is' );

		if( $preview_image ) {
			// cache remote image locally
			$preview_image_name = explode('/', $preview_image);
			$preview_image_name = end($preview_image_name);
			$preview_image_cache = new Cache( 'remote-image', $preview_image_name );
			$preview_image_cache->get_remote_file( $preview_image );

			$preview_image = get_image_html( $preview_image_cache->hash, 'remote' );
		}

		$data = [
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

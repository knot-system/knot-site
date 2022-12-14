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

		$title = false;
		$pattern = '/<title>(.*?)<\/title>/is';
		if( preg_match( $pattern, $html, $matches ) ) {
			$title = $matches[1];
		} else {
			$title = $this->short_url;
		}

		// TODO: get og tags and preview thumbnail and so on

		$data = [
			'url' => $url,
			'title' => $title,
			'last_refresh' => time()
		];

		$this->updatePreview( $data );

		return $this;
	}


};

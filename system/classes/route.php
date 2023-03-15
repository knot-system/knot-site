<?php

// update: 2023-03-15

class Route {

	public $route;

	function __construct( $core ) {

		$request = $_SERVER['REQUEST_URI'];
		$request = preg_replace( '/^'.preg_quote($core->basefolder, '/').'/', '', $request );

		$query_string = false;

		$request = explode( '?', $request );
		if( count($request) > 1 ) $query_string = $request[1];
		$request = $request[0];

		$request = explode( '/', $request );

		$pagination = 0;

		if( ! empty($request[0]) && $request[0] == 'feed' && ! empty($request[1]) ){
			// feeds

			if( $request[1] == 'rss' ) {
				// rss
				$this->route = array(
					'template' => 'rss',
				);
			} elseif( $request[1] == 'json' ){
				// json
				$this->route = array(
					'template' => 'json',
				);
			} else {
				$this->route = array(
					'template' => '404'
				);
			}

		} elseif( ! empty($request[0]) && $request[0] == 'post' && ! empty($request[1]) ){
			// single post view

			$slug = $request[1];
			$post_id = get_post_id_from_slug($slug);

			$post = false;
			if( $post_id ) $post = $core->posts->get( $post_id );

			if( $post ) {
				$this->route = array(
					'template' => 'post',
					'args' => array(
						'post_id' => $post_id,
						'post' => $post
					)
				);
			} else {
				$this->route = array(
					'template' => '404',
				);
			}

		} elseif( ! empty($request[0]) && $request[0] == 'tag' && ! empty($request[1]) ){
			// single tag view

			$tag = $request[1];

			$core->posts->filter_by_tag( $tag );

			$pagination = 0;
			if( ! empty($request[2]) && $request[2] == 'page' && isset($request[3]) ) {
				$pagination = (int)$request[3];
			}

			if( $pagination < 1 ) $pagination = 1;
			$core->posts->paginate($pagination);

			if( count($core->posts->get()) > 0 ) {
				$this->route = array(
					'template' => 'tag',
					'args' => array(
						'tag' => $tag,
						'page' => $pagination
					)
				);
			} else {
				$this->route = array(
					'template' => '404',
				);
			}

		} elseif( ! empty($request[0]) && $request[0] == micropub_get_endpoint() ) {
			// micropub

			if( micropub_check_request() ) {
				exit;
			}

			$this->route = array(
				'template' => '404'
			);

		} elseif( ! empty($request[0]) && $request[0] == api_get_endpoint() ) {
			// api
			// NOTE: this is subject to change!

			if( api_check_request() ) {
				exit;
			}

			$this->route = array(
				'template' => '404'
			);

		} elseif( ! empty($request[0]) && $request[0] == 'page' && isset($request[1]) ) {
			// overview, pagination

			$pagination = (int) $request[1];

			if( $pagination < 1 ) $pagination = 1;
			$core->posts->paginate($pagination);

			$this->route = array(
				'template' => 'index',
				'args' => array(
					'page' => $pagination
				)
			);

		} elseif( ! empty($request[0]) && $request[0] == 'content' ) {
			// maybe image

			$filename = strtolower(end($request));
			$filename = str_replace('.jpeg', '.jpg', $filename);
			if( str_ends_with($filename, '.jpg') || str_ends_with($filename, '.png') ){
				handle_image_display( urldecode(implode('/', $request)) );
				exit;
			}

			$this->route = array(
				'template' => '404'
			);

		} elseif( ! empty($request[0]) && $request[0] == 'remote-image' ) {
			// maybe cached remote image

			$hash = $request[1];

			if( file_exists($core->abspath.'cache/remote-image/'.$hash) ) {
				handle_image_display( $core->abspath.'cache/remote-image/'.$hash );
				exit;
			}
			

			$this->route = array(
				'template' => '404'
			);

		} elseif( ! empty($request[0]) ) {
			// maybe static page

			$page_id = $request[0];
			$page = $core->pages->get($page_id);
			if( $page ) {
				$this->route = array(
					'template' => 'page',
					'args' => array(
						'page_id' => $page_id,
						'page' => $page
					)
				);
			} else {
				$this->route = array(
					'template' => '404'
				);
			}

		} else {
			// default overview (posts pagination 1)

			$core->posts->paginate(1);
			$this->route = array(
				'template' => 'index',
				'args' => array(
					'page' => 1
				)
			);
		}

		return $this;
	}

	function get( $name = false ) {

		if( $name ) {

			if( ! is_array($this->route) ) return false;

			if( ! array_key_exists($name, $this->route) ) return false;

			return $this->route[$name];
		}

		return $this->route;
	}
	
}

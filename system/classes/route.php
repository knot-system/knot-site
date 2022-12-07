<?php

class Route {

	public $route;

	function __construct( $eigenheim ) {

		$request = $_SERVER['REQUEST_URI'];
		$request = preg_replace( '/^'.preg_quote($eigenheim->basefolder, '/').'/', '', $request );
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
			}

		} elseif( ! empty($request[0]) && $request[0] == 'post' && ! empty($request[1]) ){
			// single post view

			$post_id = $request[1];
			$post = get_post( $post_id );

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
			$posts = get_posts_by_tag( $tag );

			$pagination = 0;
			if( ! empty($request[2]) && $request[2] == 'page' && isset($request[3]) ) {
				$pagination = (int)$request[3];
			}

			if( ! count($posts->get()) ) {
				$this->route = array(
					'template' => '404',
				);
			}

			if( $pagination < 1 ) $pagination = 1;
			$eigenheim->posts->paginate($pagination);

			$this->route = array(
				'template' => 'tag',
				'args' => array(
					'tag' => $tag,
					'page' => $pagination
				)
			);

		} elseif( ! empty($request[0]) && $request[0] == micropub_get_endpoint() ) {
			// micropub

			// TODO: return micropub template here? or maybe return a function instead of a template?
			// the 'exit' should maybe not happen here, but in the root index.php
			micropub_check_request();
			exit;

		} elseif( ! empty($request[0]) && $request[0] == 'page' && isset($request[1]) ) {
			// overview, pagination
			$pagination = (int)$request[1];

		} elseif( ! empty($request[0]) && $request[0] == 'content' ) {
			// maybe image

			$filename = strtolower(end($request));
			$filename = str_replace('.jpeg', '.jpg', $filename);
			if( str_ends_with($filename, '.jpg') || str_ends_with($filename, '.png') ){
				handle_image_display( implode('/', $request) );
				exit;
			}

		} elseif( ! empty($request[0]) ) {
			// maybe static page

			$page_id = $request[0];
			$page = $eigenheim->pages->get($page_id);
			if( $page ) {
				$this->route = array(
					'template' => 'page',
					'args' => array(
						'page_id' => $page_id,
						'page' => $page
					)
				);
			}

		}

		// default overview (may be paginated)
		if( $pagination < 1 ) $pagination = 1;
		$eigenheim->posts->paginate($pagination);

		$this->route = array(
			'template' => 'index',
			'args' => array(
				'page' => $pagination
			)
		);

		return $this;
	}

	function get( $name = false ) {

		if( $name ) {
			if( ! array_key_exists($name, $this->route) ) return false;

			return $this->route[$name];
		}

		return $this->route;
	}
	
}

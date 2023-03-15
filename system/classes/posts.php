<?php

// Core Version: 0.1.0

class Posts {

	public $posts = array();
	public $tag = false;
	public $page;
	public $maxPage;

	function __construct( $core ){

		$this->eigenheim = $core; // TODO: how do we want to handle this?

		$database = new Database( $core, 'posts/', true, 'post.txt' );
		$objects = $database->reverse()->get();

		$posts = array();
		foreach( $objects as $object ) {
			$post = new Post( $object );
			$posts[$post->id] = $post;
		}

		$this->posts = $posts;

		$posts_per_page = $core->config->get( 'posts_per_page' );
		$maxPage = count($posts) / $posts_per_page;

		$this->page = 1;
		$this->maxPage = ceil($maxPage);

		return $this;
	}


	function get( $post_id = false ) {

		if( $post_id && ! array_key_exists($post_id, $this->posts) ) return false;

		if( $post_id ) return $this->posts[$post_id]->initialize();

		foreach( $this->posts as $post ) {
			$post->initialize(); // TODO: check this
		}

		return $this->posts;
	}


	function paginate( $page ) {

		$posts_per_page = $this->eigenheim->config->get( 'posts_per_page' );

		$offset = ($page-1)*$posts_per_page;

		$oldCount = count($this->posts);

		$this->posts = array_slice( $this->posts, $offset, $posts_per_page );

		$this->page = $page;

		return $this;
	}


	function filter_by_tag( $tag ) {

		$posts = array();

		foreach( $this->posts as $post_id => $post ) {
			if( empty($post->tags) || ! is_array($post->tags) || ! count($post->tags) ) continue;

			if( ! in_array( $tag, $post->tags) ) continue;

			$posts[$post_id] = $post;
		}

		$this->tag = $tag;

		$this->posts = $posts;

		$posts_per_page = $this->eigenheim->config->get( 'posts_per_page' );
		$this->maxPage = count($posts) / $posts_per_page;

		return $this;
	}


	function limit( $number ) {

		$this->posts = array_slice( $this->posts, 0, $number );

		return $this;
	}


	function categories() {

		$posts = $this->posts;

		if( ! $posts ) return array();

		$categories = array();
		foreach( $posts as $post ){
			foreach( $post->tags as $tag ) {
				$categories[] = $tag;
			}
		}

		$categories = array_unique( $categories );
		$categories = array_filter( $categories ); // remove empty entries
		$categories = array_values( $categories ); // get rid of keys

		return $categories;
	}

}

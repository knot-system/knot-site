<?php

class Posts {

	public $posts = array();
	public $tag = false;
	public $page;
	public $maxPage;

	function __construct( $eigenheim ){

		$this->eigenheim = $eigenheim; // TODO: how do we want to handle this?

		$database = new Database( $eigenheim, 'posts/', true, 'post.txt' );
		$objects = $database->reverse()->get();

		$posts = array();
		foreach( $objects as $object ) {
			$post = new Post( $object );
			$posts[$post->id] = $post;
		}

		$this->posts = $posts;

		$posts_per_page = $eigenheim->config->get( 'posts_per_page' );
		$maxPage = count($posts) / $posts_per_page;

		$this->page = 1;
		$this->maxPage = ceil($maxPage);

		return $this;
	}


	function get( $post_id = false ) {

		if( ! $post_id ) return $this->posts;

		if( ! array_key_exists($post_id, $this->posts) ) return false;

		return $this->posts[$post_id];
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
			if( empty($post->fields['tags']) || ! is_array($post->fields['tags']) || ! count($post->fields['tags']) ) continue;

			if( ! in_array( $tag, $post->fields['tags']) ) continue;

			$posts[$post_id] = $post;
		}

		$this->tag = $tag;

		$this->posts = $posts;

		$posts_per_page = $this->eigenheim->config->get( 'posts_per_page' );
		$this->maxPage = count($posts) / $posts_per_page;

		return $this;
	}

}

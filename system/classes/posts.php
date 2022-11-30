<?php

class Posts {

	public $posts = array();

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

		$this->posts = array_slice( $this->posts, $offset, $posts_per_page );

		return $this;
	}


	function filter_by_tag( $tag ) {

		$posts = array();

		foreach( $this->posts as $post_id => $post ) {
			if( empty($post->fields['tags']) || ! is_array($post->fields['tags']) || ! count($post->fields['tags']) ) continue;

			if( ! in_array( $tag, $post->fields['tags']) ) continue;

			$posts[$post_id] = $post;
		}

		$this->posts = $posts;

		return $this;
	}

}

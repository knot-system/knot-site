<?php

class Posts {

	public $posts = array();

	function __construct( $eigenheim ){

		$database = new Database( $eigenheim, 'posts/', true, 'post.txt' );
		$objects = $database->reverse()->get();

		$posts = array();
		foreach( $objects as $object ) {
			$post = new Post( $object );
			$posts[] = $post;
		}

		$this->posts = $posts;

		return $this;
	}

	function get( $post_id = false ) {

		if( ! $post_id ) return $this->posts;

		if( ! array_key_exists($post_id, $this->posts) ) return false;

		return $this->posts[$post_id];
	}

}

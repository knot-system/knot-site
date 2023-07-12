<?php

// this file creates some basic files and folderstructure and gets called, if important files are missing (like the config.php or .htaccess)


$message_output = false;
$debug_output = true;

if( isset($_REQUEST['debug']) ) $message_output = true;

$basefolder = str_replace( 'index.php', '', $_SERVER['PHP_SELF']);

if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
else $baseurl = 'http://';
$baseurl .= $_SERVER['HTTP_HOST'];
$baseurl .= $basefolder;


if( file_exists($abspath.'config.php') && file_exists($abspath.'.htaccess') ) {

	if( $message_output ) {
		?>
		<p>Setup already finished. Please delete <em>config.php</em> or <em>.htaccess</em> from the root directory to re-run the setup.</p>
		<?php
	}
	return;
}

if( $message_output ) {
	?>
	<p>Hi. This is the first-time setup of Eigenheim.</p>
	<p>We create some files and folders to get everything up and running.</p>

	<hr>

	<h3>Environment:</h3>
	<ul>
		<li>ABSPATH: <em><?= $abspath ?></em></li>
		<li>BASEFOLDER: <em><?= $basefolder ?></em></li>
		<li>BASEURL: <em><?= $baseurl ?></em></li>
	</ul>

	<?php
}

if( $abspath == '' ) {

	if( $debug_output ) {
		?>
		<p><strong>ERROR:</strong> ABSPATH is empty. we don't know what went wrong. we abort the setup here.</p>
		<?php
	}

	exit;
}
if( $basefolder == '' ) {

	if( $debug_output ) {
		?>
		<p><strong>ERROR:</strong> BASEFOLDER is empty. we don't know what went wrong. we abort the setup here.</p>
		<?php
	}

	exit;
}
if( $baseurl == '' ) {

	if( $debug_output ) {
		?>
		<p><strong>ERROR:</strong> BASEURL is empty. we don't know what went wrong. we abort the setup here.</p>
		<?php
	}

	exit;
}

$config = true;
if( file_exists($abspath.'config.php') ) $config = false;

if( $config && 
	( empty($_REQUEST['author_name']) || empty($_REQUEST['site_title']) || empty($_REQUEST['indieauth-metadata']) ) 
	) {
	?>
	<hr>
	<p>Please fill out these fields:</p>
	<form action="<?= $baseurl ?>" method="POST">
		<p><label><strong>Site Title</strong><br><input type="text" name="site_title" required></label></p>
		<p><label><strong>Author Name</strong><br><input type="text" name="author_name" required></label></p>
		<p><label><strong>IndieAuth Metadata Discovery Endpoint</strong><br><input type="url" name="indieauth-metadata" required></label></p>
		<p><small>(all fields above are required)</small></p>
		<p><label><input type="checkbox" name="testcontent" value="true" checked>create test content</label>
		<p><button>start installation</button></p>
	</form>
	<?php
	exit;
}

$testcontent = false;
if( $config ) {
	$site_title = $_REQUEST['site_title'];
	$author_name = $_REQUEST['author_name'];
	if( ! empty($_REQUEST['testcontent']) ) $testcontent = true;
}


if( $message_output ) {
	?>

	<hr>

	<h3>checking <em>.htaccess</em> file:</h3>
	<ul>
		<li>checking if <em>.htaccess</em> file exists</li>
	<?php
}

if( ! file_exists( $abspath.'.htaccess' ) ) {
	$rewrite_base = $basefolder;
	if( $rewrite_base == '' ) $rewrite_base = '/';

	if( $message_output ) {
		?>
		<li>file <em>.htaccess</em> does not exist, creating it with rewrite base <em><?= $rewrite_base ?></em></li>
		<?php
	}

	$content = "# BEGIN eigenheim\r\n<IfModule mod_rewrite.c>\r\nRewriteEngine on\r\nRewriteBase ".$rewrite_base."\r\n\r\nRewriteRule ^theme/[^/]+/assets/(.*)$ - [L]\r\nRewriteRule ^system/site/assets/(.*)$ - [L]\r\nRewriteRule (^|/)\.(?!well-known\/) index.php [L]\r\nRewriteRule ^content/(.*)\.(txt|md|mdown)$ index.php [L]\r\nRewriteRule ^content/(.*)\.(jpg|jpeg|png)$ index.php [L]\r\nRewriteRule ^theme/(.*) index.php [L]\r\nRewriteRule ^system/(.*) index.php [L]\r\nRewriteRule ^log/(.*) index.php [L]\r\nRewriteRule ^cache/(.*) index.php [L]\r\n\r\nRewriteCond %{REQUEST_FILENAME} !-d\r\nRewriteCond %{REQUEST_FILENAME} !-f\r\nRewriteRule . index.php [L]\r\n</IfModule>\r\n# END eigenheim\r\n";
	if( file_put_contents( $abspath.'.htaccess', $content ) === false ) {

		if( $message_output ) {
			?>
			<li><strong>ERROR:</strong> file <em>.htaccess</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
			<?php
		}

		exit;
	} else {

		if( $message_output ) {
			?>
			<li>file <em>.htaccess</em> was successfully created</li>
			<?php
		}

	}
} else {

	if( $message_output ) {
		?>
		<li>file <em>.htaccess</em> exists; if you need to recreate it, delete it and rerun this setup.</li>
		<?php
	}

}

if( $message_output ) {
	?>
	</ul>

	<h3>checking <em>content/</em> folder:</h3>
	<ul>
	<?php
}

if( ! is_dir( $abspath.'content/') ) {

	if( $message_output ) {
		?>
		<li>folder <em>content/</em> does not exist, trying to create it</li>
		<?php
	}

	$oldumask = umask(0); // we need this for permissions of mkdir to be set correctly
	if( mkdir( $abspath.'content/', 0777, true ) === false ) {

		if( $debug_output ) {
			?>
			<li><strong>ERROR:</strong> folder <em>content/</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
			<?php
		}

		exit;
	} else {

		if( $message_output ) {
			?>
			<li>folder <em>content/</em> was created successfully</li>
			<?php
		}

	}
	umask($oldumask); // we need this after changing permissions with mkdir

} else {

	if( $message_output ) {
		?><li>folder <em>content/</em> already exists, we do not need to create it</li><?php
	}

}

if( $message_output ) {
	?>
	</ul>


	<h3>checking <em>cache/</em> folder:</h3>
	<ul>
	<?php
}

if( ! is_dir( $abspath.'cache/') ) {

	if( $message_output ) {
		?>
		<li>folder <em>cache/</em> does not exist, trying to create it</li>
		<?php
	}

	if( mkdir( $abspath.'cache/', 0774, true ) === false ) {

		if( $debug_output ) {
			?>
			<li><strong>ERROR:</strong> folder <em>cache/</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
			<?php
		}

		exit;
	} else {

		if( $message_output ) {
			?>
			<li>folder <em>cache/</em> was created successfully</li>
			<?php
		}

	}
} else {

	if( $message_output ) {
		?><li>folder <em>cache/</em> already exists, we do not need to create it</li><?php
	}

}

if( $message_output ) {
	?>
	</ul>
	<?php
}


if( $testcontent ) {

	if( $message_output ) {
		?>
		<h3>creating some test content:</h3>
		<ul>
		<?php
	}

	function setup_create_testcontent( $root, $file_structure ) {

		global $message_output;
		
		if( $file_structure['type'] == 'file' ) {
			$filename = $file_structure['name'];
			$data = $file_structure['content'];
			if( ! file_exists( $root.$filename ) ){

				if( $message_output ) {
					?>
					<li>file <em><?= $root.$filename ?></em> does not exist, we need to create it</li>
					<?php
				}

				if( file_put_contents( $root.$filename, $data ) === false ) {

					if( $message_output ) {
						?>
						<li><strong>ERROR:</strong> could not create the file <em><?= $root.$filename ?></em>. we abort the setup here.</li>
						<?php
					}

					exit;
				}
			} else {

				if( $message_output ) {
					?>
					<li>file <em><?= $root.$filename ?></em> already exists, we do not need to create it</li>
					<?php
				}

			}
		} elseif( $file_structure['type'] == 'folder' ) {
			$foldername = $file_structure['name'];
			if( ! is_dir( $root.$foldername) ) {

				if( $message_output ) {
					?>
					<li>folder <em><?= $root.$foldername ?></em> does not exist, we need to create it</li>
					<?php
				}

				$oldumask = umask(0); // we need this for permissions of mkdir to be set correctly
				if( mkdir( $root.$foldername, 0777, true ) === false ) {

					if( $debug_output ) {
						?>
						<li><strong>ERROR:</strong> could not create the folder <em><?= $root.$foldername ?></em>. we abort the setup here.</li>
						<?php
					}

					exit;
				}
				umask($oldumask); // we need this after changing permissions with mkdir

			} else {

				if( $message_output ) {
					?>
					<li>folder <em><?= $root.$foldername ?></em> already exist, we do not need to create it</li>
					<?php
				}

			}
		}

		if( ! empty($file_structure['content']) && is_array($file_structure['content']) ) {
			$root .= $file_structure['name'].'/';
			foreach( $file_structure['content'] as $substructure ) {
				setup_create_testcontent( $root, $substructure );
			}
		}
	}

	$timestamp = time();

	$file_structure = array(
		'type' => 'root',
		'name' => '',
		'content' => array(
			array(
				'type' => 'folder',
				'name' => 'content',
				'content' => array(
					array(
						'type' => 'folder',
						'name' => '1_about',
						'content' => array(
							array(
								'type' => 'file',
								'name' => 'page.txt',
								'content' => "title: About\r\n\r\n----\r\n\r\ncontent: This is a Testpage. You can edit it at <em>content/1_about/page.txt</em>."
							)
						)
					),
					array(
						'type' => 'folder',
						'name' => '2_imprint',
						'content' => array(
							array(
								'type' => 'file',
								'name' => 'page.txt',
								'content' => "title: Imprint\r\n\r\n----\r\n\r\ncontent: This is a Testpage. You can edit it at <em>content/2_imprint/page.txt</em>."
							)
						)
					),
					array(
						'type' => 'folder',
						'name' => 'posts',
						'content' => array(
							array(
								'type' => 'folder',
								'name' => date('Y', $timestamp),
								'content' => array(
									array(
										'type' => 'folder',
										'name' => date('m', $timestamp),
										'content' => array(
											array(
												'type' => 'folder',
												'name' => date('Y-m-d_H-i-s', $timestamp).'_testpost',
												'content' => array(
													array(
														'type' => 'file',
														'name' => 'post.txt',
														'content' => "h: entry\r\n\r\n----\r\n\r\nname: Testpost\r\n\r\n----\r\n\r\ncontent: This is a first testpost. It is saved at <em>content/posts/".date('Y', $timestamp)."/".date('m', $timestamp)."/".date('Y-m-d_H-i-s', $timestamp)."_testpost/post.txt</em>\r\n\r\n----\r\n\r\npost-status: published\r\n\r\n----\r\n\r\ncategory: [\"eigenheim\",\"testpost\"]\r\n\r\n----\r\n\r\nslug: testpost\r\n\r\n----\r\n\r\ntimestamp: ".$timestamp."\r\n\r\n----\r\n\r\ndate: ".date('c', $timestamp)."\r\n\r\n----\r\n\r\nid: testpost"
													)
												)
											)
										)
									)
								)
							)
						)
					),
				)
			),
		),
	);
	setup_create_testcontent( $abspath, $file_structure );

	if( $message_output ) {
		?>
		<li>test folder structure created successfully</li>
		</ul>
		<?php
	}

}


if( $message_output ) {
	?>
	<h3>creating the <em>config.php</em> file:</h3>
	<ul>
		<?php
	}

	if( $config ) {

		$content = "<?php\r\n\r\nreturn [\r\n	'site_title' => '".$site_title."',\r\n	'author' => [\r\n		'p-name' => '".$author_name."',\r\n	],";

		if( ! empty($_REQUEST['baseurl_overwrite']) ) {
			$content .= "\r\n	'baseurl_overwrite' => '".$_REQUEST['baseurl_overwrite']."',";
		}
	
		if( ! empty($_REQUEST['basefolder_overwrite']) ) {
			$content .= "\r\n	'basefolder_overwrite' => '".$_REQUEST['basefolder_overwrite']."',";
		}

		if( ! empty($_REQUEST['microsub']) ) {
			$content .= "\r\n	'microsub' => '".$_REQUEST['microsub']."',";
		}

		if( ! empty($_REQUEST['indieauth-metadata']) ) {
			$content .= "\r\n	'indieauth-metadata' => '".$_REQUEST['indieauth-metadata']."',";
		}


		$content .= "\r\n];\r\n";

		if( file_put_contents( $abspath.'config.php', $content ) === false ) {

			if( $debug_output ) {
				?>
				<li><strong>ERROR:</strong> could not create the file <em>config.php</em>. make sure the folder is writeable. we abort the setup here.</li>
				<?php
			}
			exit;

		} else {

			if( $message_output ) {
				?>
				<li>file <em>config.php</em> created successfully</li>
				<?php
			}

		}
	} else {

		if( $message_output ) {
			?>
			<li>file <em>config.php</em> exists; if you need to recreate it, delete it and rerun this setup.</li>
			<?php
		}

	}

if( $message_output ) {
	?>
	</ul>


	<hr>
	<h3>Setup finished!</h3>
	<p>please <a href="<?= $baseurl ?>">reload this page</a>.</p>
	<hr>
	<?php
	
	exit;
}

header( 'location: '.$baseurl );

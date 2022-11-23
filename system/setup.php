<?php

// this file creates some basic files and folderstructure and gets called, if no config.php exists yet.

if( ! defined( 'EH_ABSPATH' ) ) exit;

if( file_exists(EH_ABSPATH.'config.php') ) {
	?>
	<p>Setup already finished. Please delete <em>config.php</em> to re-run the setup.</p>
	<?php
	exit;
}

?>
<p>Hi. This is the first-time setup of Eigenheim <?= eigenheim_get_version() ?>.</p>
<p>We create some files and folders to get everything up and running.</p>

<hr>

<p><strong>Environment:</strong></p>
<ul>
	<li>ABSPATH: <em><?= EH_ABSPATH ?></em></li>
	<li>BASEFOLDER: <em><?= EH_BASEFOLDER ?></em></li>
	<li>BASEURL: <em><?= EH_BASEURL ?></em></li>
</ul>

<?php

if( empty($_POST['auth_mail']) || empty($_POST['author_name']) || empty($_POST['site_title']) ) {

	?>
	<hr>
	<p>Please fill out these fields:</p>
	<form action="<?= EH_BASEURL ?>" method="POST">
		<p><label><strong>Site Title</strong><br><input type="text" name="site_title" required></label></p>
		<p><label><strong>Authorization Mail</strong><br><small>(this is were we send the login token to, when you log into a micropub client. It is not displayed publicly, but is added to the HTML source code)</small><br><input type="email" name="auth_mail" required></label></p>
		<p><label><strong>Author Name</strong><br><input type="text" name="author_name" required></label></p>
		<p><small>(all fields are required)</small></p>
		<p><button>start installation</button></p>
	</form>
	<?php

	exit;
}

$site_title = $_POST['site_title'];
$auth_mail = $_POST['auth_mail'];
$author_name = $_POST['author_name'];
?>

<hr>

<?php
if( EH_ABSPATH == '' ) {
	?>
	<p><strong>ERROR:</strong> ABSPATH is empty. we don't know what went wrong. we abort the setup here.</p>
	<?php
}
if( EH_BASEFOLDER == '' ) {
	?>
	<p><strong>ERROR:</strong> BASEFOLDER is empty. we don't know what went wrong. we abort the setup here.</p>
	<?php
}
if( EH_BASEURL == '' ) {
	?>
	<p><strong>ERROR:</strong> BASEURL is empty. we don't know what went wrong. we abort the setup here.</p>
	<?php
}
?>

<hr>

<p><strong>Step 1:</strong> checking <em>.htaccess</em> file:</p>
<ul>
	<li>checking if <em>.htaccess</em> file exists</li>
<?php
if( ! file_exists( EH_ABSPATH.'.htaccess' ) ) {
	$rewrite_base = EH_BASEFOLDER;
	if( $rewrite_base == '' ) $rewrite_base = '/';
	?>
	<li>file <em>.htaccess</em> does not exist, creating it with rewrite base <em><?= $rewrite_base ?></em></li>
	<?php
	$content = "<IfModule mod_rewrite.c>\nRewriteEngine on\nRewriteBase ".$rewrite_base."\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule . index.php [L]\n</IfModule>";
	if( file_put_contents( EH_ABSPATH.'.htaccess', $content ) === false ) {
		?>
		<li><strong>ERROR:</strong> file <em>.htaccess</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
		<?php
		exit;
	} else {
		?>
		<li>file <em>.htaccess</em> was successfully created</li>
		<?php
	}
} else {
	?>
	<li>file <em>.htaccess</em> exists; if you need to recreate it, delete it and rerun the setup.</li>
	<?php
}
?>
</ul>

<p><strong>Step 2:</strong> checking <em>content/</em> folder:</p>
<ul>
<?php
if( ! is_dir( EH_ABSPATH.'content/') ) {
	?>
	<li>folder <em>content/</em> does not exist, trying to create it</li>
	<?php
	if( mkdir( EH_ABSPATH.'content/', 0777, true ) === false ) {
		?>
		<li><strong>ERROR:</strong> folder <em>content/</em> could not be created. Please check the permissions of the root folder and make sure we are allowed to write to it. we abort the setup here.</li>
		<?php
		exit;
	} else {
		?>
		<li>folder <em>content/</em> was created successfully</li>
		<?php
	}
} else {
	?><li>folder <em>content/</em> already exists, we do not need to create it</li><?php
}
?>
</ul>

<p><strong>Step 3:</strong> creating some test content:</p>
<ul>
<?php
function setup_create_testcontent( $root, $file_structure ) {

	if( $file_structure['type'] == 'file' ) {
		$filename = $file_structure['name'];
		$data = $file_structure['content'];
		if( ! file_exists( $root.$filename ) ){
			?>
			<li>file <em><?= $root.$filename ?></em> does not exist, we need to create it</li>
			<?php
			if( file_put_contents( $root.$filename, $data ) === false ) {
				?>
				<li><strong>ERROR:</strong> could not create the file <em><?= $root.$filename ?></em>. we abort the setup here.</li>
				<?php
				exit;
			}
		} else {
			?>
			<li>file <em><?= $root.$filename ?></em> already exists, we do not need to create it</li>
			<?php
		}
	} elseif( $file_structure['type'] == 'folder' ) {
		$foldername = $file_structure['name'];
		if( ! is_dir( $root.$foldername) ) {
			?>
			<li>folder <em><?= $root.$foldername ?></em> does not exist, we need to create it</li>
			<?php
			if( mkdir( $root.$foldername, 0777, true ) === false ) {
				?>
				<li><strong>ERROR:</strong> could not create the folder <em><?= $root.$foldername ?></em>. we abort the setup here.</li>
				<?php
				exit;
			}
		} else {
			?>
			<li>folder <em><?= $root.$foldername ?></em> already exist, we do not need to create it</li>
			<?php
		}
	}

	if( ! empty($file_structure['content']) && is_array($file_structure['content']) ) {
		$root .= $file_structure['name'].'/';
		foreach( $file_structure['content'] as $substructure ) {
			setup_create_testcontent( $root, $substructure );
		}
	}
}

$file_structure = array(
	'type' => 'root',
	'name' => '',
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
		),
	)
);
setup_create_testcontent( EH_ABSPATH.'content/', $file_structure );

?>
<li>test folder structure created successfully</li>
<?php
if( ! is_dir(EH_ABSPATH.'content/posts/'.date('Y')) ) {
	?>
	<li>creating test post</li>
	<?php
	$testpost_data = array(
		'timestamp' => time(),
		'date' => date('c', time()),
		'category' => json_encode(array('testpost')),
		'post-status' => 'published',
		'name' => 'Testpost',
		'content' => 'This is a first testpost. It is saved at <em>content/posts/'.date('Y').'/'.date('m').'/</em>.'
	);
	if( ! database_create_post( $testpost_data ) ) {
		?>
		<li><strong>ERROR:</strong> could not create testpost. we abort the setup here.</li>
		<?php
		exit;
	} else {
		?>
		<li>test post created successfully</li>
		<?php
	}
}
?>
</ul>

<p><strong>Step 4:</strong> creating the <em>config.php</em> file:</p>
<ul>
<?php
$content = "<?php\r\n\r\nreturn [\r\n	'site_title' => '".$site_title."',\r\n	'auth_mail' => '".$auth_mail."',\r\n	'author' => [\r\n		'p-name' => '".$author_name."',\r\n	],\r\n];\r\n";
if( file_put_contents( EH_ABSPATH.'config.php', $content ) === false ) {
	?>
	<li><strong>ERROR:</strong> could not create the file <em>config.php</em>. make sure the folder is writeable. we abort the setup here.</li>
	<?php
} else {
	?>
	<li>file <em>config.php</em> created successfully</li>
	<?php
}
?>
</ul>
<hr>
<p><strong>Setup finished!</strong> - please <a href="<?= EH_BASEURL ?>">reload this page</a>.</p>
<hr>
<?php

// this file can update the system with the latest release from github. create a empty file called 'update' or 'update.txt' in the root directory, to trigger the update

if( ! $eigenheim ) exit;

$step = false;
if( ! empty($_GET['step']) ) $step = $_GET['step'];
	
?>
<h1>Eigenheim System Update</h1>
<?php

$api_url = 'https://api.github.com/repos/maxhaesslein/eigenheim/releases';

$old_version = $eigenheim->get_version();

if( $step == 'check' ) {

	$json = get_remote_json( $api_url );
	
	if( ! $json || ! is_array($json) ) {
		?>
		<p><strong>Error:</strong> could not get release information from GitHub</p>
		<?php
		exit;
	}

	$latest_release = $json[0];

	$release_name = $latest_release->name;
	$release_notes = $latest_release->body;

	?>
	<p>Latest release: <strong><?= $release_name ?></strong><br>
	Currently installed: <strong><?= $old_version ?></strong></p>
	<?php

	$new_version_available = false;
	if( $release_name != $old_version ) {
		$version_number_old = explode('.', str_replace('alpha.', '0.0.', $old_version));
		$version_number_new = explode('.', str_replace('alpha.', '0.0.', $release_name));

		for( $i = 0; $i < count($version_number_new); $i++ ){
			$dot_old = $version_number_old[$i];
			$dot_new = $version_number_new[$i];
			if( $dot_new > $dot_old ) $new_version_available = true;
		}
		
		if( $new_version_available ) {
			echo '<p><strong>New version available!</strong> You should update your system.</p>';
		}
	}

	?>
	<p><strong>Release notes:</strong></p><?= text_auto_p($release_notes) ?>
	<hr>

	<form action="<?= EH_BASEURL ?>" method="GET">
		<input type="hidden" name="step" value="install">
		<button><?php if( $new_version_available ) echo 'update system'; else echo 're-install system'; ?></button> (this may take some time, please be patient)
	</form>

	<?php
	exit;

} elseif( $step == 'install' )  {

	$json = get_remote_json( $api_url );
	
	if( ! $json || ! is_array($json) ) {
		?>
		<p><strong>Error:</strong> could not get release information from GitHub</p>
		<?php
		exit;
	}

	$latest_release = $json[0];

	$zipball = $latest_release->zipball_url;

	if( ! $zipball ) {
		?>
		<p><strong>Error:</strong> could not get new .zip file from GitHub</p>
		<?php
		exit;
	}

	echo '<p>Downloading new .zip from GitHub … ';
	flush();

	$temp_zip_file = EH_ABSPATH.'cache/_new_release.zip';
	if( file_exists($temp_zip_file) ) unlink($temp_zip_file);

	$file_handle = fopen( $temp_zip_file, 'w+' );

	$ch = curl_init( $zipball );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'maxhaesslein/eigenheim/'.$eigenheim->get_version() );
	curl_setopt( $ch, CURLOPT_FILE, $file_handle );
	curl_exec( $ch );
	curl_close( $ch );

	fclose($file_handle);

	echo 'done.</p>';

	echo '<p>Extracting .zip file … ';
	flush();

	function deleteDirectory( $dirPath ) {
		if( ! is_dir($dirPath) ) return;

		$objects = scandir($dirPath);
		foreach ($objects as $object) {
			if( $object == "." || $object == "..") continue;

			if( is_dir($dirPath . DIRECTORY_SEPARATOR . $object) ){
				deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
			} else {
				unlink($dirPath . DIRECTORY_SEPARATOR . $object);
			}
		}
		rmdir($dirPath);
	}


	$temp_folder = EH_ABSPATH.'cache/_new_release/';
	if( is_dir($temp_folder) ) deleteDirectory($temp_folder);
	mkdir( $temp_folder );

	$zip = new ZipArchive;
	$res = $zip->open($temp_zip_file);
	if( $res !== TRUE ) {
		echo '<p><strong>Error:</strong> could not extract .zip file</p>';
		exit;
	}
	$zip->extractTo( $temp_folder );
	$zip->close();

	echo 'done.</p>';

	$subfolder = false;
	foreach( scandir( $temp_folder ) as $obj ) {
		if( $obj == '.' || $obj == '..' ) continue;
		if( ! is_dir($temp_folder.$obj) ) continue;
		if( ! str_starts_with($obj, 'maxhaesslein-eigenheim-') ) continue;
		// the zip file should have exactly one subfolder, called 'maxhaesslein-eigenheim-{hash}'. this is what we want to get here
		$subfolder = $temp_folder.$obj.'/';
	}

	if( ! $subfolder ) {
		echo '<p><strong>Error:</strong> something went wrong with the .zip file</p>';
		exit;
	}

	echo '<p>Deleting old files … ';
	flush();

	deleteDirectory( EH_ABSPATH.'theme/default/' );
	deleteDirectory( EH_ABSPATH.'system/' );
	@unlink( EH_ABSPATH.'.htacces' );
	unlink( EH_ABSPATH.'index.php' );
	unlink( EH_ABSPATH.'README.md');
	unlink( EH_ABSPATH.'changelog.txt');

	echo 'done.</p>';

	echo '<p>Moving new files to new location … ';
	flush();

	rename( $subfolder.'theme/default', EH_ABSPATH.'theme/default' );
	rename( $subfolder.'system', EH_ABSPATH.'system' );
	rename( $subfolder.'index.php', EH_ABSPATH.'index.php' );
	rename( $subfolder.'README.md', EH_ABSPATH.'README.md' );
	rename( $subfolder.'changelog.txt', EH_ABSPATH.'changelog.txt' );

	echo 'done.</p>';
	echo '<p>Cleaning up …';
	@unlink( EH_ABSPATH.'update.txt' );
	@unlink( EH_ABSPATH.'update' );

	deleteDirectory( EH_ABSPATH.'cache/');
	mkdir( EH_ABSPATH.'cache/' );

	echo 'done.</p>';
	flush();

	echo '<p>Please <a href="'.EH_BASEURL.'">refresh this page</a></p>';

} else {
	?>

	<p><strong>Warning: please backup your <em>content/</em> folder, your <em>config.php</em> file and maybe your <em>theme/custom-theme</em> folder before updating!</strong></p>
	<p>Also, read the <a href="https://github.com/maxhaesslein/eigenheim/releases/latest/" target="_blank" rel="noopener">latest release notes</a> before continuing.</p>

	<p>Currently installed version: <em><?= $old_version ?></em></p>

	<form action="<?= EH_BASEURL ?>" method="GET">
		<input type="hidden" name="step" value="check">
		<button>check for update</button>
	</form>

	<?php

	exit;
}





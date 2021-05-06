#!/usr/bin/env php
<?php
/**
 * Build the WordPress plugin as a zip
 *
 * @package MyVideoRoomPlugin
 */

/* Get real path for our folder */
$root_path = realpath( 'my-video-room' );
$file_name = 'my-video-room.zip';

require_once __DIR__ . '/vendor/squizlabs/php_codesniffer/autoload.php';

$runner = new \PHP_CodeSniffer\Runner();

$_SERVER['argv'] = array( 'vendor/bin/phpcs' );
$exit_code       = $runner->runPHPCS();

if ( $exit_code ) {
	throw new \Exception( 'PHP Checkstyle failed - cannot build' );
}

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
$index = file_get_contents( $root_path . '/index.php' );

preg_match( '/Version: (.*)/', $index, $matches );

$version = trim( $matches[1] );

/* Initialize archive object */
$zip = new ZipArchive();

if ( ! file_exists( __DIR__ . '/builds/' . $version ) ) {
	mkdir( __DIR__ . '/builds/' . $version, 0777, true );
}

$zip->open( __DIR__ . '/builds/' . $version . '/' . $file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE );

/**
/* Recurse over the root directory and get all the required files
/*
/* @var SplFileInfo[] $files
*/
$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root_path ),
	RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ( $files as $name => $file ) {
	$file_path     = $file->getRealPath();
	$relative_path = substr( $file_path, strlen( $root_path ) + 1 );

	if ( strpos( $relative_path, 'Test/' ) === 0 ) {
		continue;
	}

	if ( strpos( $relative_path, '.DS_Store' ) === 0 ) {
		continue;
	}

	if ( ! $file->isDir() ) {
		$zip->addFile( $file_path, $relative_path );
	}
}

/* Zip archive will be created only after closing object */
$zip->close();

if ( file_exists( __DIR__ . '/' . $file_name ) ) {
	unlink( __DIR__ . '/' . $file_name );
}
symlink( __DIR__ . '/builds/' . $version . '/' . $file_name, __DIR__ . '/' . $file_name );

echo "Done!\n";

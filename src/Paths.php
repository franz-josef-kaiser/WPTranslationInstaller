<?php
/**
 * This file is part of the "" package.
 *
 * © 2016 Franz Josef Kaiser
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\WPTranslation;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

class Paths
{
	/** @string */
	const WP_PACKAGE_TYPE = 'wordpress-core';

	/** @type \Composer\Composer */
	private $composer;

	/** @type \Composer\IO\IOInterface */
	private $io;

	/** @type \Composer\Util\Filesystem */
	private $filesystem;

	/**
	 * Paths constructor.
	 *
	 * @param \Composer\IO\IOInterface  $io
	 * @param \Composer\Composer        $composer
	 * @param \Composer\Util\Filesystem $filesystem
	 */
	public function __construct( IOInterface $io, Composer $composer, Filesystem $filesystem )
	{
		$this->composer   = $composer;
		$this->io         = $io;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param \Composer\Package\PackageInterface $package
	 * @return string Absolute/ real path to the install dir
	 */
	public function build( PackageInterface $package )
	{
		$extra = $this->composer->getPackage()->getExtra();

		$name = $this->getPackageName( $package->getPrettyName() );

		// Check if one the paths is valid…
		$path = '';
		if ( ! empty( $extra['installer-paths'] ) ) {
			$path = key( $extra['installer-paths'] );
		}
		if ( empty( $path ) ) {
			$path = $this->getContentDir( $name );
		}
		if ( empty( $path ) ) {
			$path = $this->getWordPressDir( $name );
		}

		// …we had no chance.
		if ( empty( $path ) ) {
			return '';
		}

		$path = $this->getConcretePath( $path, compact( 'name' ) );

		// Return the final path and make sure it is not corrupted,
		// does not have escaped or trailing slashes or doubled directories
		return $this->filesystem->normalizePath( $path );
	}

	/**
	 * Retrieve and extract the name of a package
	 * @param string $name
	 * @return string
	 */
	public function getPackageName( $name )
	{
		$name = ! strpos( $name, '/', 0 )
			? $name
			: explode( '/', $name )[1];

		return filter_var( $name, FILTER_SANITIZE_STRING );
	}

	/**
	 * Retrieves the WordPress content directory as target
	 * If there is none, uses the same assumption as "WPStarter"
	 * about its name and location. *Fallback*
	 * @param string $name Name of the plugin or theme
	 * @return string Absolute path to the WP install dir
	 */
	public function getContentDir( $name )
	{
		// This assumption aligns this package with "WPStarter"
		$content = ! empty( $extra['wordpress-content-dir'] )
			? $extra['wordpress-content-dir']
			: 'wp-content';

		return sprintf( '%s/%s/languages/%s', getcwd(), $content, $name );
	}

	/**
	 * Retrieves the WordPress installation target
	 * If there is none, uses the same assumption as "WPStarter"
	 * about its name and location. *Fallback*
	 * @param string $name Name of the plugin or theme
	 * @return string Absolute path to the WP content dir
	 */
	public function getWordPressDir( $name )
	{
		// Do not attempt to construct a path for something
		// that does not exist or we do not know its location.
		if ( ! $this->isWordPressRequired() )
			return '';

		// This assumption aligns this package with "WPStarter"
		$wp = ! empty( $extra['wordpress-install-dir'] )
			? $extra['wordpress-install-dir']
			: 'wordpress';

		return sprintf( '%s/%s/wp-content/languages/%s', getcwd(), $wp, $name );
	}

	/**
	 * Checks if WordPress is required by this project or package
	 * @return bool If WordPress is required as package or not
	 */
	private function isWordPressRequired()
	{
		$packages = $this->composer
			->getRepositoryManager()
			->getLocalRepository()
			->getPackages();

		return is_int( array_search( self::WP_PACKAGE_TYPE, $packages ) );
	}

	/**
	 * Replaces template keywords with real values in a given path
	 * Mostly a copy/paste from Composer/Installers/Baseinstaller
	 * @param string $path
	 * @param array  $vars Whitelist of possible replacement values
	 * @return string
	 */
	protected function getConcretePath( $path, Array $vars = [] )
	{
		if ( false !== strpos( $path, '{' ) ) {
			extract( $vars );
			preg_match_all( '@\{\$([\w]*)\}@i', $path, $matches );
			if ( ! empty( $matches[1] ) ) {
				foreach ( $matches[1] as $var ) {
					$path = str_replace( '{$' . $var . '}', $$var, $path );
				}
			}
		}

		return $path;
	}
}
<?php
/**
 * This file is part of the "WCM WPi18nInstaller Plugin" package.
 *
 * © 2016 Franz Josef Kaiser
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCM\WPTranslation;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;

class Installer extends LibraryInstaller
{
	const VALID_TYPE = 'wordpress-language';

	private static $paths;

	private static $notifier;

	public function __construct(
		IOInterface $io,
		Composer $composer,
		$type = self::VALID_TYPE,
		Filesystem $filesystem = null
	) {
		parent::__construct( $io, $composer, $type );

		is_null( self::$paths )
			and self::$paths = new Paths( $io, $composer, $this->filesystem );

		is_null( self::$notifier )
			and self::$notifier = new Notification( $io, $composer, $this->filesystem );
	}

	/**
	 * @param PackageInterface $package
	 * @return string|void
	 */
	public function getInstallPath( PackageInterface $package )
	{
		// Abort: this installer should not be concerned with the current package
		if (
			self::VALID_TYPE !== $package->getType()
			or ! $this->composer->getPackage() instanceof RootPackageInterface
		) {
			return parent::getInstallPath( $package );
		}

		// Fetch and construct the path with fallbacks
		$path = self::$paths->build( $package );

		self::$notifier->notify( $path );

		// Generate path if not exists
		$this->filesystem->ensureDirectoryExists( $path );

		return $path;
	}

	public function supports( $packageType )
	{
		return self::VALID_TYPE === $packageType;
	}

	/**
	 * Uninstalls specific package.
	 * @param InstalledRepositoryInterface $repo    repository in which to check
	 * @param PackageInterface             $package package instance
	 */
	public function uninstall(
		InstalledRepositoryInterface $repo,
		PackageInterface $package
	) {
		// @TODO Uninstall language files alongside plugin or theme
		# $path = self::$paths->build( $package );
		# $this->filesystem->removeDirectory( $path );
	}
}
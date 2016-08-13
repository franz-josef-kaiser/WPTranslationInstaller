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

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class Installer extends LibraryInstaller
{
	/**
	 * @param PackageInterface $package
	 * @return string|void
	 */
	public function getInstallPath( PackageInterface $package )
	{
		return parent::getInstallPath( $package );
	}

	public function supports( $packageType )
	{
		return parent::supports( $packageType );
	}
}
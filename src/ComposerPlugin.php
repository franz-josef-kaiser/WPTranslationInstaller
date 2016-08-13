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
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;

class ComposerPlugin
	implements PluginInterface,
			   EventSubscriberInterface
{
	/**
	 * Sets up the custom installer
	 * @param \Composer\Composer       $composer
	 * @param \Composer\IO\IOInterface $io
	 */
	public function activate( Composer $composer, IOInterface $io )
	{
		$installer = new Installer( $io, $composer );
		$composer->getInstallationManager()->addInstaller( $installer );
	}

	/**
	 * Returns an array of event names this subscriber wants to listen to.
	 * @return array The event names to listen to with callback and priority
	 */
	public static function getSubscribedEvents()
	{
		return [
			'post-install-cmd' => [ 'run', 100 ],
			'post-update-cmd'  => [ 'run', 100 ],
		];
	}

	/**
	 * The actual command
	 */
	public function run() {
		var_dump( func_get_args() );
	}
}
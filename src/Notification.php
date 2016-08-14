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

class Notification implements NotificationInterface
{
	private $io;

	private $composer;

	/**
	 * Notification constructor.
	 * @param \Composer\IO\IOInterface $io
	 * @param \Composer\Composer $composer
	 */
	public function __construct( IOInterface $io, Composer $composer )
	{
		$this->io       = $io;
		$this->composer = $composer;
	}

	/**
	 * @param $path
	 */
	public function notify( $path )
	{
		$type = $this->getType( $path );

		$message = $this->getMessage( $path );
		$message = $this->format( $message, $type );

		$this->io->{$type}( $message );
	}

	/**
	 * Retrieves the method used for notifications
	 * @param string $path
	 * @return string
	 */
	protected function getType( $path )
	{
		return empty( $path ) ? 'writeError' : 'write';
	}

	/**
	 * Builds the message
	 * @param string $path
	 * @return string
	 */
	protected function getMessage( $path )
	{
		if ( empty( $path ) ) {
			return 'No valid language file installation path could be determined.';
		}

		return sprintf(
			'Translation package for "%s" successfully installed into "%s"',
			$this->composer->getPackage()->getPrettyName(),
			$path
		);
	}

	/**
	 * Formats a message
	 * @param string $message
	 * @param string $type
	 * @return string Fully prepared message ready for CLI output
	 */
	protected function format( $message, $type )
	{
		return sprintf(
			'  - <%1$s>'.$message.'</%1$s>',
			$this->getTag( $type )
		);
	}

	/**
	 * Retrieve the tag to wrap a notification
	 * @param string $type
	 * @return string key=value without <>
	 */
	protected function getTag( $type )
	{
		$type = ! strstr( $type, 'Error' )
			? 'fg=green'
			: 'bg=red;fg=white';

		return "{$type};option=bold";
	}
}
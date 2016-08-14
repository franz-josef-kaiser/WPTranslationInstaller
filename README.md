# WordPress Translation

Custom _Composer_ installer for _WordPress_ theme and plugin translation files.

## Installation

The package is hosted on _Packagist_ and receives auto-updates. In the CLI, you 
can install by calling:

	composer require wcm/wp-translation-installer-plugin

## Loading translation files in themes or plugins

The best thing you - as developer - can do, is to use the provided WP API 
functions _without_ a path to a subfolder in your theme. This way, the fallback 
location at `wp-content/languages/{name}` will automatically be used by WP Core.

 * [`load_theme_textdomain()`](https://developer.wordpress.org/reference/functions/load_theme_textdomain/)
 * [`load_plugin_textdomain()`](https://developer.wordpress.org/reference/functions/load_plugin_textdomain/)
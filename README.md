Image Upload
============

A small image upload page with user identification.

Requirements
------------

- PHP 5.3 or higher
- MySQL

Install
-------

1. Create a symlink to `lib/system/index.php` in the root of this project.
2. Create symlinks to `db_migrations/*.php` in db_migrations.
3. Create a `config.php` in the lib folder by copying the `config.default.php` from lib/system.
4. Add the below lines to `config.php`
5. Direct you browser to http://your.site/db_migrations/migrations.php and apply all available migrations.


	$GLOBALS['config']['thumbsDir'] = 'thumbs';
	$GLOBALS['config']['imageDir'] = 'uploads';
	$GLOBALS['config']['maxImageFileSize'] = 3145728;
	$GLOBALS['hooks']['Display']['checkPage'] = array();
	// Shorten the link for displaying images, this also gives backwards compatibility
	$GLOBALS['hooks']['Display']['checkPage'][] = function ($pageName) {
		if ($pageName == 'Index' && $_GET['image']) {
			return 'Image';
		}
	};

## License

Image Upload is licensed under the [GNU LGPLv3](https://www.gnu.org/licenses/lgpl.html)

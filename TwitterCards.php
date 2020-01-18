<?php
/**
 * TwitterCards
 * Extensions
 * @author Harsh Kothari (https://mediawiki.org/wiki/User:Harsh4101991) <harshkothari410@gmail.com>
 * @author Kunal Mehta <legoktm@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'TwitterCards' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['TwitterCards'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for the TwitterCards extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the TwitterCards extension requires MediaWiki 1.29+' );
}

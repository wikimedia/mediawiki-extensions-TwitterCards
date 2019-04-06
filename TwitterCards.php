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
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This is an extension to the MediaWiki package and cannot be run standalone." );
}

$wgExtensionCredits['other'][] = array (
	'path' => __FILE__,
	'name' => 'TwitterCards',
	'author' => array( 'Harsh Kothari', 'Kunal Mehta' ),
	'descriptionmsg' => 'twittercards-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:TwitterCards',
	'version' => '0.2',
);

/**
 * Whether to use OpenGraph tags if a fallback is acceptable
 * @see https://dev.twitter.com/docs/cards/markup-reference
 * @var bool
 */
$wgTwitterCardsPreferOG = true;

/**
 * Set this to your wiki's twitter handle
 * for example: '@wikipedia'
 * @var string
 */
$wgTwitterCardsHandle = '';

$wgExtensionMessagesFiles['TwitterCardsMagic'] = __DIR__ . '/TwitterCards.magic.php';
$wgMessagesDirs['TwitterCards'] = __DIR__ . '/i18n';
$wgAutoloadClasses['TwitterCardsHooks'] = __DIR__ . '/TwitterCards.hooks.php';
$wgHooks['BeforePageDisplay'][] = 'TwitterCardsHooks::onBeforePageDisplay';


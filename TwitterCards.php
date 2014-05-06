<?php
/**
 * TwitterCards
 * Extensions
 * @author Harsh Kothari (http://mediawiki.org/wiki/User:Harsh4101991) <harshkothari410@gmail.com>
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
);

/**
 * Set this to your wiki's twitter handle
 * for example: '@wikipedia'
 * @var string
 */
$wgTwitterCardsHandle = '';

$wgExtensionMessagesFiles['TwitterCardsMagic'] = __DIR__ . '/TwitterCards.magic.php';
$wgMessagesDirs['TwitterCards'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['TwitterCards'] = __DIR__ . '/TwitterCards.i18n.php';

$wgHooks['BeforePageDisplay'][] = 'efTwitterCardsSummary';

/**
 * Adds TwitterCards metadata for Images.
 * This is a callback method for the BeforePageDisplay hook.
 *
 * @param OutputPage &$out
 * @param SkinTemplate &$sk
 * @return bool
 */
function efTwitterCardsSummary( OutputPage &$out, SkinTemplate &$sk ) {
	global $wgTwitterCardsHandle;

	if ( !class_exists( 'ApiQueryExtracts') || !class_exists( 'ApiQueryPageImages' ) ) {
		wfDebugLog( 'TwitterCards', 'TextExtracts or PageImages extension is missing.' );
		return true;
	}

	$title = $out->getTitle();
	if ( $title->inNamespaces( NS_SPECIAL ) ) {
		// Skip
		return true;
	}

	$meta = array(
		'twitter:card' => 'summary',
		'twitter:title' => $title->getFullText(),
	);

	if ( $wgTwitterCardsHandle ) {
		$meta['twitter:site'] = $wgTwitterCardsHandle;
	}

	// @todo does this need caching?
	$api = new ApiMain(
		new FauxRequest( array(
			'action' => 'query',
			'titles' => $title->getFullText(),
			'prop' => 'extracts|pageimages',
			'exchars' => '200', // limited by twitter
			'exsectionformat' => 'plain',
			'explaintext' => '1',
			'exintro' => '1',
			'piprop' => 'thumbnail',
			'pithumbsize' => 120 * 2, // twitter says 120px minimum, let's double it
		) )
	);

	$api->execute();
	$data = $api->getResult()->getData();
	$pageData = $data['query']['pages'][$title->getArticleID()];

	$meta['twitter:description'] = $pageData['extract']['*'];
	if ( isset( $pageData['thumbnail'] ) ) { // not all pages have images
		$meta['twitter:image'] = $pageData['thumbnail']['source'];
	}

	// Add to OutputPage
	foreach ( $meta as $name => $value ) {
		$out->addHeadItem( "meta:name:$name", "	" . Html::element( 'meta', array( 'name' => $name, 'content' => $value ) ) . "\n" );
	}

	return true;
}

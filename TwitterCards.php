<?php
/**
 * TwitterCards
 * Extensions
 * @author Harsh Kothari (http://mediawiki.org/wiki/User:Harsh4101991) <harshkothari410@gmail.com>
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
if ( !defined( 'MEDIAWIKI' ) ) die( "This is an extension to the MediaWiki package and cannot be run standalone." );

$wgExtensionCredits['parserhook'][] = array (
	'path' => __FILE__,
	'name' => 'TwitterCards',
	'author' => 'Harsh Kothari',
	'descriptionmsg' => 'twittercards-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:TwitterCards',
);

$wgExtensionMessagesFiles['TwitterCardsMagic'] = __DIR__ . '/TwitterCards.magic.php';
$wgMessagesDirs['TwitterCards'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['TwitterCards'] = __DIR__ . '/TwitterCards.i18n.php';

$wgHooks['BeforePageDisplay'][] = 'efTwitterCardsHook';

/**
 * Adds TwitterCards metadata for Images.
 * This is a callback method for the BeforePageDisplay hook.
 *
 * @param &$out OutputPage The output page
 * @param &$sk SkinTemplate The skin template
 * @return Boolean always true, to go on with BeforePageDisplay processing
 */
function efTwitterCardsHook( &$out, &$sk ) {
	global $wgLogo, $wgSitename, $wgUploadPath, $wgServer, $wgArticleId;

	$title = $out->getTitle();
	$isMainpage = $title->isMainPage();

	$meta = array();
	$meta["twitter:card"] = "photo"; // current proof of concept is tailored to work with images
	$meta["twitter:site"] = $wgSitename;

	$dbr = wfGetDB( DB_SLAVE );
	$pageId = $out->getWikiPage()->getId();
	$res = $dbr->select(
		'revision',
		'rev_user_text',
		'rev_page = "' . $pageId . '"',
		__METHOD__,
		array( 'ORDER BY' => 'rev_timestamp ASC limit 1' )
	);
	if ( $row = $res->fetchObject() ) {
		$meta["twitter:creator"] = $row->rev_user_text;
	}

	$meta["twitter:title"] = $title->getText();
	$img_name = $title->getText();

	if ( isset( $out->mDescription ) ) {
		// Uses the Description2 extension
		$meta["twitter:description"] = $out->mDescription;
	} else {
		// Gets description for content
		$dbr = wfGetDB( DB_SLAVE );
		$img_name = str_replace(' ', '_', $img_name); //TODO: use Title object instead to get a proper title
		$res = $dbr->select(
			'image',
			'img_description',
			'img_name = "' . $img_name . '"',
			__METHOD__,
			array( 'ORDER BY' => 'img_description ASC limit 1' )
		);
		if ( $row = $res->fetchObject() ) {
			$meta["twitter:description"] = $row->img_description;
		}
	}

	if ( $isMainpage ) {
		$meta["twitter:url"] = wfExpandUrl( $wgLogo );
	} else {
		$meta["twitter:url"] = $title->getFullURL();
	}

	// Gets large thumbnail path
	$img = wfFindFile( $title );
	if ( $img ) {
		$thumb = $img->transform( array( 'width' => 400 ), 0 );
		$meta["twitter:image"] = $wgServer . $thumb->getUrl();
	} else {
		return true;
	}

	$meta["twitter:image:width"] = 600;
	$meta["twitter:image:height"] = 600;

	// HTML output
	foreach ( $meta as $name => $value ) {
		if ( $value ) {
			if ( isset( OutputPage::$metaAttrPrefixes ) && isset( OutputPage::$metaAttrPrefixes['name'] ) ) {
				$out->addMeta( "name:$name", $value );
			} else {
				$out->addHeadItem( "meta:name:$name", "	" . Html::element( 'meta', array( 'name' => $name, 'content' => $value ) ) . "\n" );
			}
		}
	}

	return true;
}

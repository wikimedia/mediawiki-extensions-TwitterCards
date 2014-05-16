<?php

class TwitterCardsHooks {

	/**
	 * Twitter --> OpenGraph fallbacks
	 * Only used if $wgTwitterCardsPreferOG = true;
	 * @var array
	 */
	static $fallbacks = array(
		'twitter:description' => 'og:description',
		'twitter:title' => 'og:title',
		'twitter:image:src' => 'og:image',
		'twitter:image:width' => 'og:image:width',
		'twitter:image:height' => 'og:image:height',
	);


	public static function onBeforePageDisplay( OutputPage $out, SkinTemplate $sk ) {
		$title = $out->getTitle();
		if ( $title->inNamespace( NS_SPECIAL ) ) {
			return true;
		//} elseif ( $title->inNamespace( NS_FILE ) ) {
		//	self::photoCard( $out );
		//	return true;
		} else {
			self::summaryCard( $out );
			return true;
		}
	}

	/**
	 * @param Title $title
	 * @param string $type
	 * @return array
	 */
	protected static function basicInfo( Title $title, $type ) {
		global $wgTwitterCardsHandle;
		$meta = array(
			'twitter:card' => $type,
			'twitter:title' => $title->getFullText(),
		);

		if ( $wgTwitterCardsHandle ) {
			$meta['twitter:site'] = $wgTwitterCardsHandle;
		}

		return $meta;
	}

	protected static function addMetaData( array $meta, OutputPage $out ) {
		global $wgTwitterCardsPreferOG;
		foreach ( $meta as $name => $value ) {
			if ( $wgTwitterCardsPreferOG && isset( self::$fallbacks[$name] ) ) {
				$name = self::$fallbacks[$name];
			}
			$out->addHeadItem( "meta:name:$name", "	" . Html::element( 'meta', array( 'name' => $name, 'content' => $value ) ) . "\n" );
		}

	}

	protected static function summaryCard( OutputPage $out ) {
		if ( !class_exists( 'ApiQueryExtracts') || !class_exists( 'ApiQueryPageImages' ) ) {
			wfDebugLog( 'TwitterCards', 'TextExtracts or PageImages extension is missing for summary card.' );
			return;
		}

		$title = $out->getTitle();
		$meta = self::basicInfo( $title, 'summary' );

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

		self::addMetaData( $meta, $out );

	}
}
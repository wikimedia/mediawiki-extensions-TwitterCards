<?php

class TwitterCardsHooks {

	/**
	 * Twitter --> OpenGraph fallbacks
	 * Only used if $wgTwitterCardsPreferOG = true;
	 * @var string[]
	 */
	private static $fallbacks = [
		'twitter:description' => 'og:description',
		'twitter:title' => 'og:title',
		'twitter:image:src' => 'og:image',
		'twitter:image:width' => 'og:image:width',
		'twitter:image:height' => 'og:image:height',
	];

	/**
	 * @param OutputPage $out
	 * @param Skin $sk
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $sk ) {
		$title = $out->getTitle();
		if ( $title->exists() && $title->hasContentModel( CONTENT_MODEL_WIKITEXT ) ) {
			self::summaryCard( $out );
		}
	}

	/**
	 * @param Title $title
	 * @param string $type
	 * @return array
	 */
	protected static function basicInfo( Title $title, $type ) {
		global $wgTwitterCardsHandle;
		$meta = [
			'twitter:card' => $type,
			'twitter:title' => $title->getFullText(),
		];

		if ( $wgTwitterCardsHandle ) {
			$meta['twitter:site'] = $wgTwitterCardsHandle;
		}

		return $meta;
	}

	/**
	 * @param array $meta
	 * @param OutputPage $out
	 */
	protected static function addMetaData( array $meta, OutputPage $out ) {
		global $wgTwitterCardsPreferOG;
		foreach ( $meta as $name => $value ) {
			if ( $wgTwitterCardsPreferOG && isset( self::$fallbacks[$name] ) ) {
				$name = self::$fallbacks[$name];
			}
			$out->addHeadItem( "meta:name:$name", "	" .
				Html::element( 'meta', [ 'name' => $name, 'content' => $value ] ) . "\n" );
		}
	}

	/**
	 * @param OutputPage $out
	 */
	protected static function summaryCard( OutputPage $out ) {
		$title = $out->getTitle();
		$meta = self::basicInfo( $title, 'summary' );

		$props = 'extracts';
		if ( class_exists( 'ApiQueryPageImages' ) ) {
			$props .= '|pageimages';
		}

		// @todo does this need caching?
		$api = new ApiMain(
			new FauxRequest( [
				'action' => 'query',
				'titles' => $title->getFullText(),
				'prop' => $props,
				'exchars' => '200', // limited by twitter
				'exsectionformat' => 'plain',
				'explaintext' => '1',
				'exintro' => '1',
				'piprop' => 'thumbnail',
				'pithumbsize' => 120 * 2, // twitter says 120px minimum, let's double it
			] )
		);

		$api->execute();
		if ( defined( 'ApiResult::META_CONTENT' ) ) {
			$pageData = $api->getResult()->getResultData(
				[ 'query', 'pages', $title->getArticleID() ]
			);
			$contentKey = isset( $pageData['extract'][ApiResult::META_CONTENT] )
				? $pageData['extract'][ApiResult::META_CONTENT]
				: '*';
		} else {
			$data = $api->getResult()->getData();
			$pageData = $data['query']['pages'][$title->getArticleID()];
			$contentKey = '*';
		}

		$meta['twitter:description'] = $pageData['extract'][$contentKey];
		if ( isset( $pageData['thumbnail'] ) ) { // not all pages have images or extension isn't installed
			$meta['twitter:image'] = $pageData['thumbnail']['source'];
		}

		self::addMetaData( $meta, $out );
	}
}

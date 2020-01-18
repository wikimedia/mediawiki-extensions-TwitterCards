# TwitterCards

* [View on Mediawiki.org](https://www.mediawiki.org/wiki/Extension:TwitterCards)*

A [MediaWiki](https://www.mediawiki.org/wiki/MediaWiki) extension written by
[Harsh Kothari](http://mediawiki.org/wiki/User:Harsh4101991) and
[Kunal Mehta](https://www.mediawiki.org/wiki/User:Legoktm).

It's licensed under the [GNU General Public License 2.0 or later](http://www.gnu.org/copyleft/gpl.html).

## Installation

Download this extension and put it into your `extension` folder.

Activate the extension by appending the following code to your `LocalSettings.php`.
```php
wfLoadExtension( 'TwitterCards' );
```

## Configuration

#### $wgTwitterCardsPreferOG
Whether to use OpenGraph tags if a fallback is acceptable.
[More about OpenGraph](https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/markup)

#### $wgTwitterCardsHandle
Set this to your wiki's twitter handle for example: '*@wikipedia*'

<?php

namespace MediaWiki\Extension\Matomo;

use RequestContext;
use Xml;

class Hooks {

	/** @var string|null Searched term in Special:Search. */
	public static $searchTerm = null;

	/** @var string|null Search profile in Special:Search (search category in Piwik vocabulary). */
	public static $searchProfile = null;

	/** @var int|null Number of results in Special:Search. */
	public static $searchCount = null;

	/**
	 * Initialize the Matomo hook
	 *
	 * @param string $skin
	 * @param string &$text
	 * @return bool
	 */
	public static function MatomoSetup( $skin, &$text = '' ) {
		$text .= self::addMatomo( $skin->getTitle() );
		return true;
	}

	/**
	 * Get parameter with either the new prefix $wgMatomo or the old $wgPiwik.
	 *
	 * @param string $name Parameter name without any prefix.
	 * @return mixed|null Parameter value.
	 */
	public static function getParameter( $name ) {
		$config = \MediaWiki\MediaWikiServices::getInstance()->getMainConfig();
		if ( $config->has( "Piwik$name" ) ) {
			return $config->get( "Piwik$name" );
		} elseif ( $config->has( "Matomo$name" ) ) {
			return $config->get( "Matomo$name" );
		}
		return null;
	}

	/**
	 * Hook to save some data in Special:Search.
	 *
	 * @param string $term Searched term.
	 * @param SearchResultSet|null $titleMatches Results in the titles.
	 * @param SearchResultSet|null $textMatches Results in the fulltext.
	 * @return true
	 */
	public static function onSpecialSearchResults( $term, $titleMatches, $textMatches ) {
		self::$searchTerm = $term;
		self::$searchCount = 0;
		if ( $titleMatches instanceof SearchResultSet ) {
			self::$searchCount += (int)$titleMatches->numRows();
		}
		if ( $textMatches instanceof SearchResultSet ) {
			self::$searchCount += (int)$textMatches->numRows();
		}
		return true;
	}

	/**
	 * Hook to save some data in Special:Search.
	 *
	 * @param SpecialSearch $search Special page.
	 * @param string|null $profile Search profile.
	 * @param SearchEngine $engine Search engine.
	 * @return true
	 */
	public static function onSpecialSearchSetupEngine( $search, $profile, $engine ) {
		self::$searchProfile = $profile;
		return true;
	}

	/**
	 * Add Matomo script
	 * @param string $title
	 * @return string
	 */
	public static function addMatomo( $title ) {
		$user = RequestContext::getMain()->getUser();
		// Is Matomo disabled for bots?
		if ( $user->isAllowed( 'bot' ) && self::getParameter( 'IgnoreBots' ) ) {
			return '<!-- Matomo extension is disabled for bots -->';
		}

		// Ignore Wiki System Operators
		if ( $user->isAllowed( 'protect' ) && self::getParameter( 'IgnoreSysops' ) ) {
			return '<!-- Matomo tracking is disabled for users with \'protect\' rights (i.e., sysops) -->';
		}

		// Ignore Wiki Editors
		if ( $user->isAllowed( 'edit' ) && self::getParameter( 'IgnoreEditors' ) ) {
			return "<!-- Matomo tracking is disabled for users with 'edit' rights -->";
		}

		$idSite = self::getParameter( 'IDSite' );
		$matomoURL = self::getParameter( 'URL' );
		$protocol = self::getParameter( 'Protocol' );
		$customJS = self::getParameter( 'CustomJS' );
		$jsFileURL = self::getParameter( 'JSFileURL' );

		// Missing configuration parameters
		if ( empty( $idSite ) || empty( $matomoURL ) ) {
			return '<!-- You need to set the settings for Matomo -->';
		}

		$finalActionName = self::getParameter( 'ActionName' );
		if ( self::getParameter( 'UsePageTitle' ) ) {
			$finalActionName .= $title->getPrefixedText();
		}

		// Check if disablecookies flag
		if ( self::getParameter( 'DisableCookies' ) ) {
			$disableCookiesStr = PHP_EOL . '  _paq.push(["disableCookies"]);';
		} else { $disableCookiesStr = null;
		}

		// Check if we have custom JS
		if ( !empty( $customJS ) ) {

			// Check if array is given
			// If yes we have multiple lines/variables to declare
			if ( is_array( $customJS ) ) {

				// Make empty string with a new line
				$customJs = PHP_EOL;

				// Store the lines in the $customJs line
				foreach ( $customJS as $customJsLine ) {
					$customJs .= $customJsLine;
				}

			// CustomJs is string
			} else { $customJs = PHP_EOL . $customJS;
			}

		// Contents are empty
		} else { $customJs = null;
		}

	// Track search results
	$trackingType = 'trackPageView';
	$jsTrackingSearch = '';
	$urlTrackingSearch = '';
	if ( self::$searchTerm !== null ) {
		// JavaScript
		$trackingType = 'trackSiteSearch';
		$jsTerm = Xml::encodeJsVar( self::$searchTerm );
		$jsCategory = self::$searchProfile === null ? 'false' : Xml::encodeJsVar( self::$searchProfile );
		$jsResultsCount = self::$searchCount === null ? 'false' : self::$searchCount;
		$jsTrackingSearch = ",$jsTerm,$jsCategory,$jsResultsCount";

		// URL
		$urlTrackingSearch = [ 'search' => self::$searchTerm ];
		if ( self::$searchProfile !== null ) {
			$urlTrackingSearch += [ 'search_cat' => self::$searchProfile ];
		}
		if ( self::$searchCount !== null ) {
			$urlTrackingSearch += [ 'search_count' => self::$searchCount ];
		}
		$urlTrackingSearch = '&' . wfArrayToCgi( $urlTrackingSearch );
	}

		// Track username based on https://matomo.org/docs/user-id/ The user
		// name for anonymous visitors is their IP address which Matomo already
		// records.
		if ( self::getParameter( 'TrackUsernames' ) && $user->isRegistered() ) {
			$username = Xml::encodeJsVar( $user->getName() );
			$customJs .= PHP_EOL . "  _paq.push([\"setUserId\",{$username}]);";
		}

		// Check if server uses https
		if ( $protocol == 'auto' ) {

			if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
			) {
				$protocol = 'https';
			} else {
				$protocol = 'http';
			}

		}

		// Prevent XSS
		$finalActionName = Xml::encodeJsVar( $finalActionName );

		// If $wgMatomoJSFileURL is null the locations are $wgMatomoURL/piwik.php and $wgMatomoURL/piwik.js
		// Else they are $wgMatomoURL/piwik.php and $wgMatomoJSFileURL
		$jsMatomoURL = '';
		$jsMatomoURLCommon = '';
		if ( $jsFileURL === null ) {
			$jsFileURL = 'piwik.js';
			$jsMatomoURLCommon = '+' . Xml::encodeJsVar( $matomoURL . '/' );
		} else {
			$jsMatomoURL = '+' . Xml::encodeJsVar( $matomoURL . '/' );
		}
		$jsMatomoJSFileURL = Xml::encodeJsVar( $jsFileURL );

		// Matomo script
		$script = <<<MATOMO
<!-- Matomo -->
<script type="text/javascript">
  var _paq = _paq || [];{$disableCookiesStr}{$customJs}
  _paq.push(["{$trackingType}"{$jsTrackingSearch}]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u = (("https:" == document.location.protocol) ? "https" : "http") + "://"{$jsMatomoURLCommon};
    _paq.push(["setTrackerUrl", u{$jsMatomoURL}+"piwik.php"]);
    _paq.push(["setSiteId", "{$idSite}"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+{$jsMatomoJSFileURL}; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->

<!-- Matomo Image Tracker -->
<noscript><img src="{$protocol}://{$matomoURL}/piwik.php?idsite={$idSite}&rec=1{$urlTrackingSearch}" style="border:0" alt="" /></noscript>
<!-- End Matomo -->
MATOMO;

		return $script;
	}

}

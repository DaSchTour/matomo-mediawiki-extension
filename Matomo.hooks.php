<?php

class MatomoHooks {

	/** @var string|null Searched term in Special:Search. */
	public static $searchTerm = null;

	/** @var string|null Search profile in Special:Search (search category in Matomo vocabulary). */
	public static $searchProfile = null;

	/** @var int|null Number of results in Special:Search. */
	public static $searchCount = null;

	/** @var array Collection of additional matomo callbacks */
	public static $MatomoCallbacks = [];

	/**
	 * Add an array or a single callback to the list of additional matomo callbacks
	 *
	 * @param      string|array  $customJS  matomo callback(s)
	 */
	public static function addMatomoCallbacks( $callbacks = null ) {

		// recursion: add array of callbacks
		if ( is_array( $callbacks ) ) {
			foreach ( $callbacks as $callback ) {
				self::addMatomoCallbacks( $callback );
			}
			return;
		}

		// add single callback
		self::$MatomoCallbacks[] = '  ' . trim( $callbacks );

		return;
	}

	/**
	 * Get the list of additional matomo callbacks as string
	 *
	 * @return     string
	 */
	public static function getMatomoCallbacks() {

		return implode( PHP_EOL, self::$MatomoCallbacks );
	}

	/**
	 * Initialize the Matomo hook
	 *
	 * @param      string  $skin
	 * @param      string  $text
	 *
	 * @return     bool
	 */
	public static function onSkinAfterBottomScripts($skin, &$text = '')	{

		$text .= self::addMatomo( $skin->getTitle() );

		return true;
	}

	/**
	 * Get parameter with either the new prefix $wgMatomo or the old $wgPiwik.
	 *
	 * @param      string      $name   Parameter name without any prefix.
	 *
	 * @return     mixed|null  Parameter value.
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
	 * @param      string                $term          Searched term.
	 * @param      SearchResultSet|null  $titleMatches  Results in the titles.
	 * @param      SearchResultSet|null  $textMatches   Results in the fulltext.
	 *
	 * @return     true
	 */
	public static function onSpecialSearchResults( $term, $titleMatches, $textMatches ) {

		self::$searchTerm = $term;
		self::$searchCount = 0;

		if ( $titleMatches instanceof SearchResultSet ) {
			self::$searchCount += (int) $titleMatches->numRows();
		}
		if ( $textMatches instanceof SearchResultSet ) {
			self::$searchCount += (int) $textMatches->numRows();
		}

		return true;
	}

	/**
	 * Hook to save some data in Special:Search.
	 *
	 * @param      SpecialSearch  $search   Special page.
	 * @param      string|null    $profile  Search profile.
	 * @param      SearchEngine   $engine   Search engine.
	 *
	 * @return     true
	 */
	public static function onSpecialSearchSetupEngine( $search, $profile, $engine ) {

		self::$searchProfile = $profile;

		return true;
	}

	/**
	 * Insert javascript for matomo opt out
	 *
	 * @param      OutputPage  $out
	 * @param      Skin        $skin
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {

		$out->addScriptFile( '/extensions/Matomo/MatomoOptOut.js' );

		return;
	}

	/**
	 * Register parser tag for matomo opt out
	 *
	 * @param      Parser  $parser
	 */
	public static function onParserFirstCallInit( Parser $parser ) {

		$parser->setHook( 'matomo-optout', [ self::class, 'parserTagMatomoOptOut'] );

		return;
	}

	/**
	 * Parser tag function: <matomo-optout />
	 *
	 * @param      mixed    $in
	 * @param      array    $param
	 * @param      Parser   $parser
	 * @param      PPFrame  $frame
	 *
	 * @return     string   html
	 */
	public static function parserTagMatomoOptOut( $in, array $param, Parser $parser, PPFrame $frame ) {

		$html = <<<OPTOUT
<html>
  <p>
    <input type="checkbox" id="matomo-optout" />
    <label for="matomo-optout"><strong></strong></label>
  </p>
</html>
OPTOUT;

		return $html;
	}

	/**
	 * Add Matomo script
	 *
	 * @param      string  $title
	 *
	 * @return     string
	 */
	public static function addMatomo( $title ) {

		global $wgUser, $wgScriptPath, $wgServer;


		## Disable Matomo for some users

		// Is Matomo disabled for bots?
		if ( $wgUser->isAllowed( 'bot' ) && self::getParameter( 'IgnoreBots' ) ) {
			return '<!-- Matomo extension is disabled for bots -->';
		}

		// Ignore Wiki System Operators
		if ( $wgUser->isAllowed( 'protect' ) && self::getParameter( 'IgnoreSysops' ) ) {
			return '<!-- Matomo tracking is disabled for users with \'protect\' rights (i.e., sysops) -->';
		}

		// Ignore Wiki Editors
		if ( $wgUser->isAllowed( 'edit' ) && self::getParameter( 'IgnoreEditors' ) ) {
			return "<!-- Matomo tracking is disabled for users with 'edit' rights -->";
		}


		## Configure paths and site ID

		// Matomo URL defaults to $wgServer.'/matomo/matomo.php'
		$matomoURL = self::getParameter( 'URL' ) ?: $wgServer . '/matomo/matomo.php';

		// Matomo JS URL defaults to the same place
		$matomoJSFileURL = str_replace( 'matomo.php', 'matomo.js', $matomoURL );

		// fallback for old configurations without full URL
		if ( strpos( $matomoURL, '://' ) === false ) {

			// figure out protocol type
			$protocol = self::getParameter( 'Protocol' );
			if ( $protocol == 'auto' ) {
				if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
					$protocol = 'https';
				} else {
					$protocol = 'http';
				}
			}

			// add protocol and old naming "piwik.php"
			$matomoURL = $protocol . '://' . $matomoURL . '/piwik.php';
			$matomoJSFileURL = str_replace( 'piwik.php', 'piwik.js', $matomoURL );
		}

		// use different JS URL if given
		$matomoJSFileURL = self::getParameter( 'JSFileURL' ) ?: $matomoJSFileURL;

		// encode paths for javascript
		$jsMatomoURL = Xml::encodeJsVar( $matomoURL );
		$jsMatomoJSFileURL = Xml::encodeJsVar( $matomoJSFileURL );

		// Site-ID defaults to 1
		$idSite = (int) self::getParameter( 'IDSite' ) ?: 1;


		## Javascript callbacks

		// Disable cookies for cookie-less tracking
		if ( self::getParameter( 'DisableCookies' ) ) {
			self::addMatomoCallbacks( '_paq.push(["disableCookies"]);' );
		}

		// Track username based on https://matomo.org/docs/user-id/ The user
        // name for anonymous visitors is their IP address which Matomo already
        // records.
        if ( self::getParameter( 'TrackUsernames' ) && $wgUser->isLoggedIn()) {
            $username = Xml::encodeJsVar( $wgUser->getName() );
            self::addMatomoCallbacks( "_paq.push(['setUserId',{$username}]);" );
        }

        // add Custom JS (defaults to empty string)
        $customJS = self::getParameter( 'CustomJS' ) ?: '';
        self::addMatomoCallbacks( $customJS );

        // create complete list of callbacks
		$matomoCallbacks = self::getMatomoCallbacks();

		## Tracking type

		// Track search results
		$trackingType = 'trackPageView';
		$jsTrackingSearch = '';
		$urlTrackingSearch = '';
		if ( !is_null( self::$searchTerm ) ) {

			// JavaScript
			$trackingType = 'trackSiteSearch';
			$jsTerm = Xml::encodeJsVar( self::$searchTerm );
			$jsCategory = is_null( self::$searchProfile ) ? 'false' : Xml::encodeJsVar( self::$searchProfile );
			$jsResultsCount = is_null( self::$searchCount ) ? 'false' : self::$searchCount;
			$jsTrackingSearch = ",$jsTerm,$jsCategory,$jsResultsCount";

			// URL
			$urlTrackingSearch = [ 'search' => self::$searchTerm ];
			if( !is_null( self::$searchProfile ) ) {
				$urlTrackingSearch += [ 'search_cat' => self::$searchProfile ];
			}
			if( !is_null( self::$searchCount ) ) {
				$urlTrackingSearch += [ 'search_count' => self::$searchCount ];
			}
			$urlTrackingSearch = '&' . wfArrayToCgi( $urlTrackingSearch );
		}

		// Matomo script
		$script = <<<MATOMO
<!-- Matomo -->
<script type="text/javascript">
  var _paq = _paq || [];
{$matomoCallbacks}
  _paq.push(["{$trackingType}"{$jsTrackingSearch}]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    _paq.push(["setTrackerUrl", {$jsMatomoURL}]);
    _paq.push(["setSiteId", "{$idSite}"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src={$jsMatomoJSFileURL}; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->

<!-- Matomo Image Tracker -->
<noscript><img src="{$matomoURL}?idsite={$idSite}&rec=1{$urlTrackingSearch}" style="border:0" alt="" /></noscript>
<!-- End Matomo -->
MATOMO;

		return $script;
	}

}

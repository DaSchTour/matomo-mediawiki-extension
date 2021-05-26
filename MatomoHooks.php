<?php

class MatomoHooks {

	/** @var string|null Searched term in Special:Search. */
	private static $searchTerm = null;

	/** @var string|null Search profile in Special:Search (search category in Matomo vocabulary). */
	private static $searchProfile = null;

	/** @var int|null Number of results in Special:Search. */
	private static $searchCount = null;

	/** @var array Collection of additional Matomo callbacks */
	private static $MatomoCallbacks = [];

	/** @var User current MediaWiki user */
	private static $user = null;

	/**
	 * Remember user for use in isMatomoDisabled() and addMatomoScript()
	 *
	 * This method is called from various hooks to get the current user.
	 * Multiple call ensure that a hook is run on each page as some are
	 * only fired on the search page.
	 *
	 * @param      User  $user
	 *
	 * @return     true
	 */
	private static function rememberUser( User $user = null ) {

		if ( $user instanceof User ) {
			self::$user = $user;
		}

		return true;
	}

	/**
	 * Check if Matomo is disabled (to skip all processing)
	 *
	 * @return     bool  true, if Matomo is disabled
	 */
	private static function isMatomoDisabled() {

		// if user could not be determined, disable Matomo (just in case, this should not happen)
		if ( null === self::$user ) {
			return true;
		}

		// Disable Matomo for Wiki Editors (if configured)
		if ( self::$user->isAllowed( 'edit' ) && self::getParameter( 'IgnoreEditors' ) ) {
			return true;
		}

		// Disable Matomo for bots (if configured)
		if ( self::$user->isAllowed( 'bot' ) && self::getParameter( 'IgnoreBots' ) ) {
			return true;
		}

		// Disable Matomo for Wiki System Operators (if configured)
		if ( self::$user->isAllowed( 'protect' ) && self::getParameter( 'IgnoreSysops' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get parameter with either the new prefix $wgMatomo or the old $wgPiwik.
	 *
	 * @param      string      $name   Parameter name without any prefix.
	 *
	 * @return     mixed|null  Parameter value.
	 */
	private static function getParameter( $name ) {

		$config = \MediaWiki\MediaWikiServices::getInstance()->getMainConfig();

		if ( $config->has( "Piwik$name" ) ) {
			return $config->get( "Piwik$name" );
		} elseif ( $config->has( "Matomo$name" ) ) {
			return $config->get( "Matomo$name" );
		}

		return null;
	}

	/**
	 * Add an array or a single callback to the list of additional Matomo callbacks
	 *
	 * @param      mixed  $callbacks  Matomo callback(s) (type: string|array)
	 *
	 * @return     bool   true
	 */
	private static function addMatomoCallbacks( $callbacks = null ) {

		// recursion: add array of callbacks
		if ( is_array( $callbacks ) ) {
			foreach ( $callbacks as $callback ) {
				self::addMatomoCallbacks( $callback );
			}
			return;
		}

		// add single callback
		self::$MatomoCallbacks[] = '  ' . trim( $callbacks );

		return true;
	}

	/**
	 * Get the list of additional Matomo callbacks as string
	 *
	 * @return     string
	 */
	private static function getMatomoCallbacks() {

		return implode( PHP_EOL, self::$MatomoCallbacks );
	}

	##
	## Hint: hooks are sorted here according to the order they are fired
	##

	/**
	 * Hook: Register parser tag for Matomo opt out
	 *
	 * @param      Parser  $parser
	 *
	 * @return     bool    true
	 */
	public static function onParserFirstCallInit( Parser $parser ) {

		$parser->setHook( 'matomo-optout', [ self::class, 'parserTagMatomoOptOut'] );

		return true;
	}

	/**
	 * Hook: Save some additional data in Special:Search.
	 *
	 * @param      SpecialSearch  $search   Special page.
	 * @param      string|null    $profile  Search profile.
	 * @param      SearchEngine   $engine   Search engine.
	 *
	 * @return     bool           true
	 */
	public static function onSpecialSearchSetupEngine( SpecialSearch $search, string $profile, SearchEngine $engine ) {

		self::rememberUser( $search->getUser() );

		// skip if Matomo is disabled
		if ( self::isMatomoDisabled() ) {
			return true;
		}

		self::$searchProfile = $profile;

		return true;
	}

	/**
	 * Hook: Save some additional data in Special:Search.
	 *
	 * @param      string                $term          Searched term.
	 * @param      SearchResultSet|null  $titleMatches  Results in the titles.
	 * @param      SearchResultSet|null  $textMatches   Results in the fulltext.
	 *
	 * @return     bool                  true
	 */
	public static function onSpecialSearchResults( string $term, ISearchResultSet $titleMatches = null, ISearchResultSet $textMatches = null ) {

		// skip if Matomo is disabled
		if ( self::isMatomoDisabled() ) {
			return true;
		}

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
	 * Hook: Insert JavaScript for Matomo opt out
	 *
	 * @param      OutputPage  $out
	 * @param      Skin        $skin
	 *
	 * @return     bool        true
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {

		self::rememberUser( $out->getUser() );

		// skip if Matomo is disabled
		if ( self::isMatomoDisabled() ) {
			return true;
		}

		$out->addScriptFile( '/extensions/Matomo/MatomoOptOut.js' );

		return true;
	}

	/**
	 * Hook: Insert Matomo script before closing <body>
	 *
	 * @param      Skin    $skin
	 * @param      string  $text
	 *
	 * @return     bool    true
	 */
	public static function onSkinAfterBottomScripts( Skin $skin, string &$text = '' ) {

		self::rememberUser( $skin->getUser() );

		// skip if Matomo is disabled
		if ( self::isMatomoDisabled() ) {
			return true;
		}

		$text .= self::addMatomoScript( $skin->getTitle() );

		return true;
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

		// this message is displayed if Matomo is disabled (otherwise MatomoOptOut.js will overwrite this)
		$msg = wfMessage( 'matomo-disabled' );

		$html = <<<OPTOUT
<html>
  <p>
	<input type="checkbox" id="matomo-optout" />
	<label for="matomo-optout"><strong>${msg}</strong></label>
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
	private static function addMatomoScript( $title ) {

		global $wgServer;

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

		// encode paths for JavaScript
		$jsMatomoURL = Xml::encodeJsVar( $matomoURL );
		$jsMatomoJSFileURL = Xml::encodeJsVar( $matomoJSFileURL );

		// Site-ID defaults to 1
		$idSite = (int) self::getParameter( 'IDSite' ) ?: 1;

		## JavaScript callbacks

		// Disable cookies for cookie-less tracking
		if ( self::getParameter( 'DisableCookies' ) ) {
			self::addMatomoCallbacks( '_paq.push(["disableCookies"]);' );
		}

		// Track username based on https://matomo.org/docs/user-id/ The user name
		// for anonymous visitors is their IP address which Matomo already records.
		if ( self::getParameter( 'TrackUsernames' ) && self::$user instanceof User && self::$user->isLoggedIn() ) {
			$username = Xml::encodeJsVar( self::$user->getName() );
			self::addMatomoCallbacks( "_paq.push(['setUserId',{$username}]);" );
		}

		// add Custom JS (defaults to empty string)
		$customJS = self::getParameter( 'CustomJS' ) ?: '';
		self::addMatomoCallbacks( $customJS );

		// create complete list of callbacks
		$matomoCallbacks = self::getMatomoCallbacks();

		## Tracking type

		// tracking type defaults to 'trackPageView'
		$trackingType = 'trackPageView';

		// Track search results
		$jsTrackingSearch = '';
		$urlTrackingSearch = '';
		if ( !is_null( self::$searchTerm ) ) {

			$trackingType = 'trackSiteSearch';

			// JavaScript
			$jsTerm = Xml::encodeJsVar( self::$searchTerm );
			$jsCategory = Xml::encodeJsVar( self::$searchProfile ) ?: 'false';
			$jsResultsCount = Xml::encodeJsVar( self::$searchCount ) ?: 'false';
			$jsTrackingSearch = ",$jsTerm,$jsCategory,$jsResultsCount";

			// URL
			$urlTrackingSearch = [ 'search' => self::$searchTerm ];
			if ( !is_null( self::$searchProfile ) ) {
				$urlTrackingSearch += [ 'search_cat' => self::$searchProfile ];
			}
			if ( !is_null( self::$searchCount ) ) {
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

<?php
class PiwikHooks {
	function PiwikSetup( $skin, &$text = '' ) {
	$text .= PiwikHooks::AddPiwik( $skin->getTitle() );
	return true;
	}

	function AddPiwik( $title ) {
	global $wgPiwikIDSite, $wgPiwikURL, $wgPiwikIgnoreSysops, $wgPiwikIgnoreBots, $wgUser, $wgScriptPath, $wgPiwikCustomJS, $wgPiwikActionName, $wgPiwikUsePageTitle;
	if ( !$wgUser->isAllowed( 'bot' ) || !$wgPiwikIgnoreBots ) {
		if ( !$wgUser->isAllowed( 'protect' ) || !$wgPiwikIgnoreSysops ) {
			if ( !empty( $wgPiwikIDSite ) AND !empty( $wgPiwikURL ) ) {
				if ( $wgPiwikUsePageTitle ) {
					$wgPiwikPageTitle = $title->getPrefixedText();

					$wgPiwikFinalActionName = $wgPiwikActionName;
					$wgPiwikFinalActionName .= $wgPiwikPageTitle;
				} else {
					$wgPiwikFinalActionName = $wgPiwikActionName;
				}
				// Stop xss since page title's can have " and stuff in them.
				$wgPiwikFinalActionName = Xml::encodeJsVar( $wgPiwikFinalActionName );
				$funcOutput = <<<PIWIK
<!-- Piwik -->
<script type="text/javascript">
/* <![CDATA[ */
var pkBaseURL = (("https:" == document.location.protocol) ? "https://{$wgPiwikURL}" : "http://{$wgPiwikURL}");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
/* ]]> */
</script>
<script type="text/javascript">
/* <![CDATA[ */
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", {$wgPiwikIDSite});
piwikTracker.setDocumentTitle({$wgPiwikFinalActionName});
piwikTracker.setIgnoreClasses("image");
{$wgPiwikCustomJS}
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
/* ]]> */
</script><noscript><p><img src="http://{$wgPiwikURL}piwik.php?idsite={$wgPiwikIDSite}" style="border:0" alt=""/></p></noscript>
<!-- /Piwik -->
PIWIK;
			} else {
				$funcOutput = "\n<!-- You need to set the settings for Piwik -->";
			}
		} else {
			$funcOutput = "\n<!-- Piwik tracking is disabled for users with 'protect' rights (i.e., sysops) -->";
		}
	} else {
		$funcOutput = "\n<!-- Piwik tracking is disabled for bots -->";
	}

	return $funcOutput;	
	}
}


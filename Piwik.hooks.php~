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
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://{$wgPiwikURL}/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "{$wgPiwikIDSite}"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->

<!-- Piwik Image Tracker -->
<noscript><img src="http://{$wgPiwikURL}/piwik.php?idsite={$wgPiwikIDSite}&amp;rec=1" style="border:0" alt="" /></noscript>
<!-- End Piwik -->
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


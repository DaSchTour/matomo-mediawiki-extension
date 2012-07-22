<?php
/**
 * Inserts Piwik script into MediaWiki pages for tracking and adds some stats
 *
 * @file
 * @ingroup Extensions
 * @author Isb1009 <isb1009 at gmail dot com>
 * @copyright © 2008-2010 Isb1009
 * @licence GNU General Public Licence 2.0
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Piwik Integration',
	'version'        => '2.0.0',
	'author'         => 'Isb1009, [http://www.dasch-tour.de DaSch]',
	'description'	 => 'Adding Piwik Tracking Code',
	'url'            => 'https://www.mediawiki.org/wiki/Extension:Piwik_Integration',
);

$dir = '/' . dirname(__FILE__);

$wgAutoloadClasses['PiwikHooks'] = $dir . 'Piwik.hooks.php';

$wgHooks['SkinAfterBottomScripts'][]  = 'PiwikHooks::PiwikSetup';

$wgPiwikIDSite = "";
$wgPiwikURL = "";
$wgPiwikIgnoreSysops = true;
$wgPiwikIgnoreBots = true;
$wgPiwikCustomJS = "";
$wgPiwikUsePageTitle = false;
$wgPiwikActionName = "";
$wgPiwikSpecialPageDate = 'yesterday';
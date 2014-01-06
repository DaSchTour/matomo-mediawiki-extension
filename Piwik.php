<?php
/**
 * Inserts Piwik script into MediaWiki pages for tracking and adds some stats
 *
 * @file
 * @ingroup Extensions
 * @author Isb1009 <isb1009 at gmail dot com>
 * @author DaSch <dasch@daschmedia.de>
 * @author Youri van den Bogert <yvdbogert@archixl.nl>
 * @copyright © 2008-2010 Isb1009
 * @licence GNU General Public Licence 2.0
 * @package Extensions
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 'This file is a MediaWiki extension, it is not a valid entry point' );

$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'Piwik Integration',
	'version'        => '2.3.0',
	'author'         => array('Isb1009', '[http://www.daschmedia.de DaSch]', '[https://github.com/YOUR1 Youri van den Bogert]'),
	'description'	 => 'Adding Piwik Tracking Code',
	'url'            => 'https://www.mediawiki.org/wiki/Extension:Piwik_Integration',
);

$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['PiwikHooks'] = $dir . 'Piwik.hooks.php';

$wgHooks['SkinAfterBottomScripts'][]  = 'PiwikHooks::PiwikSetup';

$wgPiwikIDSite = "";
$wgPiwikURL = "";
$wgPiwikIgnoreSysops = true;
$wgPiwikIgnoreBots = true;
$wgPiwikCustomJS = "";
$wgPiwikUsePageTitle = false;
$wgPiwikActionName = "";
$wgPiwikDisableCookies = false;
// Allowed values: auto, http or https
// If the value is set to auto the server will use the currently set protocol.
// Note; if you set this to https, make sure your PIWIK server has a valid certificate!
$wgPiwikProtocol = 'auto'; // Allowed values: auto, http, or https.


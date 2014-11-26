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

$GLOBALS['wgExtensionCredits']['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'Piwik Integration',
	'version'        => '2.4.2',
	'author'         => array('Isb1009', '[http://www.daschmedia.de DaSch]', '[https://github.com/YOUR1 Youri van den Bogert]'),
	'description'    => 'Adding Piwik Tracking Code',
	'url'            => 'https://www.mediawiki.org/wiki/Extension:Piwik_Integration',
);

$dir = dirname(__FILE__) . '/';

$GLOBALS['wgAutoloadClasses']['PiwikHooks'] = $dir . 'Piwik.hooks.php';

$GLOBALS['wgHooks']['SkinAfterBottomScripts'][]  = 'PiwikHooks::PiwikSetup';

$GLOBALS['wgPiwikIDSite'] = "";
$GLOBALS['wgPiwikURL'] = "";
$GLOBALS['wgPiwikIgnoreSysops'] = true;
$GLOBALS['wgPiwikIgnoreBots'] = true;
$GLOBALS['wgPiwikCustomJS'] = "";
$GLOBALS['wgPiwikUsePageTitle'] = false;
$GLOBALS['wgPiwikActionName'] = "";
$GLOBALS['wgPiwikDisableCookies'] = false;
$GLOBALS['wgPiwikProtocol'] = 'auto';
$GLOBALS['wgPiwikUsernameCustomVariable'] = array();


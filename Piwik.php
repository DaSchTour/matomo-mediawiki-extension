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

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Piwik' );
	/* wfWarn(
		'Deprecated PHP entry point used for Piwik extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return true;
} else {
	die( 'This version of the Piwik extension requires MediaWiki 1.25+' );
}

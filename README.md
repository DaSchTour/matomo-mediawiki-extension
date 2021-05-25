Matomo extension for MediaWiki
==============================

* Version 4.2.4
* Last update: 13th April 2021

This extension is a modified version of the regular [Matomo extension](https://www.mediawiki.org/wiki/Extension:Matomo).

It has been developed based on version 4.0.1 from the original source code:
https://github.com/DaSchTour/matomo-mediawiki-extension/


Minimum requirements
--------------------------------

This extension integrates Matomo (formerly known as Piwik) into your MediaWiki application.

It does not contain Matomo, so you have to install and setup it separately first.

The extension requires at least the following versions to fully work:

* MediaWiki 1.25+
* Matomo 2.7+

Installation instructions
---------------------------------

The installation is fairly easy, just follow these steps:

1. If you haven't set up a site for your MediaWiki yet, follow this guide to add a new site to Matomo: https://matomo.org/docs/manage-websites/

    Be sure to note URL and site ID since you will need it in a minute.

2. Copy the whole "Matomo" folder into your extensions directory

    Note: You can also use `composer.json` to add the extension to your site.

3. Edit your `LocalSettings.php` and add the following at the end of the file:

    ```php
    wfLoadExtension( 'Matomo' );
    $wgMatomoURL = "https://your-matomo-server.tld/matomo/matomo.php";
    $wgMatomoIDSite = 1;
    ```

    Fill in your URL and site ID your got from step 1.

    Note: Until version 4.2.0 `$wgMatomoURL` had to be defined without protocol and filename (e.g. `"matomo-host.tld/dir/"`). This configuration will still work but is deprecated.

3. Check if the Matomo Extension is loaded in MediaWiki. It should be listed on the page "Special:Version".


Matomo opt out
------------------------

This extension offers a simple way to include an opt out mechanism into your pages.

You only need to add this parser tag to your page (e.g. data protection):

  ```html
  <matomo-optout />
  ```

User can then toggle their consent status by clicking on the corresponding text.

This can replace the none-responsive iframe opt-out and is inspired by this article:
* https://developer.matomo.org/guides/tracking-javascript-guide#optional-creating-a-custom-opt-out-form


Custom variables
------------------------

* Disable cookies permanent cookies (default: `false`)

    ```php
    $wgMatomoDisableCookies = true;
    ```

* Do not track regular editors (default: `false`)

    ```php
    $wgMatomoIgnoreEditors = true;
    ```

* Do not track bots (default: `true`)

    ```php
    $wgMatomoIgnoreEditors = true;
    ```

* Do not track sysop users (default: `true`)

    ```php
    $wgMatomoIgnoreSysops = true;
    ```

* Track users by their MediaWiki username:  (default: `false`)

    ```php
    $wgMatomoTrackUsernames = true;
    ```

* You may also add custom javascript callbacks to the inserted Matomo code:

    ```php
    $wgMatomoCustomJS = "_paq.push(['trackGoal', '1']);"
    ```

  If you have multiple variables to define, use an array:

    ```php
    $wgMatomoCustomJS = array (
      "_paq.push(['setCustomVariable', '1', 'environment', 'production']);",
      "_paq.push(['setCustomVariable', '1', 'is_user', 'yes']);"
    );
    ```

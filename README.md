Mamoto extension for MediaWiki
==============================
Version 4.0.1
 - Last update: 28 February 2019

This the Mamoto (ex-Piwik) integration extension for MediaWiki
software. The extension is only useful if you've got a MediaWiki
installation; it can only be installed by the administrator of the site.

Minimum requirements
--------------------------------

1.  MediaWiki 1.25+

2.  A Matomo (0.4+) installation with the site configured

Installation instructions
---------------------------------
Please, read them carefully. They're not very difficult to understand,
but **ALL** steps are necessary:

1. Create a folder called "Matomo" in your extensions directory

2. Upload extension.json and Matomo.hooks.php in the "Matomo" folder you've just created

3. Edit your LocalSettings.php and, at the end of the file, add the
  following:

        wfLoadExtension( 'Matomo' );


4. Configure the Matomo URL and site-id. To do so; edit the LocalSettings and set up the following variables:
      > $wgMatomoURL = "matomo-host.tld/dir/";

      > $wgMatomoIDSite = "matomo_idsite";

      **IMPORTANT** Do not define the protocol of the $wgMatomoURL

  Note: Change the value of $wgMatomoURL with the URL, without the protocol
	but including the domain name, where you installed Matomo.
	Remember to add the trailing slash!

5. Enjoy our extension!
> Note: to check if the extension has succesfully installed; go to your wiki and check if the Matomo extension is present on the bottom of the Wiki source code.


Custom variables
------------------------
* Disable cookies by setting  the ```$wgMatomoDisableCookies``` variable to ```false```.
  > For example: $wgMatomoDisableCookies = false;

* Ignore regular editors: set ```$wgMatomoIgnoreEditors``` to  ```true```
* Ignore Bots: set ```$wgMatomoIgnoreBots``` to ```true```
* Ignore sysop users: set ```$wgMatomoIgnoreSysops``` to ```true```

* To define custom javascript tags in the Matomo javascript code, its possible to define the $wgMatomoCustomJS variable. For example if you have a single setting to insert; use the following code:
   > ```$wgMatomoCustomJS = "_paq.push(['trackGoal', '1']);"```

   If you have multiple variables to define; use an array. For example:
   > `` $wgMatomoCustomJS = array(
"_paq.push(['setCustomVariable', '1','environment','production']);",
"_paq.push(['setCustomVariable', '1','is_user','yes']);"
);``

* If you want to change the title of your pages inside the Matomo tracker,
  you can set ```$wgMatomoActionName``` inside your LocalSettings.php file.

* In case you want to include the title as, for example,
   "wiki/Title of the page", you can set ```$wgMatomoUsePageTitle``` to
  ```true``` and set ```$wgMatomoActionName``` to ```wiki/```. The extension will print matomo_action_name = 'wiki/Title of the page';

* If you want to track the username of the visitor with the Matomo feature User ID (needs Matomo >= 2.7.0) 
  set the ```$wgMatomoTrackUsernames``` to true in LocalSettings.php.

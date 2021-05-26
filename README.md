Mamoto extension for MediaWiki
==============================
Version 4.2.0
 - Last update: 24 January 2021

This the Matomo (ex-Piwik) integration extension for MediaWiki software.
The extension is only useful if you've got a MediaWiki installation;
it can only be installed by the administrator of the site.

Minimum requirements
--------------------------------

1.  MediaWiki 1.25+

2.  A Matomo (0.4+) installation with the site configured

Installation instructions
---------------------------------
Please, read them carefully. They're not very difficult to understand, but **ALL** steps are necessary:

1. Create a folder called "Matomo" in your extensions directory

2. Upload extension.json and Matomo.hooks.php in the "Matomo" folder you've just created

3. Edit your LocalSettings.php and, at the end of the file, add the following:

  ```wfLoadExtension( 'Matomo' );```

4. Configure the Matomo URL and site-id. To do so; edit the LocalSettings and set up the following variables:

  ```$wgMatomoURL = "https://your-matomo-server.tld/matomo/matomo.php";```
  
  ```$wgMatomoIDSite = 1;```

  Note: Until version 4.2.0 $wgMatomoURL had to be defined without protocol and filename (e.g. "matomo-host.tld/dir/"). This configuration will still work but is deprecated.

5. Enjoy our extension!

  Note: to check if the extension has successfully installed; go to your wiki and check if the Matomo extension is present on the bottom of the Wiki source code.


Custom variables
------------------------
* Disable cookies by setting  the ```$wgMatomoDisableCookies``` variable to ```false```.
  > For example: $wgMatomoDisableCookies = false;

* Ignore regular editors: set ```$wgMatomoIgnoreEditors``` to  ```true``` (default: ```false```)
* Ignore Bots: set ```$wgMatomoIgnoreBots``` to ```true``` (default: ```true```)
* Ignore sysop users: set ```$wgMatomoIgnoreSysops``` to ```true``` (default: ```true```)

* To define custom javascript tags in the Matomo javascript code, its possible to define the $wgMatomoCustomJS variable. For example if you have a single setting to insert; use the following code:

  ```$wgMatomoCustomJS = "_paq.push(['trackGoal', '1']);"```

  If you have multiple variables to define; use an array. For example:

  `` $wgMatomoCustomJS = array(
  "_paq.push(['setCustomVariable', '1','environment','production']);",
  "_paq.push(['setCustomVariable', '1','is_user','yes']);"
  );``

* If you want to track the username of the visitor with the Matomo feature User ID (needs Matomo >= 2.7.0) 
  set the ```$wgMatomoTrackUsernames``` to true in LocalSettings.php.
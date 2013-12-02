## MediaWiki Piwik Integration extension

Version 2.2.1
 - Last update: 8 may 2013

This is the README file for the Piwik Integration extension for MediaWiki
software. The extension is only useful if you've got a MediaWiki
installation; it can only be installed by the administrator of the site.

### Minimum requirements

1.  MediaWiki 1.14+

2.  A Piwik (0.4+) installation with the site configured

### Installation instructions

Please, read them carefully. They're not very difficult to understand,
but **ALL** steps are necessary:

* Create a folder called `piwik` in your extensions directory

* Upload Piwik.php, Piwik.hooks.php, to the "piwik" folder you've just created

* Edit your LocalSettings.php and, at the end of the file, add the
  following:

```php
require_once('/extensions/piwik/Piwik.php');
```

* Configure the Piwik URL and site-id. To do so; edit the LocalSettings and set up the following variables: 

```php
$wgPiwikURL = "piwik-host.tld/dir/";

$wgPiwikIDSite = "piwik_idsite";
```

> **IMPORTANT** Do not define the protocol of the $wgPiwikURL

> Note: Change the value of $wgPiwikURL with the URL, without the protocol
	but including the domain name, where you installed Piwik.
	Remember to add the trailing slash!

* Enjoy our extension!

> Note: to check if the extension has succesfully installed; go to your wiki and check if the Piwik Extension is present on the bottom of the Wiki source code.


### Custom variables

* Disable cookies by setting  the `$wgPiwikDisableCookies` variable to `false`.

```php
# For example:
$wgPiwikDsiableCookies = false;
```

* To define custom javascript tags in the Piwik javascript code, its possible to define the $wgPiwikCustomJS variable. For example if you have a single setting to insert; use the following code:  

```php
$wgPiwikCustomJS = "_paq.push(['trackGoal', '1']);"; 
```

   If you have multiple variables to define; use an array. For example:

```php
$wgPiwikCustomJS = array( 
    "_paq.push(['setCustomVariable', '1','environment','production']);",
    "_paq.push(['setCustomVariable', '1','is_user','yes']);"
);
```
 
* If you want to change the title of your pages inside the Piwik tracker,
  you can set `$wgPiwikActionName` inside your LocalSettings.php file.

* In case you want to include the title as, for example,
   "wiki/Title of the page", you can set `$wgPiwikUsePageTitle` to
  `true` and set `$wgPiwikActionName` to `wiki/`. The extension will print `piwik_action_name = 'wiki/Title of the page';`.
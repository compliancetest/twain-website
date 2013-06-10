=== WP PIE ===
Contributors: ThemeReactor, DanielPClarke
Donate link: http://themereactor.com/
Tags: progressive, internet, explorer, 
Requires at least: 3.3
Tested up to: 3.3.2
Stable tag: trunk

Allows rounded borders, drop shadows and other CSS3 styles to work in Internet Explorer 6-9

== Description ==

Enables support of certain CSS3 styles in IE by automatically searching the primary stylesheet and applying the PIE (http://css3pie.com/) behaviour to the appropriate styles. This results in the enqueuing of a seperate stylesheet.

WP PIE is a very quick, very simple implementation for Wordpress of PIE. It automatically searches the primary stylsheet of the active theme for any rule set containing properties to which PIE can apply. Currently it applies to 'border-radius,' 'box-shadow,' and '-pie-background.' It retrieves these styles and generates and enqueues it's own stylesheet to apply the PIE behaviour. All you need to do to make this work is view the settings page. When that page loads it will automatically apply the fix and you're styles will work in IE.At this time, it will search and apply the style to items with -pie-background but will not generate the style itself. you will need to manually enter these styles. The plugin will however apply the behaviour on it's own afterwards.If for any reason you need to regenerate this list, an update in style perhaps, all you need to do is reload the settings page and it will discard it's old styles in favour of new ones.

== Installation ==

1. Upload `wp-pie` folder and it's contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. View the plugin page to activate.

== Frequently Asked Questions ==

= What do I need to do to make it work? =

Almost nothing! All you need to do is view the WP PIE page in the Wordpress admin panel and the plugin does the rest.

= How do I apply this to additonal stylesheets? =

In simple terms, you can't. Having said that, if you're reasonably comfortable with PHP you can edit the $stylesheets array in wp-pie.php to include the stylesheets you need and it should function in the same manner.

== Screenshots ==


== Changelog ==

= 1.0 =
*first release
= 1.1 = 
*Updated file list
= 1.2 =
*Updated to PIE 1.0.0
*Updated functions for expanded usability.
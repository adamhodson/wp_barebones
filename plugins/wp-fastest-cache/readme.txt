=== WP Fastest Cache ===
Contributors: emrevona
Donate link: http://profiles.wordpress.org/emrevona/
Tags: cache, caching, performance, wp-cache, total cache, super cache, cdn
Requires at least: 3.3
Tested up to: 5.0
Stable tag: 0.8.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The simplest and fastest WP Cache system

== Description ==

<h4>Official Website</h4>

You can find more information on our web site (<a href="http://www.wpfastestcache.com/">wpfastestcache.com</a>)

This plugin creates static html files from your dynamic WordPress blog.
When a page is rendered, php and mysql are used. Therefore, system needs RAM and CPU. 
If many visitors come to a site, system uses lots of RAM and CPU so page is rendered so slowly. 
In this case, you need a cache system not to render page again and again.
Cache system generates a static html file and saves. Other users reach to static html page.
<br><br>
Setup of this plugin is so easy. You don't need to modify the .htacces file. It will be modified automatically.

<h4>Multisite Support</h4>
Wpfc does not support Wordpress Multisite yet.

<h4>Features</h4>

1. Mod_Rewrite which is the fastest method is used in this plugin
2. All cache files are deleted when a post or page is published
3. Admin can delete all cached files from the options page
4. Admin can delete minified css and js files from the options page
5. Block cache for specific page or post with Short Code
6. Cache Timeout - All cached files are deleted at the determinated time
7. Cache Timeout for specific pages
8. Enable/Disable cache option for mobile devices
9. Enable/Disable cache option for logged-in users
10. SSL support
11. CDN support
12. Cloudflare support
13. Preload Cache - Create the cache of all the site automatically
14. Exclude pages and user-agents

<h4>Performance Optimization</h4>

1. Generating static html files from your dynamic WordPress blog
2. Minify Html - You can decrease the size of page
3. Minify Css - You can decrease the size of css files
4. Enable Gzip Compression - Reduce the size of files sent from your server to increase the speed to which they are transferred to the browser.
5. Leverage browser caching - Reduce page load times for repeat visitors
6. Combine CSS - Reduce number of HTTP round-trips by combining multiple CSS resources into one
7. Combine JS
8. Disable Emoji - You can remove the emoji inline css and wp-emoji-release.min.js

<h4>Supported languages: </h4>

* 中文 (by suifengtec)
* Deutsch
* English
* Español (by Diplo)
* Français (by PascalJ)
* Italiana (by Valerio)
* 日本語 (by KUCKLU)
* Nederlands (by Frans Pronk https://ifra.nl)
* Polski (by roan24.pl)
* Português
* Română
* Русский (by Maxim)
* Suomi (by Arhi Paivarinta)
* Svenska (by Linus Wileryd)
* Türkçe

== Installation ==

1. Upload `wp-fastest-cache` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Permission of .htacces must 644
4. Enable this plugin on option page

== Screenshots ==

1. Performance Comparison
2. Other Performance Comparison
3. Without Cache
4. With Cache
5. Main Settings Page
6. Preload
7. New Post
8. Update Cache
9. Delete Cache
10. All cached files are deleted at the determinated time
11. Block caching for post and pages (TinyMCE)
12. Clean cached files via admin toolbar easily
13. Exclude Page
14. CDN
15. Enter CDN Information
16. File Types
17. Specify Sources
18. Database Cleanup

== Changelog ==

= 0.8.9.0 =
* to replace lazy load blank.gif with cdn-url
* to exclude wishlist url of YITH WooCommerce Wishlist
* refactoring of is_amp()
* to add webm extension for cdn
* refactoring of current_url()

= 0.8.8.9 =
* to fix url with replacing cdn-url on data-product_variations attribute
* to increase browser cache time from 3 months to 4 months
* to fix bug on language dropdown [<a target="_blank" href="https://wordpress.org/support/topic/bug-with-language-setting/">Details</a>]
* to increase the value of Cloudflare Browser Cache Expiration to 6 months
* to exclude Twitterbot user-agent
* to fix PHP Warning: file_exists(): open_basedir restriction in effect

= 0.8.8.8 =
* to move "cache timeout" to under "delete cache" tab [<a target="_blank" href="https://www.wpfastestcache.com/features/cache-timeout-page/">Details</a>]
* to prevent clearing cache after Ninja Form is submitted
* <strong>[FEATURE]</strong> Preload for custom post types
* to fix PHP Fatal error: Call to undefined function add_settings_error()

= 0.8.8.7 =
* to fix error which is Undefined index: SERVER_PORT
* to prevent running preload when cache is disabled
* to change saving options and notice system
* to replace image urls of woocommerce variable with cdn-url
* to fix url() problem for data:font/opentype
* to add add_action('wp') for detection current page type
* to support non-latin characters for clearing category cache
* to show cache if the url contains “fbclid” (Facebook Click Identifier)
* to show cache if the url contains “gclid” (Google Click Identifier)

= 0.8.8.6 =
* to show single clear cache button for the pages
* to show warning if wp cron is disabled when a cache timeout rule is added
* to disabe lazy load for the amp pages
* to replace urls on data-cvpsrc and data-cvpset attribute with cdn-url
* to clear the cache of a post which includes /%postname%/%post_id% permalink after the post is deleted
* to fix vulnerability
* to add "event" and "artist" custom post types for preload

= 0.8.8.5 =
* to fix pre tag problem after minify html
* to add woff2 extensions for cdn
* to support non-latin characters for exclude
* to support non-latin characters for toolbar clear current page cache
* to fix "removing dollar sign with number" on fixing pre tag
* to clear cache of cloudflare afer restart preload
* to exclude LinkedInBot user-agent
* to replace urls on data-large_image attribute with cdn-url

= 0.8.8.4 =
* to show language option via php instead of javascript
* to show warning if there is no zone on Cloudflare
* to fix Uncaught SyntaxError on cdn.js [<a target="_blank" href="https://wordpress.org/support/topic/uncaught-syntaxerror-cdn-js/">Details</a>]
* refactoring of cdn_replace_urls()
* <strong>[FEATURE]</strong> Clear Cache via URL [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-via-url/">Details</a>]
* to add webm, ogg and mp4 extensions for browser cache

= 0.8.8.3 =
* to fix Revolution Slider Error
* to make Cloudflare CDN integration work with sub-domains
* to fix error on js combine feature
* to fix error replace cdn-url
* to replace urls on data-thumb attribute with cdn-url
* to move the rules of Gtranslate at the top of WP Fastest Cache on .htaccess
* to define preload number manually [<a target="_blank" href="https://www.wpfastestcache.com/features/preload-settings/#preload-number">Details</a>]

= 0.8.8.2 =
* to fix removing the escaped slashes of Cloudflare Integration [<a target="_blank" href="https://wordpress.org/support/topic/wpfc-cf-is-stripping-important-code/">Details</a>]
* <strong>[FEATURE]</strong> Compatible with Fast Velocity Minify
* <strong>[FEATURE]</strong> Microsoft IIS support
* to serve the sources of Rev Slider if the cdn integration is enabled
* to exclude woocommerce_items_in_cart cookie automatically 
* to check wptouch-pro-view cookie 

= 0.8.8.1 =
* to update russian translation
* to set browser caching for Cloudflare
* not to load the css and js sources of clear cache button when toolar is not visible
* to fix SyntaxError: Unexpected token M in JSON at position 0 error

= 0.8.8.0 =
* to rename the text on admin bar
* to move cdn functions to speed up wp fastest cache
* check if settings are indexed by multilang locales for WeePie Cookie Allow
* <strong>[FEATURE]</strong> to add Cloudflare integration

= 0.8.7.9 =
* refactoring of delete_cache_of_term()
* <strong>[FEATURE]</strong> exclude archives
* to delete cache of pagination after new post
* to add do_action() for deleteCache()
* to shorten the url of the minified and combined sources
* to fix excluding googleanalytics problem
* to create cache for mobile user-agents when mobile option is disabled
* <strong>[FEATURE]</strong> Compatible with WeePie Cookie Allow

= 0.8.7.8 =
* to check <title> for 404 if the header return 200
* to clear all the cache if the visibility is converted to private
* refactoring of ignored()
* to stop loading the admin functions if DOING_AJAX is set
* to fix excluding attachment pages problem
* <strong>[FEATURE]</strong> Compatible with Yet Another Stars Rating
* to disable including powerfulhtml class for ajax request
* to convert the uninstall method to uninstall.php
* <strong>[FEATURE]</strong> exclude Google Analytics Parameters [<a target="_blank" href="http://www.wpfastestcache.com/features/cache-url-with-google-analytics-parameters-querystring/#disable-cache-google-analytics-parameters">Details</a>]
* to add WPFC_DISABLE_WEBP [<a target="_blank" href="http://www.wpfastestcache.com/premium/image-optimization/#disable-webp">Details</a>]
* to fix 403 cdn template error
* to fix height problem of lightbox

= 0.8.7.7 =
* to remove "via php" text if WPFC_REMOVE_VIA_FOOTER_COMMENT is defined as true
* <strong>[FEATURE]</strong> Restart Preload [<a target="_blank" href="http://www.wpfastestcache.com/features/restart-preload-after-completed/">Details</a>]
* to fix the problem of selecting chinese language automatically
* to fix php notice trying to get property of non-object in delete_cache_of_term()
* to speed up getting db optimization statistics()
* to show the cache of main content without query string if google analytics parameters are set [<a target="_blank" href="http://www.wpfastestcache.com/features/cache-url-with-google-analytics-parameters-querystring/">Details</a>]


= 0.8.7.6 =
* to fix the problem which show the same cache for every language on Polylang
* <strong>[FEATURE]</strong> to remove wordpress emojis [<a target="_blank" href="http://www.wpfastestcache.com/optimization/disableremove-wordpress-emojis/">Details</a>]

= 0.8.7.5 =
* <strong>[FEATURE]</strong> Compatible with Easy HTTPS (SSL) Redirection
* to clear of the post when WPML is active

= 0.8.7.4 =
* to exclude the renamed my-account page of woocommerce
* to add AddType x-font/ttf for gzip
* to fix the errors of Undefined property: stdClass::$post, stdClass::$page, stdClass::$category
* <strong>[FEATURE]</strong> exclude attachments

= 0.8.7.3 =
* to espace empty spaces for webp rules
* to clear cache after new Woocommerce orders
* <strong>[FEATURE]</strong> Compatible with kk Star Ratings
* to clear the pages cache of the categories and tags
* <strong>[FEATURE]</strong> Compatible with All In One Schema.org Rich Snippets
* <strong>[FEATURE]</strong> Compatible with WPML Multilingual Plugin
* <strong>[FEATURE]</strong> Compatible with Cloudinary

= 0.8.7.2 =
* to exclude the admins cookies for the cache automatically
* to clear the cache of category and tag after update
* refactoring of insertWebp()
* to exclude /cart automatically for eCommerce Shopping Cart by WP EasyCart
* to exclude /cart and /checkout automatically for Easy Digital Downloads
* to exclude /sitemap_index.xml automatically for Yoast SEO
* to decode path if it is not utf-8
* to fix problem on activation and deactivation
* to clear widget cache after publishing a new post

= 0.8.7.1 =
* to fix image error on Structured Data Testing Tool when cdn is used
* to optimize the panel of wp fastest cache
* to fix cache path for gtranslate
* to fix Cannot modify header error
* to add WPFC_HIDE_CLEAR_CACHE_BUTTON

= 0.8.7.0 =
* to avoid removing www prefix from photon cdn-url
* to prevent to empty origin-url on cdn wizard
* <strong>[FEATURE]</strong> to clear cache of post via post list
* preload for woocommorce product category
* to clear cache of tag and cat after product update
* to check that SG Optimizer is active or not
* <strong>[FEATURE]</strong> Preload for tags
* <strong>[FEATURE]</strong> Preload for attachments
* <strong>[FEATURE]</strong> exclude categories, tags, posts and pages

= 0.8.6.9 =
* <strong>[FEATURE]</strong> to clear cache of the post tags and the post categories after new post
* <strong>[FEATURE]</strong> WebP [<a target="_blank" href="http://www.wpfastestcache.com/premium/image-optimization/">Details</a>]
* to fix BlogPosting error on Structured Data Testing Tool when cdn is used
* to fix more than one cdn work concurrently
* <strong>[FEATURE]</strong> Preload for category
* <strong>[FEATURE]</strong> Preload for woocommerce products

= 0.8.6.8 =
* <strong>[FEATURE]</strong> Widget Cache [<a target="_blank" href="http://www.wpfastestcache.com/premium/widget-cache-reduce-the-number-of-sql-queries/">Details</a>]
* to add browser cache for woff2
* to fix Woocommerce basket issue
* to serve the sources via cdn for logged-in users
* to prevent removing "=" from exclude rules
* to change <FilesMatch "\.(html|htm)"> to <FilesMatch "index\.(html|htm)">
* to fix problem about random url of photon
* to replace origin-url which starts with /wp-content with cdn-url
* to replace wp_get_recent_posts() with get_results() for preload
* to replace the attribute which are data-srcsmall|data-srclarge|data-srcfull with cdn-url
* <strong>[FEATURE]</strong> Compatible with WP Hide & Security Enhancer

= 0.8.6.7 =
* to escape spaces in path for htaccess
* to fix the error of htaccess not writeable warning
* <strong>[FEATURE]</strong> WP AMP — Accelerated Mobile Pages for WordPress and WooCommerce
* <strong>[FEATURE]</strong> to support webp for leverage browser caching
* <strong>[FEATURE]</strong> to exclude REST API url which start with /wp-json
* <strong>[FEATURE]</strong> Google Fonts Async [<a target="_blank" href="http://www.wpfastestcache.com/premium/google-fonts-optimize-css-delivery/">Details</a>]
* <strong>[FEATURE]</strong> Random option for Photon CDN

= 0.8.6.6 =
* to make compatible with the new rules of wordpress
* <strong>[FEATURE]</strong> Database Cleanup [<a target="_blank" href="http://www.wpfastestcache.com/premium/database-cleanup-speed-up-databases/">Details</a>]
* to decode URLs in non-latin languages for singleDeleteCache()
* to change the method of the premium update <a href="http://www.wpfastestcache.com/blog/premium-update-before-v1-3-6/">Details</a>

= 0.8.6.5 =
* <strong>[FEATURE]</strong> Compatible with GTranslate
* to exclude avada-dynamic-css-css for css optimizations
* to compatible with Safir Mobile theme
* to update htaccess after activation
* <strong>[FEATURE]</strong> Compatible with Caldera Forms
* to prevent 404 errors for non-existent minified files
* to clear the cache of post cats and the cache of post tags after update post

= 0.8.6.4 =
* to add aac, mp3, ogg extension for CDN
* to serve wp-emoji-release.min.js via cdn if CDN integration has been added
* not to show the cache for comment authors
* to show how to enable gzip warning for Nginx
* to fix the check cdn-url issue which is cURL error 6: Couldn't resolve host 
* to check http_response_code is 503 or not if DONOTCACHEPAGE is set
* to fix the warnings about clearing cache
* to fix the issue if a js source is called as ?site=site.com

= 0.8.6.3 =
* to be able to hide toolbar [<a target="_blank" href="http://www.wpfastestcache.com/features/hide-toolbar-link/">Details</a>]
* <strong>[FEATURE]</strong> Compatible with Yet Another Stars Rating
* <strong>[FEATURE]</strong> Cache Timeout with Hour and Minute [<a target="_blank" href="http://www.wpfastestcache.com/features/cache-timeout-with-hour-and-minute/">Details</a>]
* to add style for manually preload
* to fix htaccess popup
* <strong>[FEATURE]</strong>  exclude cookie

= 0.8.6.2 =
* to update user-agents with Microsoft Edge
* to fix duplicate menu problem
* to create /cache/index.html for security
* to create /cache/wpfc-minified/index.html for security
* to execute render blocking js before css and js (premium)
* to fix Undefined variable: trailing_slash_rule
* <strong>[FEATURE]</strong> Clear Cache for Specific Page [<a target="_blank" href="http://www.wpfastestcache.com/tutorial/clear-cache-for-specific-page/">Details</a>]
* to add http_host condition into htaccess
* <strong>[FEATURE]</strong> Compatible with Mailchimp mc4wp.com
* <strong>[FEATURE]</strong> Compatible with Hide My WP [<a target="_blank" href="http://www.wpfastestcache.com/features/hide-my-wp/">Details</a>]
* <strong>[FEATURE]</strong> Compatible with AMP

= 0.8.6.1 =
* <strong>[FEATURE]</strong>  exclude css sources
* to fix Non-trailing Slash problem
* to add specify source option for cdn integration
* new interface of cdn tab
* <strong>[FEATURE]</strong> to add Photon
* <strong>[FEATURE]</strong> Multiple CDN
* <strong>[FEATURE]</strong>  exclude js sources
* to improve Combine JS feature
* <strong>[FEATURE]</strong> Compatible with WpResidence theme
* <strong>[FEATURE]</strong> to call preload manually

= 0.8.6.0 =
* to fix the problem about replacing url after minify css
* to add "start with" option to the cache timeout
* to add uninstall feature
* to add "user-agent" option to the exclude page
* to convert cache time to local time
* to exclude WhatsApp user-agent

= 0.8.5.9 =
* to remove X-Wap-Profile from htaccess
* to show warning lightbox if the cache cannot be deleted
* <strong>[FEATURE]</strong> Set preload number
* refactoring of is_wptouch_smartphone()
* <strong>[FEATURE]</strong> to clear only the homepage cache
* wp nonces added for security

= 0.8.5.8 =
* to remove hostname from exclude rule
* to fix file cache problem
* to change the mobile user-agents
* to fix Wordfence Security report

= 0.8.5.7 =
* <strong>[FEATURE]</strong> Preload
* to exclude the renamed page of woocommerce
* to fix path which starts with ./ in css files
* <strong>[FEATURE]</strong> Compatible with Visual Composer Post Grid
* application/x-javascript has been added for leverage browser caching
* to prevent removing newlines from .htaccess
* <strong>[FEATURE]</strong> Compatible with WP-CLI
* to add wp-touch mobile user-agent list
* to exclude facebookexternalhit user-agent
* <strong>[FEATURE]</strong> Compatible with Any Mobile Theme Switcher
* <strong>[FEATURE]</strong> CDN for css files
* to fix the huge size of tmpWpfc problem

= 0.8.5.6 =
* to combine css files by media attribute
* to fix lots of disk usage issue
* to fix design broken which occours after some time
* to fix PHP Notice:  Undefined index: HTTP_ACCEPT
* to fix WP-Polls issue if PHP v5.6
* to remove ï»¿
* <strong>[FEATURE]</strong> to add Polish language
* <strong>[FEATURE]</strong> to add CDN77
* to get the source of fonts.googleapis.com
* new style of Exclude Page
* to set cache timeout for specific pages
* gzip for woff type
* to fix "unknown error" of AWS cdn integration
* <strong>[FEATURE]</strong> chinese language has been added
* <strong>[FEATURE]</strong> Compatible with WP-PostRatings
* to add WPFC_TOOLBAR_FOR_AUTHOR
* to fix combine css issue if a file is empty after minify
* to add woff2 for browser caching
* to disable creating cache if get_option("home") is secure and current url is not secure
* to fix switching theme from mobile to desktop on wptouch
* to remove carriage return (^M)
* <strong>[FEATURE]</strong> Finnish language has been added
* <strong>[FEATURE]</strong> Nginx support
* to use case-insensitive file system for browser caching
* WAP-Browser has been added into mobile user agent list
* to prevent 404 error for wpfc-minified after clearing minified files
* <strong>[FEATURE]</strong> to add any cdn provider
* to prevent from xss attacks (Brendon Boshell)
* to clear the cache of homepage after update static page
* to clear the cache of homepage after update sticky page
* to clear the cache of homepage after update post which appears on homepage

= 0.8.5.5 =
* to add Amazon CloudFront CDN
* to add KeyCDN
* to update Russian Language
* to fix PHP Notice: Undefined index: HTTP_USER_AGENT
* to fix PHP Notice: Undefined index: name
* to fix combine js issue with commented out js
* to fix delete minify files issue
* to add image/svg+xml for leverage browser cache
* refactoring of minify and combine css features
* to fix redirection to /wp-content/cache/all for ssl
* to add text for toolbar icon

= 0.8.5.4 =
* to be compatible with Guideline

= 0.8.5.3 =
* to check zlib extension for downloading premium automatically
* to update Portuguese and Turkish languages
* to be compatible with sub-directory installation with renamed wp-content
* refactoring of js-utilities.php
* to fix delete comment issue

= 0.8.5.2 =
* to replace https:// and http:// to // after converting inline css to link
* to replace https:// and http:// to // after converting inline js to link
* to ignore the empty css files
* to optimize exclude page feature
* to fix mobile cache issue for ipad user if wp touch is used
* to prevent caching js files whose type is text/template
* to update Portuguese language

= 0.8.5.1 =
* <strong>[FEATURE]</strong> to add MaxCDN
* to remove comments from inline js
* to fix trim() issue
* to be compatible with Leaflet Maps Marker

= 0.8.5.0 =
* to prevent combine js file which is added by WP Socializer
* to make ruleForWpContent() pasive
* refactoring of inlineToScript()
* <strong>[FEATURE]</strong> Romanian has been added

= 0.8.4.9 =
* <strong>[FEATURE]</strong> to be compatible with WP Mobile Edition
* to prevent from sql injection attacks (Kacper Szurek)
* to prevent using Head Cleaner

= 0.8.4.8 =
* to show .htaccess rules if not writeable
* not to comment out Facebook js
* not to comment out document.createElement('script')
* not to minify and combine style and js codes in noscript tag

= 0.8.4.7 =
* to update Portuguese language
* to make compatible with Google Adsense plugin
* to add user-agent of Samsung S5, LG and HTC

= 0.8.4.6 =
* to add popup warning modal for premium download
* to remove mobile cache after update post

= 0.8.4.5 =
* wpfcNOT is visible only for admins
* to prevent creating cache for POST request
* to fix issue of converting inline js to internal file

= 0.8.4.4 =
* to fix inline css issue
* to prevent caching for renamed wp-login.php

= 0.8.4.3 =
* to stop caching for /wp-api/v1, /cart, /checkout, /receipt, /confirmation, /product - WooCommerce
* to prevent caching if useragent Mediapartners-Google
* to decrease pcre.recursion_limit in css optimization to prevent Internal Server Error

= 0.8.4.2 =
* to fix premium page issue

= 0.8.4.1 =
* exclude page is case insensitive
* to check buffer is json or not for checkWoocommerceSession()
* not to comment out Google Analytics by Yoast

= 0.8.4.0 =
* refactoring of checkHtml()

= 0.8.3.9 =
* other plugins can use the functions
* to put ";" at the end of js file if last char is not ";"
* to make compatible renamed wp-content sites

= 0.8.3.8 =
* to prevent using GZip Ninja Speed Compression
* to prevent using GZIP Output
* to show success message after saving exclude page
* style of left panel
* opera mini has been added into mobile user agent list
* to fix download premium version issue

= 0.8.3.7 =
* to prevent confliction on left menu
* to fix error and success message icon

= 0.8.3.6 =
* MIDP has been added into mobile user agent list

= 0.8.3.5 =
* to fix vulnerability (discoverd by 0pc0deFR aka Kevin FALCOZ)
* to fix issue of moving chartset to the top
* to prevent combine Google Fonts javascripts
* <strong>[FEATURE]</strong>  exclude page

= 0.8.3.4 =
* to prevent inline to external if the style is used in the javascript
* to prevent creating cache for xmlrpc.php
* to fix white page issue because of combine css and js
* icons for premium version

= 0.8.3.3 =
* WPtouch issue has been solved
* improvement of cache delete
* to add ";" at the end of JS file if it does not exist

= 0.8.3.2 =
* to prevent comment out google analitics code
* refactoring of isPasswordProtected()
* the clear cache button on toolbar is available for editors

= 0.8.3.1 =
* index.html files have been added intead of .htaccess
* to prevent comment out inline js rules twice
* <strong>[FEATURE]</strong>  to add delete button on the admin bar
* to fix url() problem for data:image/svg+xml

= 0.8.3.0 =
* to fix <!--[wpfcNOT]--> issue of text to visual
* refactoring of redirect rule
* to prevent the directory access
* to prevent from xss attacks (Kacper Szurek)

= 0.8.2.9 =
* to fix 301 redirection issue of sub-folder
* to support non-english characters on search

= 0.8.2.8 =
* to prevent caching wp-login.php if renamed
* to prevent caching if the page has a contactform7 form with captcha
* to prevent caching for ajax call

= 0.8.2.7 =
* to implement single post cache deletion when a post/page is modified
* <strong>[FEATURE]</strong> to implement new frequency values for cache timeout
* html corrupted warning has been added
* to make compatible with WooCommerce Themes
* to prevent combine inline css if commented out

= 0.8.2.6 =
* to fix sub-domain redirect issue with www
* to prevent caching of sitemap.xml
* to fix getting error when .htaccess is not found
* to improve combine css
* to prevent caching wp-comments-post.php
* to prevent combine comment out js files
* to prevent caching js files whose type is application/ld+json
* style changes of delete cache panel
* to fix php warning which is Invalid argument supplied for foreach()
* to fix "File not found" message when trying to leave a comment

= 0.8.2.5 =
* to prevent converting style rules to link more than once
* to clear cache after admin writes a comment
* to clear cache if comment has not ben manually approved 
* to disable minute and hour when hourly is selected
* to show both time when twice daily is selected on cache timeout panel

= 0.8.2.4 =
* rewrite rule issue has been solved
* to remove empty chars from url()
* to add media type for inline css after minify

= 0.8.2.3 =
* to support setting hour and minute as a 0
* to fix server time NaN
* to check the length of inline css for combine css
* to support selecting the css files which do not include home_url()
* to support selecting the js files which do not include home_url()
* publish_page to save_post

= 0.8.2.2 =
* to minify css files which are NOT "media='all'"
* to support selecting the css files which do not include home_url()
* to insert define('WP_CACHE', true) into wp-config.php for wp-postviews
* to fix PHP Warning: Missing argument 2 for CssUtilities::minifyCss()
* to fix PHP Warning: scandir warning

= 0.8.2.1 =
* to support WP-PostViews
* tab of minified css and js has been removed
* warning about Microsoft IIS has been added
* to prevent minify and combine css if returns 404
* to prevent combine js if returns 404
* warning about Multisite has been added

= 0.8.2.0 =
* warning of regular expression is too large has been added
* <strong>[FEATURE]</strong> to be able to choose specific time
* js and css merging is not beta anymore

= 0.8.1.9 =
* to delete cachen when page is edited or published
* warning of DONOTCACHEPAGE has been added
* file_get_contents_curl() issue for the files which start with //
* to combine the css files which has media="all" attribute
* to fix re-write rule for sub-directory installation
* <strong>[FEATURE]</strong> to prevent 304 browser caching to see new post
* <strong>[FEATURE]</strong> wpfcNOT works for pages as well except the themes
* the warning has been added for empty buffer

= 0.8.1.8 =
* to fix disable the plugin
* to check permalinks was set or not
* modified of deletion of minified files' warning
* to fix inserting extra comment tag

= 0.8.1.7 =
* wp-polls issue
* cache timeout issue
* minify css issue for data:application/x-font-woff

= 0.8.1.6 =
* optimization of deletion cache
* creating cache problem when combine css is unchecked

= 0.8.1.5 =
* <strong>[FEATURE]</strong> JS Combine
* to check that super cache is active or not
* to check that better wordPress minify is active or not
* <strong>[FEATURE]</strong> french translation

= 0.8.1.4 =
* to prevent creating cache for logged-in users
* gzip for svg, x-font-ttf, vnd.ms-fontobject, font/opentype font/ttf font/eot font/otf
* stlye files issue with https
* <strong>[FEATURE]</strong> Keep Alive
* compatible with @import "style.css";
* <strong>[FEATURE]</strong> italian language has been added

= 0.8.1.3 =
* to support renamed wp-content

= 0.8.1.2 =
* to fix combine css breaking css down
* the password protected posts are not cached
* change of minified css file name

= 0.8.1.1 =
* to show which style files are combined
* to fix the minify css issue
* to fix minify css breaking css down

= 0.8 =
* <strong>[FEATURE]</strong> Supports "Subdirectory Install"
* <strong>[FEATURE]</strong> SSL support
* <strong>[FEATURE]</strong> Leverage browser caching has been added
* GZippy warning has been added
* Path issue of rewrite rules has been solved
* to prevent create cache for mobile devices
* <strong>[FEATURE]</strong> Enable/Disable cache option for logged-in users has been added
* Improvement of Turkish and Spanish translation
* Issue of subdirectory install using with subdirectory url
* Double slash in the rewrite rule problem has been solved
* Full path is written instead of %{DOCUMENT_ROOT}
* Stop to prevent not to minify css files which has small size
* Improvement of detection active plugins
* <strong>[FEATURE]</strong> "Combine Css" has been added
* Stop to prevent not to minify css files which has small size
* Improvement of detection active plugins
* <strong>[FEATURE]</strong> "Combine Css" has been added
* Improvement of combine css
* to prevent creating cache for the urls which has query string

= 0.7.9 =
* <strong>[FEATURE]</strong> Compatible with WP-Polls
* <strong>[FEATURE]</strong> Enable/Disable cache option for mobile devices has been added
* <strong>[FEATURE]</strong> "[wpfcNOT]" shortcode has been converted to the image
* Optimization of CSS minify
* r10.net support forum url has been added
* Some style changes
* to correct misspelling
* Icon has been changed
* <strong>[FEATURE]</strong> Portuguese language has been added
* <strong>[FEATURE]</strong> German language has been added
* Minify css issue has been solved
* <strong>[FEATURE]</strong> Blackberry PlayBook has been added into mobiles
* <strong>[FEATURE]</strong> www and non-www redirections have been added

= 0.7.8 =
* <strong>[FEATURE]</strong> Delete Minified Css & Js feature has been added
* Update of Spanish translation
* Update of Turkish translation
* Update of Russian translation
* Update of Ukrainian translation

= 0.7.7 =
* Optimization of CSS minify
* rmdir, mkdir and rename error_log problem
* modify .htaccess problem
* Update of Spanish translation
* Update of Turkish translation
* Update of Russian translation
* Update of Ukrainian translation

= 0.7.6 =
* <strong>[FEATURE]</strong> Gzip Compression

= 0.7.5 =
* Performance of delete all files is improved
* Rewrite rules of WPFC is removed from .htaccess when wpfc is deactivated
* CSS of Warnings has been changed

= 0.7.4 =
* Minify Css problem has been solved
* Info panel has been added
* Update of Spanish translation
* Update of Turkish translation
* Update of Russian translation
* Update of Ukrainian translation

= 0.7.3 =
* Info Tip has been added

= 0.7.2 =
* <strong>[FEATURE]</strong> Minify CSS files

= 0.7.1 =
* Delete Cron Job when the plugin is deactivated
* Delete from DB when the plugin is deactivated

= 0.7 =
* <strong>[FEATURE]</strong> works with Wordfence properly
* <strong>[FEATURE]</strong> 404 pages are not cached
* urls which includes words that wp-content, wp-admin, wp-includes are not cached
* The issue about cache timeout has been solved
* <strong>[FEATURE]</strong> Cache Timeout has been added
* <strong>[FEATURE]</strong> Spanish language has been added
* <strong>[FEATURE]</strong> Minify html
* <strong>[FEATURE]</strong> Supported languages: Russian, Ukrainian and Turkish
* <strong>[FEATURE]</strong> "Block Cache For Posts and Pages" has been added as a icon for TinyMCE and  Quicktags editor
* Cache file is not created if the file is exist
* Cached files are deleted after deactivation of the plugin

= 0.6 =
* Cached file is not updated after comment because of security reasons
* Checking corruption of html
* Creation time of file has been added
* "Not cached version" text has been removed
* Some style changes
* System works under sub wp sites
* Plugin URI has been added
* Dir path has been removed from not cached version 
* Some styles changes
* Some styles changes

= 0.5 =
* <strong>[FEATURE]</strong> Admin can delete all cached files from the options page
* <strong>[FEATURE]</strong> All cache files are deleted when a post or page is published
* <strong>[FEATURE]</strong> Blocking cache with Shortcode

== Frequently Asked Questions ==

= How do I know my blog is being cached? =
You need to refresh a page twice. If a page is cached, at the bottom of the page there is a text like "&lt;!-- WP Fastest Cache file was created in 0.330816984177 seconds, on 08-01-14 9:01:35 --&gt;".

= Does it work with Nginx? =
Yes, it works with Nginx properly.

= Does it work with IIS (Windows Server) ? =
Yes, it works with IIS properly.

= What does ".htaccess not found" warning mean? =
Wpfc does not create .htaccess automatically so you need to create empty one.

= How is "tmpWpfc" removed? =
When the cached files are deleted, they are moved to "tmpWpfc" instead of being deleted and a cron-job is set. Delete all files are so difficult for server so cron-job is set not to use a lot of CPU resources. Cron-job is set and it deletes 100 files every 5 minutes. When all files in "tmpWpfc" are deleted, cron-job is unset.

= Does Wpfc work with WPMU (Wordpress Multisite) properly? =
No. Wpfc does not support Wordpress Multisite yet.

= Does Wpfc work in "Subdirectory Install"? =
Yes. Wpfc supports "Subdirectory Install".

= Is this plugin compatible with Http Secure (https) ? =
Yes, it is compatible with Http Secure (https).

= Is this plugin compatible with Adsense? =
Yes, it is compatible with Adsense 100%.

= Is this plugin compatible with CloudFlare? =
Yes, it is but you need to read the details. <a href="http://www.wpfastestcache.com/tutorial/wp-fastest-cache-cloudflarecloudfront/">Click</a>

= Is this plugin compatible with WP-Polls? =
Yes, it is compatible with WP-Polls 100%.

= Is this plugin compatible with Bulletproof Security? =
Yes, it is compatible with Bulletproof Security 100%.

= Is this plugin compatible with Wordfence Security? =
Yes, it is compatible with Wordfence Security 100%.

= Is this plugin compatible with qTranslate? =
Yes, it is compatible with qTranslate 100%.

= Is this plugin compatible with WPtouch Mobile Plugin? =
Yes, it is compatible with WPtouch Mobile Plugin 100%.

= Is this plugin compatible with Any Mobile Theme Switcher Plugin? =
Yes, it is compatible with Any Mobile Theme Switcher Plugin 100%.

= Is this plugin compatible with WP-PostRatings? =
Yes, it is compatible with WP-PostRatings.

= Is this plugin compatible with AdRotate? =
No, it is NOT compatible with AdRotate.

= Is this plugin compatible with WP Hide & Security Enhancer? =
No, it is NOT compatible with WP Hide & Security Enhancer.

= Is this plugin compatible with WP-PostViews? =
Yes, it is compatible with WP-PostViews. The current post views appear on the admin panel. The visitors cannot see the current post views. The developer of WP-PostViews needs to fix this issue.

= Is this plugin compatible with MobilePress? =
No, it is NOT compatible with MobilePress. We advise WPtouch Mobile.

= Is this plugin compatible with WooCommerce Themes? =
Yes, it is compatible with WooCommerce Themes 100%.

== Upgrade notice ==
....
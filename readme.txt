=== Vladimir Admin IDs ===
Tags: admin, ids, posts, pages
Requires at least: 6.8
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 0.1.3
License: GPL-2.0
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Show object IDs in WordPress admin tables and copy ID-based links to posts and pages.

== Description ==

The ID column identifies each item in its admin list. Posts and pages also have a separate column with a selectable URL and a copy button. Each site in a network uses its own front-end address.

Numeric IDs appear in the standard tables for posts, pages, custom post types, media (list view), comments, terms and users. Sites using the link manager also show bookmark IDs. You can hide the columns individually in Screen Options.

Post links use /?p=ID and page links use /?page_id=ID. WordPress handles redirects and access. A link to an unpublished item does not grant public or preview access. Password-protected content keeps its password prompt. Users can select the URL even if JavaScript is disabled or the browser denies clipboard access.

== Installation ==

Upgrading from 0.1.2 or earlier: deactivate the old version before installing and activating 0.1.3. WordPress lists the two directories as separate plugins. Review the ID columns in Screen Options after switching.

Upload the plugin ZIP in the WordPress admin and activate Vladimir Admin IDs. If Simply Show IDs is active, deactivate it to avoid duplicate ID columns. See Anleitung.txt for the German user guide.

== Credits ==

Based on Simply Show IDs 1.3.3, Copyright (c) 2009-2010 Matt Martz (http://sivel.net). The original author credited Joel Fisher for the idea. Vladimir Admin IDs is maintained by Vladimir Simović (https://www.perun.net/). Distributed under the GNU General Public License v2. See LICENSE.txt.

== Changelog ==

= 0.1.3 =
* Use Vladimir for the plugin directory, main file and internal identifiers.

= 0.1.2 =
* Rename Perun Admin IDs to Vladimir Admin IDs and add author details. The plugin directory stays the same for upgrades.
* Lower the minimum requirements to WordPress 6.8 and PHP 8.1.

= 0.1.1 =
* Escape the translated ID and ID link column headings as plain text.

= 0.1.0 =
* Show numeric IDs in WordPress admin list tables.
* Add selectable ID-based links and local clipboard controls to post and page lists.

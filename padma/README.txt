=== Padma ===
Contributors: Padma Unlimited Team
Requires at least: WordPress 4.7
Tested up to: WordPress 5.1
Version: 1.1.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: theme, template, template builder, builder, theme builder, padma, flexible, flexible-header

== Description ==

Padma is a Wordpress Framework template system. Padma allows you to easily create the most beautiful WordPress websites built from a completely blank canvas.


== Installation ==

1. Upload Padma Base via FTP to your wp-content/themes/ directory.
2. Go to your WordPress dashboard and select Appearance.
3. Select Padma and click activate.


== Copyright ==

Copyright 2014-2019 Plasma Soluciones M.S.V S.A.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA


== Changelog ==



= 1.1.10
* Released: On Dev

Tested with WordPress 5.2.2
Tested with ClassicPress 1.0.1
Fixed: Custom code minor fix
Fixed: Compatibility with Updater plugin auto update
New features on content block
- Added: Custom Excerpts Length (Thanks to https://github.com/raydale)
- Added: Custom Archive title
New Option to upgrade and use developer/preview version
Added toggle option to slider inputs (Block API)
Added constant PADMA_DISABLE_PHP_VERIFICATION, if true Padma will not verify PHP version
Added 'font-display: swap' to Google Fonts to ensure text remains visible during webfont load
Added better plugin templates support
Added option to exclude current post from PinBoard block when custom query is used
Updated Constant PADMA_DISABLE_PHP_PARSING is true by default
Updated Custom Code Block, to execute PHP functions require PADMA_DISABLE_PHP_PARSING = false in wp-config.php



= 1.1.0
* Released: April 24, 2019

Fixed: Block hidden option on responsive
Fixed: Parallax: background images jump to the left when Parallax is activated.
Fixed: Responsive custom breakpoint issue
Fixed: WooCommerce product design selectors
- Product title fonts size
- Product Button
Fixed: PinBoard refresh on design mode
Fixed: Search Results message
Fixed: Block type selector issue
Fixed: Structured-data schema on content block
Fixed: Issue when showing templates saved on Padma Services account
Fixed: Navigation block issue
Fixed: WPML Compatibility home_url when loading VE
Updated jQuery Superfish Menu Plugin to v1.7.10
New Contact Form 7 Block
New Mailchimp for WordPress Block
New PadmaQuery Class
Added new meta data support to Content Block
 - publisher
 - publisher_img
 - publisher_no_img
 - modified_date
Added Compatibility with mod_pagespeed
Added HTTP/2 Server Push support
Added Compatibility with aMember Pro V4 (plugin version v1.1)
Added Support for Blox Theme templates (Headway already was supported)
Added Logo option to Customize > Site Identity > Site Logo
Added better Schema.org support
Added Blocks to Shortcode support (to use blocks anywhere)



= 1.0.0
* Released: February 28, 2019

Tested with WordPress 5.1
Tested with ClassicPress 1.0.0-rc2
Visual Editor Fixes
- Responsive breakpoints fix
- Select block filter fix
- ToolTip fix
- Mobile preview fix
Footer block update: Now Custom Copyright support %Y% to show current year
Admin support notice
New option to prevent mirror Wrapper styles


= 0.3.1
* Released: February 8, 2019

Visual Editor Fixes
- Fixed Blocks Elements Group issue
- Loading issue
- Navigation block issue
API Panel radio input support
Vertical align added to design options


= 0.3.0
* Released: January 24, 2019

Tested with ClassicPress 1.0.0-beta2
Visual Editor Fixes
- Fixed: Block settings didn't save
- Fixed: Font family update when changed on design panel
- Fixed: Repeater, input for multiple contents 
Tools > System info Updated
Divi Builder Plugin compatibility update
jQuery compatibility update


= 0.2.2
* Released: January 10, 2019

Tested with WordPress 5.0.3
Added Import/Export option to wrappers.


= 0.2.1
* Released: December 14, 2018

Tested with WordPress 5.1 Alpha
Visual Editor Fixes
- Fixed extra padding on both Grid and Design View.
- LiveCSS fixes


= 0.2.0
* Released: December 11, 2018

Tested with WordPress 5
Tested with Gutenberg Plugin 4.6.1
Tested with ClassicPress Beta 1
Visual Editor Fixes
- Fixed CSS Transform VE issue
- Missing setup panel fixed
- Added tab index for inputs on panel
Code Fixes
- Remove PHP Notices
- Content block query fix 
- API Block fixes
- Navigation block fixes
Design Mode Updated
- Added Advanced option group
Offline notification
New loader animation
WooCommerce compatibility update
Gutenberg compatibility update
Headway compatibility update
Filter options added to Select Block Type panel
Auto Update Support
Allow mobile zooming
Added Blox 1.0.X Templates support
Improved Headway/Blox Templates support


= 0.1.0
* Released: October 29, 2018

Tested with WordPress 5 Beta 1
Tested with ClassicPress Alpha
PHP 7.3 Ready
Visual Editor Fixes
Design Mode updates
- Added more CSS Units
Grid updates
- Support to delete one or multiple blocks with the delete key
New Audio block
New Video block
New Object-fit and Object-position options on sizes
JS libraries updates
- Underscore updated to 1.9.1
- jQuery UI Touch Punch updated to 0.2.3
Slider block updated
Footer new option to hide "Show full site" on mobile.
WooCommerce compatibility update


= 0.0.24
* Released: September 5, 2018
Save on cloud updates
Visual editor upgrades 
- Duplicate wrappers with or without blocks
- Use empty layout fixes
Other fixes


= 0.0.23
* Released: August 17, 2018

Visual Editor fixes
Live CSS editor fixes
Tested on Wordpress 4.9.8
Block API improvements
Responsive Options updated on block options
CSS Transform options added to Visual Editor and API Block
Compilator fixes
Removed deprecated WordPress constants
PHP 7+ Compatibility fix (Removed "The /e modifier" warning on preg_replace() function)


= 0.0.22
* Released: Jul 31, 2018

PHP 7.2 compatibility fixes
Tested on Wordpress 4.9.8-RC2
Ckeditor updated to 4.9.2
RequireJS updated to 2.3.5
jQuery and SizzleJS updated to 3.3.1 and 2.3.3 respectively
Knockout updated to v3.4.2
Live CSS Editor Fixes
Visual Editor Fixes
Child themes support Fixes

= 0.0.21
* Released: Jun 11, 2018

Several improves to Pinboard block 
Added Undo / Redo panel options
CSS Animation Code Fix and improvements
Added filter option for design options

= 0.0.20
* Released: May 23, 2018

Headway (3.7.0 to 3.8.9 templates support (not Headway 4.x)
Save templates on cloud with Padma Services
Added an option for prevent plugins installation recommendation (Updater and Services)
Content Editor feature improvements
New option for change the lateral panel of side in design mode
Code fixes for PHP and Javascript

= 0.0.19
* Released: April 30, 2018

Added size properties to Design editor options for wrappers and blocks
Content editor fixes
Google Web Fonts Variant fixed
Headway Blocks Compatibility
Native CSS Animations added to Design mode
PHP 7.0 or higher compatibility check
Preview devices fixes
Vertical navigation block, current option indicator
Visual composer support for Content Editor
Visual Editor favicon
Web Fonts Library Updated


= 0.0.18
* Released: April 3, 2018

Solved 2 bug for navigation block
Solved 1 bug for footer.


= 0.0.17 =
* Released: March 21, 2018

Code fixes for PHP and Javascript
CSS Editor improvement for Global Styling panel
Some unused libraries were removed
Padma LifeSaver plugin compatibility


= 0.0.16 =
* Released: March 16, 2018

Updated Visual Editor night mode colors
Updated "Grid Wizard" to "Grid Manager"
Content editor fixes
License references were removed.
Cleaning of code and fixes for PHP and Javascript


= 0.0.15 =
* Released: March 14, 2018

Code fixes for PHP and Javascript
Initial changes for content editor


= 0.0.14 =
* Released: March 1, 2018

Slide menu fix for older browsers
Code fixes for PHP and Javascript


= 0.0.13 =
* Released: February 27, 2018

Code fixes for PHP and Javascript
Preview devices in design mode


= 0.0.12 =
* Released: February 19, 2018

Codemirror minor fix for HTML


= 0.0.11 =
* Released: February 8, 2018

Support for updater plugin


= 0.0.10 =
* Released: February 5, 2018

Fixed Google Web Fonts API


= 0.0.9 =
* Released: January 30, 2018

Improved navigation block
Code fixes for PHP and Javascript


= 0.0.8 =
* Released: January 29, 2018

Updates from CDN implemented


= 0.0.7 =
* Released: January 22, 2018

CSS and night mode fixes
Integration of TGM-Plugin-Activation to recommends Padma Services plugin


= 0.0.6 =
* Released: January 12, 2018

CSS and images fixes


= 0.0.5 =
* Released: January 9, 2018

Code fixes for PHP and Javascript
Change live css editor to CodeMirror
Added night / day mode support to live css editor


= 0.0.4 =
* Released: January 3, 2018

Code fixes
Added night / day mode switcher to visual editor
Tested on Wordpress 5 alpha


= 0.0.3 =
* Released: January 1, 2018

Code fixes for PHP and Javascript


= 0.0.2 =
* Released: December 25, 2017

Renamed all references from Box to Padma


= 0.0.1 =
* Released: December 24, 2017

Initial release based on BloxTheme 1.0.3

=== Infinitum ===
Contributors: laubsterboy
Requires at least: 6.5
Requires PHP: 8.1
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



== Description ==

A clean and elegant starter theme, or framework, used to build beautiful websites. Infinitum also fully supports the Beaver Builder page builder plugin.



== Changelog ==

= 0.0.1-beta-2 - 2024-04-23 =

* Added: Added theme support for "menus" when the Beaver Builder plugin is installed and activated.
* Updated: The Beaver_Builder::insert_form_item method has been updated to add a new $adaptive parameter
* Updated: Beaver Builder CSS to set fixed row max width to be the same fluid value that is used for block content (this helps make content look more appealing at high resolutions such as 2K and 4K).
* Fixed: Beaver Builder infinitum-typography CSS has been completely re-written to automatically work with MOST modules by using the module form preview selector (as long as a module typography field has ['preview']['selector'] this will work automatically). All other modules have been manually coded for, so all Beaver Builder modules are now supported.

= 0.0.1-beta-1 - 2024-04-19 =

* Added: Beaver Builder CSS breakpoints are now available as CSS variables (--wp--custom--beaver-builder--breakpoints--large-px, --wp--custom--beaver-builder--breakpoints--medium-px, --wp--custom--beaver-builder--breakpoints--responsive-px)
* Added: The Drawer block now supports "Shadows", which will apply to the open button.
* Fixed: The Drawer block was causing a CLS (cumulative layout shift) on page load due to default styles not being applied to the modal and is now fixed.
* Fixed: Beaver Builder spacing and typography now have proper default values (including responsive values)
* Fixed: Beaver Builder FLBuilderPreview._getDefaultValue has been overridden with a custom method to prevent margin, padding, font-size, and line-height (all properties that we're managing with "Theme" fields) from being used in the inline preview CSS (particularly when switching responsive sizes).
* Fixed: Beaver_Builder::insert_form_item method removing section titles for all forms (rows, columns, modules, etc) and this has been fixed.

= 0.0.1-alpha-2 - 2024-03-27 =

* Added: front-page.html template with the page meta data (featured image, title, and breadcrumb) hidden.
* Added: Basic duotones and gradients to the theme config (theme.json)
* Added: A theme method to remove some theme supports (core-block-patterns and starter-content)
* Added: Integrations and Theme Features (all "Addon" classes) now have the option to hook into the theme_activation and theme_deactivation methods to perform tasks, such as set or cleanup options.
* Added: "Dancing Script" and "Raleway" font faces and set "Raleway" as the default.
* Added: Theme logo and mark images
* Added: Theme screenshot so it can be seen in the list of "Themes"
* Updated: The Drawer block now defaults to showing the first created Drawer post, which after the theme is activated is the "Header" drawer that is automatically created.
* Updated: The Drawer block now has very basic support for the Interactivity API so other blocks could communicate. This is not yet finalized.
* Fixed: Beaver Builder CSS that makes layouts using the Block Editor and Beaver Builder uniform was removing padding on mobile devices and has been re-written.
* Fixed: Beaver Builder global defaults were setting many values to 0 that were otherwise blank and it caused undesired side effects with responsive layout.

= 0.0.1-alpha-1 - 2024-03-26 =

* Initial release



== Upgrade Notice ==

= 0.0.1-beta-2 =

* This includes breaking changes to the Beaver_Builder::insert_form_item method.

= 0.0.1-beta-1 =

* This includes breaking changes for older versions that used Beaver Builder and set module, column, or row spacing using the "Theme Spacing". These values will need to be set again.

= 0.0.1-alpha-1 =

* Initial release



== Copyright ==

Infinitum WordPress Theme, (C) 2023-2024 John Russell.
Infinitum is distributed under the terms of the GNU GPL.
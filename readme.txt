=== Infinitum ===
Contributors: laubsterboy
Requires at least: 6.6
Requires PHP: 8.1
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html



== Description ==

A clean and elegant starter theme, or framework, used to build beautiful websites. Infinitum also fully supports the Beaver Builder page builder plugin.



== Changelog ==

= 1.3.1 - 2025-03-21 =
* Updated: modal.js to fix console errors when closing before the open animation has finished and added a window resize listener to update the modal offsets.

= 1.3.0 - 2025-03-12 =
* Updated: modal.js to add a new feature to automatically add offsets to the modalElement (top, right, bottom, and/or left) based on the position of the openElement, also another feature to allow for scrolling to reveal the open button and maximize the space available for the modal
* Updated: infinitum/drawer block to support the new modal.js features

= 1.2.2 - 2025-03-07 =
* Updated: modal.js and infinitum/drawer view.js so that when one modal is opened it will close all others that are currently open, in case open/close buttons are in a fixed position and visible when modals are open

= 1.2.1 - 2025-03-07 =
* Updated: infinitum/drawer CSS for disabled open/close buttons

= 1.2.0 - 2025-03-06 =
* Updated: infinitum/drawer block to add a toggle to be able to place the close button in the modal or have it be a sibling of the open button, and cleaned up the interactivity API

= 1.1.3 - 2024-11-12 =
* Updated: The color palette to increase the contrast of the accent color against white so it can be used as a link/target

= 1.1.2 - 2024-11-12 =
* Updated: style.css to add WCAG related custom properties so they can be used in theme development and updated in one place

= 1.1.1 - 2024-11-05 =
* Updated: style.css for the .has-<#>-font-size classes to include the 90 and 80 sizes

= 1.1.0 - 2024-10-18 =
* Fixed: The CSS rule for images (and other media) to set the height to auto so the proportions are maintained

= 1.0.0 - 2024-09-18 =
* Updated: style.css for the .has-<#>-font-size classes to exclude h# tags
* Fixed: theme.json settings.layout.wideSize so that it properly automatically adjusts when the settings.custom.infinitum.contentWidth or settings.custom.infinitum.contentWidthWideRatio

= 0.0.1-beta-4 - 2024-04-29 =
* Added: Spacing to the bottom of the core/list block to match paragraphs and headings.
* Fixed: The parent infinitum theme will attempt to enqueue the child theme style.css if the current theme is a child theme and now the "version" matches the version of the child theme rather than the parent theme version.

= 0.0.1-beta-3 - 2024-04-26 =
* Added: Initial child theme support.
* Added: Added a new theme spacing for 1/8 to allow for thin borders

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
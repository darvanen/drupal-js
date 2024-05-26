# Collapsiblock

The Collapsiblock module provides the functionality to make blocks collapsible.

This module is intended for site-builders who are new to Drupal with relatively
simple needs. We will try to accommodate feature requests and options but will
balance those with the need for a simple UI.

For a full description of the module visit
[project page](https://www.drupal.org/project/collapsiblock).

To submit bug reports and feature suggestions, or to track changes visit
[issue queue](https://www.drupal.org/project/issues/collapsiblock).

## Contents of this file

- Requirements
- Installation
- Configuration
- Troubleshooting
- Maintainers

## Requirements

This module requires no modules outside of Drupal core.

## Installation

Install the Collapsiblock module as you would normally install a contributed
Drupal module. Visit
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules)
for further information.

### JS Library

This module uses a CDN for appraisal purposes but you should install the
slide-element javascript library in your site if you decide to use it. Save
https://unpkg.com/slide-element@2.3.1/dist/index.umd.js in your web root as
`/libraries/slide-element/index.umd.js`.

## Configuration

1. Navigate to `Administration > Extend` and enable the module.
2. Navigate to `Administration > Structure > Block Layout` and place a block.
3. Now when placing blocks there is an additional fieldset on the
   configuration page. Choose the "Block collapse behavior". Save block.
4. If you want to set global settings for collapsiblock, navigate to
   `Administration > Configuration > User Interface > Collapsiblock`.

The global settings allow you to:

1. Choose a default action for all blocks.
2. Choose whether to save the last state of blocks in a cookie for each user.
3. Choose whether active links are kept open (useful for menu blocks).

## Troubleshooting

If your blocks are not behaving as expected, ensure that the template
for your block(s) uses the `{{ title_prefix }}` and `{{ title_suffix }}`
elements to wrap the portion of the block that should be used as the
non-collapsing, clickable portion of the block. The JS script will try to
collapse anything outside that wrapper that is in the block except for
contextual links which have their own behaviors.

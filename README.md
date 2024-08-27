# Drupal JS

This is currently just a proof of concept using the Collapsiblock and Foxy modules. The end goal is to be
able to follow a pattern that allows modules to opt-in to Foxy and compile everything in one go, splitting out common
dependencies.

This would be particularly useful in enabling Drupal contributors to use JS dependencies without including them in the
built code of their module, thus reducing the amount of code downloaded by the end user.

The two non-standard things this project prototype has beyond those modules is the vite.config.js file and the post-install /
post-update hooks to run vite.

To see the POC in action, clone this repository, then run:

```bash
composer install
drush si
drush en -y foxy collapsiblock
drush uli
```

Then
* Log in using the link
* Go to /admin/config/system/foxy
* Enter `web` and `/libraries/compiled/` in the two settings fields
* Save the form


* Go to the home page
* Use the contextual menu to configure a block
* Turn the title display on and set the Collapsiblock settings to something other than 'none'
* Save the form


* Go back to the home page
* Click on the title of the block you edited


Expected results
* The block will expand/collapse when you click the title
* There is a generated package.json file in the project root
* There is a set of installed `node_modules` including a Collapsiblock dependency: `slide-element`
* There is a compiled JS asset for collapsiblock in `web/libraries/compiled`
* There is a compiled CSS asset for collapsiblock in `web/libraries/compiled/assets`

Next steps:

* [Support CSS/image/etc assets](https://www.drupal.org/project/foxy/issues/3452336)

You can find me on
[Drupal Slack](https://www.drupal.org/community/contributor-guide/reference-information/talk/tools/slack) as @darvanen.
Come join the #frontend-bundler-initiative channel there!

# Drupal JS

This is currently just a proof of concept using a modified version of the Collapsiblock module. The end goal is to be
able to follow a pattern that allows modules to opt-in to Foxy and compile everything in one go, splitting out common
dependencies.

This would be particularly useful in enabling Drupal contributors to use JS dependencies without including them in the
built code of their module, thus reducing the amount of code downloaded by the end user.

To see the POC in action, clone this repository, then run:

```bash
composer install
drush si
drush en -y foxy collapsiblock
drush uli
```

Then
* Follow the link
* Go to the home page
* Use the contextual menu to configure a block
* Turn the title display on and set the Collapsiblock settings to something other than 'none'
* Go back to the home page
* Click on the title of the block you edited

Expected results
* The block will expand/collapse when you click the title
* There is a generated package.json file in the project root
* There is a set of installed `node_modules` including a Collapsiblock dependency: `slide-element`
* There is a compiled version of collapsiblock in `web/libraries/compiled/collapsiblock`

Next steps:

* Support CSS/image/etc assets

You can find me on
[Drupal Slack](https://www.drupal.org/community/contributor-guide/reference-information/talk/tools/slack) as @darvanen.

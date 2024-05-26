# Drupal JS

This is currently just a proof of concept using a modified version of the Collapsiblock module. The end goal is to be
able to follow a pattern that allows modules to opt-in to Foxy and compile everything in one go, splitting out common
dependencies.

This would be particularly useful in enabling Drupal contributors to use JS dependencies without including them in the
built code of their module, thus reducing the amount of code downloaded by the end user.

To see the POC in action, clone this repository, then run:

```bash
composer install
./node_modules/vite/bin/vite.js build
```

You should see
* a generated package.json file in the project root
* a set of installed `node_modules` including a Collapsiblock dependency: `slide-element`
* a compiled version of collapsiblock in `web/libraries/compiled`

Next steps:

* Create a hook in the foxy module that redirects library definitions to the compiled version

You can find me on
[Drupal Slack](https://www.drupal.org/community/contributor-guide/reference-information/talk/tools/slack) as @darvanen.

# Drupal JS

This is currently just a proof of concept using a couple of modified libraries from npm that share a dependency. The end
goal is to be able to publish Drupal module JS to a package registry (maybe
[in Drupal's GitLab instance](https://docs.gitlab.com/ee/user/packages/npm_registry/index.html)?) and compile everything
in one go, splitting out common dependencies.

This would be particularly useful in enabling Drupal contributors to use JS dependencies without including them in the
built code of their module, thus reducing the amount of code downloaded by the end user.

To see the POC in action, clone this repository, then run `composer install`.

What you should see is a package.json


You can find me on
[Drupal Slack](https://www.drupal.org/community/contributor-guide/reference-information/talk/tools/slack) as @darvanen.

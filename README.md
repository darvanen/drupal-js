# Drupal JS

This is currently just a proof of concept using a couple of modified libraries from npm that share a dependency. The end
goal is to be able to publish Drupal module JS to a package registry (maybe
[in Drupal's GitLab instance](https://docs.gitlab.com/ee/user/packages/npm_registry/index.html)?) and compile everything
in one go, splitting out common dependencies.

This would mainly be useful for Drupal contributors who want to use JS dependencies without including them in the built
code of their module.

To see the POC in action, clone this repository, copy `example.package.json` to `package.json`, then:

```bash
npm install
npm run compile:libraries
npm run build
```

You should see the compiled libraries appear in `web/libraries/compiled`, with three pairs of built files for:

- infinite-tree
- nob_pubsubsql
- events - this is the common dependency

You can find me on
[Drupal Slack](https://www.drupal.org/community/contributor-guide/reference-information/talk/tools/slack) as @darvanen.

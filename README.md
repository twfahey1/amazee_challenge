# Amazee Challenge!

Welcome to the Amazee Challenge repo, taken on by Tyler Fahey!

## Quickstart: How to evaluate my solution
* Since this is based on the [Composer template from Amazee](https://github.com/amazeeio/drupal-example), the standard setup instructions should suffice for getting the environment setup locally.
* Run `docker-compose exec cli drush en ultimate_lexparser_example -y` - This will enable the `ultimate_lexparser_example` module. This includes a sample content type that makes evaluation quick and easy, as well as enabling the base `ultimate_lexparser` module.
* Login as an admin - `docker-compose exec cli drush uli --uri=http://drupal-example.docker.amazee.io`
* Go to `/node/add/flexparser_example_page`, and provide any title, and any formula in the "Example Text Parser Field".
* Save the node, and you should see the computed formula on the rendered node.

## Overview
The challenge states the following:
>1.	The Parser needs to be able to compute simple mathematical operations using the most basic operators (+, -, *, /) without using eval().
	for example: “10 + 20 - 30 + 15 * 5” should return 75.
>2.	Make sure you take care of operator precedence using infix notation.
>3.	Provide a field formatter plugin in a Drupal 8 module that uses this Service.
>4.	Deliver the work as a Drupal 8 site with the custom module in a git repository.

I've accomplished this via a custom module: `ultimate_lexparser`. This module defines a service, `ultimate_lexparser.parser`, that is instantiated via dependency injection in a custom field formatter, `LexParserFieldFormatter`. This field formatter can be applied on any `string` based field, and leverages the `denissimon/formula-parser` package to do the computation.

### Notes/considerations
In considering this challenge, my mind first went towards how I would go about parsing the data. After some reflection, it occurred to me that this certainly must be a wheel already invented, and that if I could grab it as a composer package to integrate with Drupal, much of my work would be complete! Sure enough, the ["denissimon/formula-parser" package](https://github.com/denissimon/formula-parser) appeared to be a perfect fit for the job!

## BONUS: Provide a simple unit test
Assuming usage of the Lagoon environment, the test can be easily executed via the `./test-lex.sh` script, included in the docroot of this repo. The test itself is located in the `ultimate_lexparser` module. The test leverages an `@dataProvider` to easily validate several formulas against the `ultimate_lexparser.parser` service.

## The other bonus challenges
As of right now, I have not yet added the GraphQL or frontend integration bonuses.

---
# ORIGINAL DOCS FOR REFERENCE:

# Composer template for Drupal projects hosted on amazee.io

This project template should provide a kickstart for managing your site
dependencies with [Composer](https://getcomposer.org/). It is based on the [original Drupal Composer Template](https://github.com/drupal-composer/drupal-project), but includes everything necessary to run on amazee.io (either the local development environment or on amazee.io servers.)

test2

## Requirements

* [docker](https://docs.docker.com/install/).
* [pygmy](https://docs.amazee.io/local_docker_development/pygmy.html) `gem install pygmy` (you might need sudo for this depending on your ruby configuration)

## Local environment setup

1. Checkout project repo and confirm the path is in docker's file sharing config - https://docs.docker.com/docker-for-mac/#file-sharing

```
git clone https://github.com/amazeeio/drupal-example.git drupal8-lagoon && cd $_
```

2. Make sure you don't have anything running on port 80 on the host machine (like a web server) then run `pygmy up`

3. Build and start the build images

```
docker-compose up -d
docker-compose exec cli composer install
```

4. Visit the new site @ `http://drupal-example.docker.amazee.io`

* If any steps fail you're safe to rerun from any point,
starting again from the beginning will just reconfirm the changes.

## What does the template do?

When installing the given `composer.json` some tasks are taken care of:

* Drupal will be installed in the `web`-directory.
* Autoloader is implemented to use the generated composer autoloader in `vendor/autoload.php`,
  instead of the one provided by Drupal (`web/vendor/autoload.php`).
* Modules (packages of type `drupal-module`) will be placed in `web/modules/contrib/`
* Themes (packages of type `drupal-theme`) will be placed in `web/themes/contrib/`
* Profiles (packages of type `drupal-profile`) will be placed in `web/profiles/contrib/`
* Creates the `web/sites/default/files`-directory.
* Latest version of drush is installed locally for use at `vendor/bin/drush`.
* Latest version of [Drupal Console](http://www.drupalconsole.com) is installed locally for use at `vendor/bin/drupal`.

## Updating Drupal Core

This project will attempt to keep all of your Drupal Core files up-to-date; the
project [drupal-composer/drupal-scaffold](https://github.com/drupal-composer/drupal-scaffold)
is used to ensure that your scaffold files are updated every time drupal/core is
updated. If you customize any of the "scaffolding" files (commonly .htaccess),
you may need to merge conflicts if any of your modified files are updated in a
new release of Drupal core.

Follow the steps below to update your core files.

1. Run `composer update drupal/core --with-dependencies` to update Drupal Core and its dependencies.
1. Run `git diff` to determine if any of the scaffolding files have changed.
   Review the files for any changes and restore any customizations to
  `.htaccess` or `robots.txt`.
1. Commit everything all together in a single commit, so `web` will remain in
   sync with the `core` when checking out branches or running `git bisect`.
1. In the event that there are non-trivial conflicts in step 2, you may wish
   to perform these steps on a separate branch, and use `git merge` to combine the
   updated core files with your customized files. This facilitates the use
   of a [three-way merge tool such as kdiff3](http://www.gitshah.com/2010/12/how-to-setup-kdiff-as-diff-tool-for-git.html). This setup is not necessary if your changes are simple;
   keeping all of your modifications at the beginning or end of the file is a
   good strategy to keep merges easy.

## FAQ

### Should I commit the contrib modules I download?

Composer recommends **no**. They provide [argumentation against but also
workarounds if a project decides to do it anyway](https://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).

### Should I commit the scaffolding files?

The [drupal-scaffold](https://github.com/drupal-composer/drupal-scaffold) plugin can download the scaffold files (like
index.php, update.php, …) to the web/ directory of your project. If you have not customized those files you could choose
to not check them into your version control system (e.g. git). If that is the case for your project it might be
convenient to automatically run the drupal-scaffold plugin after every install or update of your project. You can
achieve that by registering `@drupal-scaffold` as a post-install and post-update command in your composer.json:

```json
"scripts": {
    "drupal-scaffold": "DrupalComposer\\DrupalScaffold\\Plugin::scaffold",
    "post-install-cmd": [
        "@drupal-scaffold",
        "..."
    ],
    "post-update-cmd": [
        "@drupal-scaffold",
        "..."
    ]
},
```
### How can I apply patches to downloaded modules?

If you need to apply patches (depending on the project being modified, a pull
request is often a better solution), you can do so with the
[composer-patches](https://github.com/cweagans/composer-patches) plugin.

To add a patch to drupal module foobar insert the patches section in the extra
section of composer.json:
```json
"extra": {
    "patches": {
        "drupal/foobar": {
            "Patch description": "URL to patch"
        }
    }
}
```

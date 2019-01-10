# My Site Name

> Drupal 8 / Lightning codebase for [site URL]

Badges can go here from generated [README.md](README.md).

## About This Repository

This project was generated using the [Terminus Build Tools Plugin](https://github.com/pantheon-systems/terminus-build-tools-plugin) from [Urban's Pantheon Lightning Upstream](https://github.com/UI-Research/lightning-drops-8-composer), and extends the [Acquia Lightning](https://github.com/acquia/lightning) Drupal 8 distribution with basic settings common to Urban Websites. Continuous Integration is handled with [CircleCI](https://circleci.com/gh/UI-Research/urban-500cities), which is configured in the `.circlci` directory.


## Lando-based Development
This project requires [Lando](https://docs.devwithlando.io/) for local site-building and development. We are using an overridden appserver configuration that extends the default Pantheon appserver with node, which is helpful for Lando-based theme builds and development (when applicable). See [.lando.yml](.lando.yml) `tooling` block for a list of lando commands.

### First time Quick Start

`lando start` will create containers and setup the local environoment. This will take a few minutes the first time, as images are downloaded and started.

`lando quickstart` will install all dependencies, scaffold the site, and compile the theme. This may take a few minutes to complete.

`lando pull --code=none` will pull in a database and files from Pantheon. Make sure you are setup for machine token access. See lando and/or Pantheon docs if you aren't sure.

Assuming the above completed with no error messages, you should be able to view the site in a browser or run `lando drush` or `lando drupal` commands from the `web` directory. Should "just work."

### Troubleshooting

##### Problem
Lando uses a custom Hypervisor (eg: Docker for Mac), which can slow down for a number of reasons.

##### Try
> Restarting Docker can solve random performance issues.

##### Problem
Sometimes containers don't fully start, which causes drush commands not to work (drush never gets installed). 

##### Try
> Restart the containers with `lando restart`. Strangely, `lando start` (even after you just ran it) can also work.

> If that doesn't work, rebuild the container with`lando rebuild -y`. This is not destructive, you will not lose data or uploaded files.

> If nothing works, see below.

##### Problem
Nothing is working right, I lost track, Help me.

##### Try
> Destroy your local environment with `lando destroy`. This IS DESTRUCTIVE. You will lose your database. Synced files are not impacted. Solr indexes and all other services and data are destroyed. You will need to `lando pull` after you are back up and running.

> Go nuclear: If it seems like nothing is working, Docker may be the problem. Uninstall it (you will lose all volumes and data), restart your computer, and reinstall it again. Do some Googling and/or talk to somebody before doing this unless you really do know what you are doing. It's only necessary in extreme cases.

## Theme Development

IMPORTANT: We recommend that you run all theme installation and npm commands OUTSIDE of Lando. Not only is this faster, 
but it allows Pattern Lab to serve pages and reload automatically. 

Check out the [Theme documentation](web/themes/custom/particle/README.md) for full setup and dev instructions. See [package.json](web/themes/custom/particle/package.json) for Particle's npm commands.


### Running Node locally 
Make sure you have Node and npm installed.

You'll need Node >= 8 to properly compile the theme.

You should use [NVM](https://github.com/creationix/nvm) to manage different node versions.
In most cases, your working session should begin with setting the correct node version and installing the requirements:

`node -v` to determine your current node version.

If you haven't installed with nvm recently, run:
`nvm install 8`

Run this each time.
`nvm use 8`

Run as needed. Good idea when changing versions or if you aren't getting new dependencies after switching versions.
`npm cache clean`


## Site Building workflow

Your day to day workflow should look something like this (ymmv):

From project root:
`lando start`
`lando composer update`

From web root (`cd web`):
`lando drush updatedb -y`

Do some site building and coding and stuff for today (in your feature branch of course).

From web root:
`lando drush cex` (to export any config changes)

From project root:
`lando composer update` is only necessary if you have updated composer.json.

## Adding new modules
Use composer to include contributed modules in this project as described [Here](https://www.drupal.org/docs/develop/using-composer/using-composer-to-manage-drupal-site-dependencies#managing-contributed), but with our lando prefix:

`lando composer require drupal/<some_module>:<version>`

or 

```bash
lando composer require 'drupal/token:^1.5'
lando composer require 'drupal/simple_fb_connect:~3.0'
lando composer require 'drupal/ctools:3.0.0-alpha26'
lando composer require 'drupal/token:1.x-dev'
```

## Updating Lightning 
Drupal core updates are handled by the Lightning distro. We adhere to [Lightning's release schedule](https://github.com/acquia/lightning/releases), and you can generally follow the Update Steps provided for the new release, with some differences. Those documents assume you are using drush 9, amd you aren't on this project (Pantheon does not support just yet). So we'll need [Drupal Console](https://docs.drupalconsole.com/en/commands/available-commands.html) for some commands, and we'll use the drush 8 syntax. Usually, the update will look similar to the following:

```bash
lando composer self-update
lando composer require acquia/lightning:~3.2.3 --no-update
lando composer update

lando drush cr
lando drush updatedb -y
lando drupal update:lightning
```

_IMPORTANT: Use release-specific versions of these commands to apply core/distro updates._

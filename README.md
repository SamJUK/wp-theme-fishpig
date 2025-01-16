# SamJUK/wp-theme-fishpig

[![Supported Fishpig Versions](https://img.shields.io/badge/Supported&nbsp;Fishpig&nbsp;Versions-2.x&nbsp;|&nbsp;3.x-orange.svg?logo=magento)](https://github.com/SamJUK/wp-theme-fishpig)
[![GitHub Release](https://img.shields.io/github/v/release/SamJUK/wp-theme-fishpig?label=Latest%20Release&logo=github)](https://github.com/SamJUK/wp-theme-fishpig/releases)
[![Watcher Workflow Status](https://github.com/samjuk/wp-theme-fishpig/actions/workflows/watcher.yml/badge.svg?)](https://github.com/SamJUK/wp-theme-fishpig/actions/workflows/watcher.yml)

The purpose of this repository is to build the FishPig wordpress theme from source. Generate a composer manifest and publish the package to Packagist.

Allowing for the Fishpig theme to be integrated as a dependency when managing Wordpress by Composer.

## Usage

The packages are available on Packagist. Make sure to specific the same version tag, as your Fishpig M2 module.

Pinning the theme version, as well as the Magento module is recommended for compatibility.

```sh
composer require samjuk/wp-theme-fishpig:3.31.9
```

## Versioning

This theme package tracks the upstream Magento 2 module versions 1 to 1. To help simplify, which version your need to install.

This means some releases, will have no changes over previous versions. The releases have a `update.flag` file is a hash for the theme content, which will indicate if the content has changed between versions. (This hash is also included in the commit message).

You can compare changes between the tagged versions, either by CLI, or via the Github Website.

### Github
When viewing the diff on Github, make sure to use the [double period comparative method](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-comparing-branches-in-pull-requests#three-dot-and-two-dot-git-diff-comparisons). The URL format is:
```
https://github.com/SamJUK/wp-theme-fishpig/compare/$OLD_VERSION..$NEW_VERSION
```


For Example, to compare 3.0.1 against the 3.1.1 release.

[https://github.com/SamJUK/wp-theme-fishpig/compare/3.0.1..3.1.1](https://github.com/SamJUK/wp-theme-fishpig/compare/3.0.1..3.1.1)


### Git CLI
```sh
git init
git remote add origin git@github.com:SamJUK/wp-theme-fishpig.git
git fetch origin tag 3.0.1 3.1.1 --no-tags
git fetch origin tag 3.1.1 --no-tags
git diff 3.0.1 3.1.1
```

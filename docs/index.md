---
nav_order: 1
title: Quickstart
---

[![codestyle](https://github.com/media-code/workspace/actions/workflows/codestyle.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/codestyle.yml)
[![tests](https://github.com/media-code/workspace/actions/workflows/tests.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/tests.yml)
[![coverage](https://img.shields.io/codecov/c/github/media-code/workspace?token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace)
[![core coverage](https://img.shields.io/codecov/c/github/media-code/workspace-core?label=core%20coverage&token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace-core)

Effortlessly keep linters, fixers, static analysis, CI workflows, editor integrations and more in sync across all your teams & projects.

## Quickstart

```bash
composer require gedachtegoed/workspace --dev
```

Then run the install command to set up Workspace's configs in your project:

```bash
php artisan workspace:install
```

This command will install all configured Integrations in [workspace-integrations.php](https://github.com/media-code/workspace/blob/main/config/workspace-integrations.php). These are all configurable by publishing the config file. You may remove or override any implementation with your own on a per-project basis. Read more about overriding Integrations [here](media-code/github.io/workspace/digging-deeper/#overriding-integrations).

{: .note }

> Workspace ships with a handfull carefully crafted Integrations.
>
> If you'd like to roll out your own Portable Workspace to share between projects and teams refer to [this section](media-code.github.io/workspace/portable-workspaces) of the documentation

## Updating

Whenever Integrations change upstream you can easily sync it with your project.

```bash
php artisan workspace:update
```

This command will update Workspace itself (by a minor version only) and update the Integration's _composer_ and _npm_ dependencies before rebuilding all your integration configs.

_Please note that while Workspace's internal Integrations are carefully selected, they are higly opinionated. We do encourage you to write your own **Portable Workspace**._

<!-- Move to Portable Workspace section -->
<!-- When you use the `update` command with your own _Portable Workspace_ you'll have to manually update your _Portable Workspace_ package. The update command only takes care of upgrading your Integration's composer & npm dependencies before rebuilding the config files. -->

## Integrate your IDE

Workspace uses [Duster](https://github.com/tighten/duster) under the hood to facilitate linting and fixing of your code. But to use it you need to manually trigger the command before you commit your code or let CI handle it for you. To bridge the gap you can integrate you IDE so all _Workspace Integrations_ are seamlessly applied when you type.

```bash
php artisan workspace:integrate
```

You'll be prompted for your editor of choice (`vscode` or `phpstorm`). If your team uses both editors simply select them both.

This will install some configuration to your `.vscode` or `.idea` directory respectively.

In case of `vscode` after running the `workspace:integrate` command you'll need to install vscode's recommended extentions. If the prompt doesn't appear; Open the command pallette [CMD + Shift + P] and select 'Show Recommended Extensions' and install them.

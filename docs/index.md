[![codestyle](https://github.com/media-code/workspace/actions/workflows/codestyle.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/codestyle.yml)
[![tests](https://github.com/media-code/workspace/actions/workflows/tests.yml/badge.svg)](https://github.com/media-code/workspace/actions/workflows/tests.yml)
[![coverage](https://img.shields.io/codecov/c/github/media-code/workspace?token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace)
[![core coverage](https://img.shields.io/codecov/c/github/media-code/workspace-core?label=core%20coverage&token=ON4MTY8C1B&color=45%2C190%2C65)](https://codecov.io/gh/media-code/workspace-core)

A extendible workspace configurator for Laravel to effortlessly keep linters, fixers, static analysis, CI workflows, editor integrations and more in sync across all your teams & projects.

## Installation

```bash
composer require gedachtegoed/workspace --dev
```

Then run the install command to set up Workspace's configs in your project:

```bash
php artisan workspace:install
```


{: .note }
> Workspace ships with a handfull carefully selected Integrations.
>
> If you'd like to roll out your own Portable Workspace to share between projects and teams refer to [this section](media-code/github.io/workspace/portable-workspaces) of the documentation

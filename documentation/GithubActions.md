# Automated Deployment
By Matthew Getgen

This document is a guide on the current Github Actions in place, how they work, and what they do for us.

## Table of Contents

1. [TL;DR](#tl;dr)
1. [Release Please](#release-please)
    1. [SemVer](#semantic-versioning)
    1. [Conventional Commits](#conventional-commits)
    1. [Release Pull Request](#release-pull-request)
1. [Build and Deploy](#build-and-deploy)

## TL;DR

To work with the github actions, simply push/merge your changes to the master branch, wait for **Release Please** to create a release Pull Request, and then in the GitHub UI click `Squash Merge` (if you don't see squash merge, click the arrow next to it and choose it). Then, go to the Actions tab and wait for the Build and Deploy action to finish running. Update the staging or production image tag version the same way you have been doing, except follow the `semver` numbering created by **Release Please**

## Release Please

[Release Please](https://github.com/googleapis/release-please-action) is a github action developed by Google that will automatically create a release based on commits made to a specific branch in your repo. By parsing commit messages, it will also build an entry for the `CHANGELOG.md` file and bump the semantic version number (known as [semver](https://semver.org/)). **Release Please** will try to determine changes made, as well as the types of changes made based on commit messages. The ideal way to describe a commit for **Release Please** is to follow [conventional commit message](https://www.conventionalcommits.org/) patterns to inform the type of change the commit is.

### [Semantic Versioning](https://semver.org/)

The semver versioning system has 3 numbers:
- Major (1.0.0)
- Minor (0.1.0)
- Patch (0.0.1)

### [Conventional Commits](https://www.conventionalcommits.org/)

Release Please focuses on 3 major conventional commit messages to bump the semver numbers:
- `fix:` which describes **Bug Fixes**, and bumps the **patch** semver.
- `feat:` which describes **Features**, and bumps the **minor** semver.
- `feat!:`, `fix!:`, or any conventional commit prefix ending with a `!`, which describes **Breaking Changes**, and bumps the **major** semver.

Not following **Conventional Commits** won't break **Release Please**, but it will just be guessing, so it's best to follow this strategy.

If squash merging a PR, the PR name should also follow the conventional commit message.

### Release Pull Request

After pushing a commit to the `master` branch on our repo, **Release Please** will run and create a pull request, assuming the commit message had a prefix of `feat:` or `fix:`, or if the commit message doesn't follow **conventional commits**. Commit messages that start with `chore:` (another conventional commit type), for example, won't create a release.

If you don't want to commit the release, you don't have to. If you keep the commit created by **Release Please** around, it will just get updated by newer commits.

**Release Please** will append the changes to the `CHANGELOG.md` file, as well bump the version number inside of `.release-please-manifest.json`, and will tag the git commit with the version number.

When you have decided that you want to merge this release, go to the Pull Request page on GitHub and find the green `Squash Merge` button at the bottom of the page. If you don't see `Squash Merge` button, hit the arrow key next to the button and select `Squash Merge`. Then merge that PR. This will automatically kick off the next action.

## Build and Deploy

**Build and Deploy** is a github action that will automatically build a docker image based on the `Dockerfile` at the root of the repo. It will also tag the image with the latest **semver**. After that, it will deploy it to dockerhub to be downloaded and run by kubernetes for staging or production.

**Build and Deploy** will only run when a new tag is pushed to the `master` branch, which only happens when a **release please** PR is merged (unless done manually).

The dockerhub repository that it pushes to is `mattgetgen/sb-php`. The tags will be `latest` and the version made by **release please** (i.e. `1.0.0`). The repo it uses is a private repo, and requires permissions to push to, as well as pull from. I have supplied a PAT from my dockerhub account, and given read/write permissions for this github action, as well as read permissions for both the staging and production kubernetes environment.

From there, you can update the image that kubernetes uses and point at the new version.


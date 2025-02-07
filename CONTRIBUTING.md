# Contributing

Thanks that you want to contribute to our project.

Checkout the section [Report a bug](#report-a-bug) if you found a bug, or you only have a question, or a feature request.

## Table of Contents
* [Code of conduct](#code-of-conduct)
* [Setting up a development environment](#setting-up-a-development-environment)
* [Workflow](#workflow)
  * [Report a bug](#report-a-bug)
  * [Implementation](#implementation)
  * [Testing](#testing)
  * [Submit changes](#submit-changes)
* [Styleguide](#styleguide)

## Code of conduct

Everyone how want to contribute to this project including the owners must hold our [Code of Conduct](CODE_OF_CONDUCT.md).

## Setting up a development environment

Before implementing a feature or fixing a bug or security issue you need to set up a development environment.
The easiest way is to install [vagrant](https://www.vagrantup.com/) on your machine and create a `Homestead.yml` in the
project folder for the fully set up laravel development environment. Afterwards run the homestead virtual machine by
executing `vagrant up` in the project folder. This will provide a fully set up virtual machine where the application
can be executed. Also, you need to set up a ldap server. A corresponding tutorial can be found on
[this](https://github.com/THM-Health/PILOS/wiki/Installing-OpenLDAP) wiki page. For testing functionality of the
application which requires a running BigBlueButton server you may need to install a server on your development machine.
Checkout the installation guide in the [readme](README.md) for additional steps needed to finish the setup. Instead of
running `npm run production` you must run in the dev environment `npm run dev`. For the development you can use any
editor of your choice but please do not check in any configuration files for your editor. In this case you may want to
extend the `.gitignore` with yours editor config files.

## Workflow

If you have a question, found a bug, or a feature is missing please report this by creating an issue in this repository.
In case when you have a solution for the feature or bug you can fork this repository and implement the solution in a
corresponding branch, since we are working by the [GitHub flow](https://guides.github.com/introduction/flow/). If
everything is ok, after a review your implementation will be merged in the main branch of the repository.

### Report a bug
Before reporting a new issue, please checkout the existing open and already **closed** issues. May be there is already a
solution for your question. If there is no appropriate issue, you can open a new one. If it is only a question you may
open an empty formless issue. In case of a feature request, or a bug report you must use the corresponding template.
Please fill out everything you can so that other can understand your problem and implement a solution or give an answer
as fast as possible without any additional discussions.

### Implementation
In case if you have a solution for a bug, or you want to implement a new feature please fork this repository, create a
new branch, implement the solution by following the [Styleguide](#styleguide) and afterwards create a pull request to
this repository. Please also don't forget to update the [Changelog](CHANGELOG.md) under the section `Unreleased`. After
creating a pull request fulfill the checklist in the template. Only if everything done and the PR is linked to an
existing issue, the pull request will be checked by a maintainer of this repository.

### Testing
A new development shouldn't decrease the code testing coverage. Everything in the application must be covered by
appropriate unit and feature tests, depending on the case. In case of bugfixes a test, that fails in the appropriate
case should be implemented, to make regression tests possible for further changes in the application. For the backend the
api can be tested by using feature tests and other functions just with unit tests. For more information about tests
checkout the [Laravel testing guides](https://laravel.com/docs/7.x/testing). The frontend implemented in vue, and the
tests gets executed by the test framework mocha. The api responses can be stubbed by using the
[moxios](https://github.com/axios/moxios) framework. In case of api changes, the stubbed responses also should be
changed. For more information about writing tests for the frontend, consider the vue test utils
[documentation](https://vue-test-utils.vuejs.org/) and the
[Vue Testing Handbook](https://lmiller1990.github.io/vue-testing-handbook/#what-is-this-guide).

### Submit changes
After implementing the new feature or bugfix you must create a new pull request to the original repository by using the
corresponding pull request template. If all checks by the ci passed and all tasks in the pull request done, a maintainer
of the repository will check the PR and may make some comments on your PR. If everything fixed or there were no problems
at all, then the PR will be merged into the main branch of this repository.

## Styleguide
The backend uses the php framework Laravel and therefore it follows the
[Laravel coding style guide](https://laravel.com/docs/7.x/contributions#coding-style). To apply the code style to your
implemented code you can run the command `composer run fix-cs`.
The frontend style gets checked by eslint. The style can be fixed by running the command `npm run fix-cs`. For best
practices checkout the [vue style guide](https://vuejs.org/v2/style-guide/).

Additionally, to the style guides the following things should apply to the changes:
* Short and meaningful method, attribute and class names
* Short and not complex methods
* Make things dry and reuse where possible (e.g. mixins or components in the frontend)
* Errors should be handled (in the frontend at least with the `Base.error()` method)
* Permissions should be checked where necessary in frontend and backend
* Validations for requests must exist in the backend
* Translation of static language at least in English
* Don't change already committed database migrations
* Change only database seeds if they are considered, to be executed multiple times
* Define application settings where possible, instead of just hardcode things or creating environment variables
* Don't implement multiple issue solutions in one branch
* Use short and meaningful branch names with the issue number and a short description in the imperative mood
* Commit messages should also use the imperative mood and be as short as possible (checkout also [this](https://chris.beams.io/posts/git-commit/#limit-50) blogpost)

Security Annotations
====================

[![Build Status](https://github.com/nepada/security-annotations/workflows/CI/badge.svg)](https://github.com/nepada/security-annotations/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/nepada/security-annotations/badge.svg?branch=master)](https://coveralls.io/github/nepada/security-annotations?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/security-annotations.svg)](https://packagist.org/packages/nepada/security-annotations)
[![Latest stable](https://img.shields.io/packagist/v/nepada/security-annotations.svg)](https://packagist.org/packages/nepada/security-annotations)


Installation
------------

Via Composer:

```sh
$ composer require nepada/security-annotations
```

Register the extension in `config.neon`:

```yaml
extensions:
    securityAnnotations: Nepada\Bridges\SecurityAnnotationsDI\SecurityAnnotationsExtension
```

For parsing phpdoc annotation this package relies on [doctrine/annotations](https://packagist.org/packages/doctrine/annotations) for parsing annotations. It's up to you to choose and set up the integration, the recommended package to do the job is [nettrine/annotations](https://packagist.org/packages/nettrine/annotations).

**Note: using phpdoc annotations is deprecated and will be removed in next major release. Migrate all your annotations to native PHP8 attributes and set `enableDoctrineAnnotations: false` in your config.** 


UsagecheckReq
-----

This package builds on top of the standard access authorization of Nette components, namely `Nette\Application\UI\Component::checkRequirements()` method.
This method is called before invoking any of component/presenter signal handlers, and before presenter `startup`, `action<>` and `render<>` methods.

With this package you can specify the access rules via attributes on any of the mentioned methods, or on presenter class.
To enable this feature simple use `SecurityAnnotations` trait in any presenter or component and make sure `RequirementsChecker` service gets injected via `injectRequirementsChecker()` - with default Nette configuration this should work on presenters out of the box, but you need to take care of components, e.g. by enabling inject calls.

**Example:**
```php
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\Annotations\LoggedIn;
use Nepada\SecurityAnnotations\Annotations\Role;

/**
 * To access this presenter the user must be logged in.
 */
 #[LoggedIn]
class SecuredPresenter extends Nette\Application\UI\Presenter
{

    use Nepada\SecurityAnnotations\SecurityAnnotations;

    #[Role("admin", "superadmin")]
    public function actionForAdmins(): void
    {
        // Only users with role admin or superadmin are allowed here.
    }

     #[Allowed(resource: "world", privilege: "destroy")]
    public function handleDestroyWorld(): void
    {
        // Only users with specific permission are allowed to call this signal.
    }

}
```

The attributes and rules they enforce are completely customizable (see below), however the default setup comes with three predefined rules:

- **LoggedIn** - checks whether the user is logged in.
- **Role(["admin", "superadmin"])** - checks whether the user has at least one of the specified roles.
  If you use `Nette\Security\Permission` as your authorizator, then role inheritance is taken into account, i.e. users that have at least one role that inherits from at least one of the specified roles are allowed as well.
- **Allowed(resource: "world", privilege: "destroy")** - checks whether the user has at least one role that is granted the specified privilege on the specified resource.


### Securing components

Properly securing components is a tricky business, take a look at the following example:

```php
use Nepada\SecurityAnnotations\Annotations\LoggedIn;

class SecuredPresenter extends Nette\Application\UI\Presenter
{

    use Nepada\SecurityAnnotations\SecurityAnnotations;

    #[LoggedIn]
    public function actionDefault(): void
    {
        // ...
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addSubmit('Do something dangerous');
        $form->onSuccess[] = function (Nette\Application\UI\Form $form): void {
            // ...
        };
        return $form;
    }

}
```

Securing presenter `action<>` (or `render<>`) methods is not sufficient! All it takes is a one general route in your router, e.g. a very common `Route('<presenter>/<action>')`, and anyone may successfully submit the form by sending POST request to `/secured/foo` URL.

You should always check user's permissions when creating the component. To make your life easier there is `SecuredComponents` trait that calls the standard `Nette\Application\UI\Component::checkRequirements()` method before calling the component factory. Combining it with `SecurityAnnotations` it allows you to control access to components via attributes on `createComponent<>` methods.


### Customizing access validators

- You can disable the default set of validators by `enableDefaultValidators: false`.
- You can also define your own validators, i.e. services implementing `Nepada\SecurityAnnotations\AccessValidators\AccessValidator` interface in `validators` configuration section.

```yaml
securityAnnotations:
    enableDefaultValidators: false # disable default set of validators
    validators:
        - MyRoleAccessValidator # define validator by class name
        - @fooAccessValidator # define validator by service reference
        
services:
    fooAccessValidator: FooAccessValidator(%fooParameter%)
```

#### How do access validators work?

Every access validator implements `Nepada\SecurityAnnotations\AccessValidators\AccessValidator` interface. The access validator specifies which attribute type it supports via its public API.

When checking the requirements PHP attributes and all annotations parsed using `doctrine/annotations` are passed one by one to associated access validator for inspection. Based on the attribute value the validator decides either to deny access (throws `Nette\Application\BadRequestException`), or grant access (no exception is thrown).

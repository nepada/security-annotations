Security Annotations
====================

[![Build Status](https://travis-ci.org/nepada/security-annotations.svg?branch=master)](https://travis-ci.org/nepada/security-annotations)
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


Usage
-----

This package builds on top of the standard access authorization of Nette components, namely `Nette\Application\UI\Component::checkRequirements()` method.
This method is called before invoking any of component/presenter signal handlers, and before presenter `startup`, `action<>` and `render<>` methods.

With this package you can specify the access rules via annotations on any of the mentioned methods, or on presenter class.
To enable this feature simple use `SecurityAnnotations` trait in any presenter or component.

**Example:**
```php
/**
 * @loggedIn
 * To access this presenter the user must be logged in.
 */
class SecuredPresenter extends Nette\Application\UI\Presenter
{

    use Nepada\SecurityAnnotations\SecurityAnnotations;

    /**
     * @role(admin, superadmin)
     */
    public function actionForAdmins(): void
    {
        // Only users with role admin or superadmin are allowed here.
    }

    /**
     * @allowed(resource=world, privilege=destroy)
     */
    public function handleDestroyWorld(): void
    {
        // Only users with specific permission are allowed to call this signal.
    }

}
```

The annotations and rules they enforce are completely customizable (see below), however the default setup comes with three predefined rules:

- **@loggedIn** - checks whether the user is logged in.
- **@role(admin, superadmin)** - checks whether the user has at least one of the specified roles.
  If you use `Nette\Security\Permission` as your authorizator, then role inheritance is taken into account, i.e. users that have at least one role that inherits from at least one of the specified roles are allowed as well.
- **@allowed(resource=world, privilege=destroy)** - checks whether the user has at least one role that is granted the specified privilege on the specified resource.


### Securing components

Properly securing components is a tricky business, take a look at the following example:

```php
class SecuredPresenter extends Nette\Application\UI\Presenter
{

    use Nepada\SecurityAnnotations\SecurityAnnotations;

    /**
     * @loggedIn
     */
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

You should always check user's permissions when creating the component. To make your life easier there is `SecuredComponents` trait that calls the standard `Nette\Application\UI\Component::checkRequirements()` method before calling the component factory. Combining it with `SecurityAnnotations` it allows you to control access to components via annotations on `createComponent<>` methods.


### Customizing access validators

All access validators are configured in `validators` section of the extension configuration:
- To disable any of the default rules set the validator to `false`.
- You can also define your own validators, i.e. services implementing `Nepada\SecurityAnnotations\AccessValidators\AccessValidator` interface.

```yaml
securityAnnotations:
    validators:
        loggedIn: false # disable predefined @loggedIn annotation validator
        role: MyRoleAccessValidator # redefine validator for @role annotation
        foo: @fooAccessValidator # use named service as validator for @foo annotation
        
services:
    fooAccessValidator: FooAccessValidator(%fooParameter%)
```

#### How do access validators work?

Annotations are parsed by `Nette\Reflection\AnnotationsParser` and their value is one by one passed for inspection to the specific validator.
It's the responsibility of the validator to check whether or not the annotation value is of expected type, e.g. the default `@loggedIn` validator can handle only boolean values.
Based on the annotation value the validator decides either to deny access (throws `Nette\Application\BadRequestException`), or grant access (no exception is thrown).

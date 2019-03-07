# Http Authentication Router PHP

[![Build Status](https://travis-ci.com/hunomina/http-auth-router-php.svg?branch=master)](https://travis-ci.com/hunomina/http-auth-router-php)

Description : Implementation of Http Router classes for PHP7.1 or higher with authentication.

This library is mainly composed of 8 classes.

### AuthRouter

The *[AuthRouter](https://github.com/hunomina/http-auth-router-php/blob/master/src/AuthRouter.php)* class extends from the *[hunomina\Routing\Router]()* class.

Therefore it can handle request by calling the *request(string $method, string $url)* method which must return a route action response.

> Read [this documentation](https://github.com/hunomina/http-router-php/blob/master/README.md) in order to understand how the base routing system works.

This class can be instantiated by passing a route file, an *[AuthenticationCheckerInterface](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/Checker/AuthenticationCheckerInterface.php)* instance and a type (json, yaml... extend if you want to add new ones).

The route file syntax is identical to the *[hunomina\Routing\Router](https://github.com/hunomina/http-router-php/blob/master/src/Routing/Router.php)* route file syntax.

> Examples [here](https://github.com/hunomina/http-auth-router-php/tree/master/tests/conf).

### AuthenticationCheckerInterface

The *[AuthenticationCheckerInterface](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/Checker/AuthenticationCheckerInterface.php)* has three methods :

- *bool* isAuthenticated() : Return true if an user is connected to your application. You can whatever you want : cookies, sessions, headers...
- *?UserInterface* getAuthenticatedUser() : Return the authenticated user for session, cookie, headers...
- *bool* checkAuthorization(*?UserInterface* $user, *SecurityContext* $securityContext, *string* $method, *string* $url) : Return true if a specific user can access to a route (method + url) based on a security context

In order for the AuthRouter to work with your application, you have to create a class that implements the *[AuthenticationCheckerInterface](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/Checker/AuthenticationCheckerInterface.php)* interface and her methods.

You absolutely can add some methods if needed.

### UserInterface

The *[UserInterface](https://github.com/hunomina/http-auth-router-php/blob/master/src/UserInterface.php)* must be implemented by all user entities that will log into your application.

This interface has only one method :

- *string[]* getRoles() : Return a string list of all the user's security context roles

### SecurityContext

Allow to define role based rules for specific routes.

This class loads the security context from a configuration file, check his validity and instantiate multiple *[Role](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Role.php)* and *[Rule](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Rule.php)* objects according to the configuration file.

You can then use the security context object in your AuthenticationChecker application instance to check users rights.

This class is abstract and has two methods that you have to implement :

- *void* load(): Once the configuration file has been validated, loads it and instantiates *[Role](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Role.php)* and *[Rule](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Rule.php)* object to feet the configuration
- *bool* isSecurityContextValid() : Return true if the configuration file syntax is valid

This package comes with two built-in SecurityContext implementation :

- *[YamlSecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/YamlSecurityContext.php)*
- *[JsonSecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/JsonSecurityContext.php)*

If you want to add a new SecurityConext type, you'll have to create two classes :

- A class extending the AuthRouter class in order to be able to use the new SecurityContext type
- A class extending the SecurityContext class in order to create a new configuration file parser

### Role

A *[Role](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Role.php)* is just a name with child roles.

This class has two properties :

- *string* $_name : The name of the role
- *Role[]* $_children : A list of child roles. Allow to define a role hierarchy

And one method :

- *bool* contains(*Role* $role) : Return true if the Role instance is $role or has $role as a child

### Rule

A *[Rule](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Rule.php)* has 3 properties :

- *string* $_path : Pseudo regular expression matching the urls the rule must apply to. "Pseudo regular expression" means that it's a regular expression without "/" escaped
- *string[]* $_methods : List of http methods. Restrict the http methods on which the rule can match
- *Role[]* $_roles : List of Role who can access to the $_path with the $methods http methods

Those three properties are passed to the constructor. Make sure all of them are valid (e.g. valid pseudo regular expression for $_path property)

### YamlSecurityContext

The *[YamlSecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/YamlSecurityContext.php)* is a built-in *[SecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/SecurityContext.php)* implementation.

It allow you to use Yaml configuration file for your application AuthRouter SecurityContext. If you use this SecurityContext class you must follow this simple configuration file syntax :

- Have a root element `security`.
- Have two children element : `auth_firewall` and `role_hierarchy`.
- `auth_firewall` contains the list of all the application firewall rules.
- `role_hierarchy` contains the list of all the application roles and their hierarchy.

Each rule is composed of :

- `path` : Pseudo regular expression for the *[Rule](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/Rule.php)* object.
- `roles` : A string or a string list of the roles the rule uses. If empty, nothing can validate the rule.
- `methods` : An optional list of http methods to use to match the rule

Each role is composed of :

- A key : The role name
- A value : A string or a string list of child roles

> Example [here](https://github.com/hunomina/http-auth-router-php/blob/master/tests/conf/security.yml) (Yaml)

### JsonSecurityContext

The *[JsonSecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/JsonSecurityContext.php)* is a built-in *[SecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/SecurityContext.php)* implementation too.

It allow you to use Json configuration file for your application AuthRouter SecurityContext.

For this SecurityContext class, the configuration file syntax is the same as the *[YamlSecurityContext](https://github.com/hunomina/http-auth-router-php/blob/master/src/Firewall/SecurityContext/YamlSecurityContext.php)*.

> Example [here](https://github.com/hunomina/http-auth-router-php/blob/master/tests/conf/security.json) (Json)

### Examples

For more examples, check those [here](https://github.com/hunomina/http-auth-router-php/blob/master/tests/)
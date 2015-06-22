#Dynactive SSO Integration Kit

##Overview

Dynactive SSO uses SAML 2.0 for it's SSO authentication piece.  
The specification for SAML can be read here: [https://wiki.oasis-open.org/security/FrontPage](https://wiki.oasis-open.org/security/FrontPage)

We are using the HTTP POST Binding and specifically IDP initiated login.

This particular library wraps around the SimpleSAML library: [https://simplesamlphp.org/](https://simplesamlphp.org/)

Dynactive provisions quite a bit of data for students such as their leaderboards,
grade data, the courses they are assigned to, etc.  This provisioning can take 
a few seconds to complete and propagate across our cloud infrastructure.

Because of this, we have separated our user creation and user authentication into two distinct API calls.
User creation should occur at the time a user is created in your system.  For example when a new employee
account is created, or when a customer purchases a course you are selling.  If you have an existing customer / user 
base you will need to call our create API for each one of these pre-existing users before they have access.

## Requirements

* [php composer](https://getcomposer.org/)
* [php 5.3.3+](http://php.net/) -
* [php mcrypt](http://php.net/manual/en/book.mcrypt.php)
* [simplesamlphp/saml2 1.5.0+](https://github.com/simplesamlphp/saml2)
* [symfony/validator 2.2+](https://github.com/symfony/Validator)
* [monolog/monolog 1.12.0+](https://github.com/Seldaek/monolog.git)
* [doctrine/annotations v1.2.3](https://github.com/doctrine/annotations.git)
* [doctrine/cache 1.4.0](https://github.com/doctrine/cache.git)

## Install
The easiest way to get setup is to use composer, you can run a composer install to verify your system requirements
and install all of the dependency libraries. Links are provided above if you don't want to go this route.
If you want to run this library stand-alone then do the following in order to get the right doctrine autoloader working:
`composer install --dev`

If you are including this library through composer add this to your composer.json file:
```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Dynactive-Software/DynactiveSSOIntegrationKit.git"
    }
]
```

And add the following to your require
```
"require": {
    // other libraries
    ,"dynactive-software/dynactiveSSOIntegrationKit": "v2.1+"
}
```

If you do not have composer installed you can get it here: [https://getcomposer.org/](https://getcomposer.org/)

## Running Samples
If you want to see how the process flows right away you can just do the following
if you have php 5.4+ you can run the CLI server and the current example should work for you.  
go to the examples directory and run the following:
`php -S localhost:8000 ./`

Then you if you navigate your browser to [http://localhost:8000/sample-idp-send.php](http://localhost:8000/sample-idp-send.php)
you should be logged in as a student.

The example for creating a user can be found in the examples/sample-idp-create-user.php file.

The example for authenticating a user can be found in examples/idp-send.php

## Implementation

To get started you will need to generate an RSA private key and an X.509 cert file.  The X.509 cert needs to be uploaded on the Dynactive LMS
site.  We can do that if you email us the file, or we can walk you through instructions on how to do it if you plan on implementing SSO for
multiple sub clients.  This would be the case if you are a content publisher who plans on having your content used by your clients.

The easiest way to do this is through openssl
`openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout privateKey.key -out certificate.crt`

Now you can edit the config.php file and change the key and cert names/locations to where you have stored your private key and the dynactive public key
You will also need to send us your certificate.crt or have us walk you through how to upload that in the LMS system.

Change the domain name to the domain hosting the sample and the clientLMS to the URL suffix you've been given.

Then edit the specific user information in sample-idp-create-user.php and run that file
`php sample-idp-create-user.php`

The response you should get back is (with the ssoUid you sent):
`status=OK,ssoUid=55848541446a9`

You should save off your ssoUid for the user from the response
`echo $response->getSsoUid();`

From here you need to set the ssoUid for the user in sample-idp-send.php
`$user->setSSOUID("55848541446a9");`

Now run the server again
`php -S localhost:8000 ./`

Now if you open up a browser to [http://localhost:8000/sample-idp-send.php](http://localhost:8000/sample-idp-send.php)
it should authenticate you to the site

If there are any errors they are sent to http://localhost:8000/sample-dip-error-handler.php
You can set this URL to be whatever you want as explained in config.php

From there bundling this sample into your application should be fairly straightforward.  
If you have purchased one of our support level packages, please contact us for help or assistance if you have questions.
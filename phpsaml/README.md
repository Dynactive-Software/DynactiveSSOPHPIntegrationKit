Dynactive SSO uses SAML 2.0 for it's SSO authentication piece.  
The specification for SAML can be read here: https://wiki.oasis-open.org/security/FrontPage

We are using the HTTP POST Binding and specifically IDP initiated login.

This particular library wraps around the SimpleSAML library: https://simplesamlphp.org/

If you want to see how the process flows right away you can just do the following if you have php 5.4+ you can run the CLI server and the current example should work for you.  In the project directory run the following:
php -S localhost:8000 .

Then you if you navigate your browser to http://localhost:8000/ you should be logged in as a student.

Editing the index.php file you can test out logging in as a student, instructor, or client admin.


Steps on implementation with this library for your own SSO.

To get started you will need to generate an RSA private key and an X.509 cert file.  The X.509 cert needs to be uploaded on the dynactive LMS
site.  We can do that if you email us the file, or we can walk you through instructions on how to do it if you plan on implementing SSO for
multiple sub clients.  This would be the case if you are a content publisher who plans on having your content used by your clients.

The easiest way to do this is through openssl
openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout privateKey.key -out certificate.crt

Now you can edit the index.php file and change the key and cert names/locations to where you have stored your private key and the dynactive public key
You will also need to send us your certificate.crt or have us walk you through how to upload that in the LMS system.

Then edit the specific user information following the comments as needed to clarify their purpose and format.

Change the domain name to the domain hosting the sample and the clientLMS to the URL suffix you've been given.

Once you've done that by navigating in a browser to index.php you should be able to verify that you can connect your SSO.

From there bundling this sample into your application should be fairly straightforward.  Any questions you can contact us for help or assistance.

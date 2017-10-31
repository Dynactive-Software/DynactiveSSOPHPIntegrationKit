# Dynactive SSO Overview

Dynactive SSO uses SAML 2.0 for it's SSO authentication piece.
The specification for SAML can be read here: https://wiki.oasis-open.org/security/FrontPage

We are using the HTTP POST Binding and specifically IDP initiated login.

Dynactive provisions quite a bit of data for students such as their leaderboards, grade data, the courses they are assigned to, etc. This provisioning can take a few seconds to complete and propagate across our cloud infrastructure.

Because of this, we have separated our user creation and user authentication into two distinct API calls. User creation should occur at the time a user is created in your system. For example when a new employee account is created, or when a customer purchases a course you are selling. If you have an existing customer / user base you will need to call our create API for each one of these pre-existing users before they have access.

The actual process flow is the following:

## User Signup
1. User signs up for Identity Provider's Service
2. Identity Provider generates unique SSO ID for User
3. Identity Provider generates User Create request and digitally signs the inner assertion with their private key.
4. Identity Provider encrypts the user attributes using Dynactive's public cert
5. Identity Provider digitally signs the overall request with their private key.
6. Identity Provider sends an HTTP Post request containing the request to the Dynactive LMS
7. Dynactive LMS receives User Create Request and verifies the request signature using identity provider's public certificate
8. Dynactive LMS decrypts the user attributes using Dynactive's private key.
9. Dynactive LMS verifies the user attribute signature using identity provider's public certificate
10. Dynactive LMS validates the user attributes and provisions the user in their system.  A response is sent back to Identity Provider saying the user is created and being provisioned.
11. Identity Provider receives JSON response with Status of Error or Ok

## User Authentication
1. User logs into Identity Provider's Service
2. User tells Identity Provider they want to access the Dynactive LMS (link, button, other action)
3. Identity Provider retrieves User information including unique SSO ID.
4. Identity Provider generates User Authentication request and digitally signs the inner assertion with their private key.
4. Identity Provider encrypts the user attributes using Dynactive's public cert
5. Identity Provider digitally signs the overall request with their private key.
6. Identity Provider sends a form with a button or has the form auto-submit an HTTP Post containing the request to the Dynactive LMS
7. Dynactive LMS receives Authenticate Request and verifies the request signature using identity provider's public certificate
8. Dynactive LMS decrypts the user attributes using Dynactive's private key.
9. Dynactive LMS verifies the user attribute signature using identity provider's public certificate
10. Dynactive LMS validates the user exists and updates any attributes about the user.  A session is established with the client machine and the client is redirected back to the responseURL that the Identity Provider sent.
11. Identity Provider parses the status, message, and redirect parameters that Dynactive LMS sends and does any post-authentication logic it desires.  
12. Identity Provider sends client an HTTP redirect to the redirect parameter given by Dynactive LMS
12. User is now logged in.

## User Authentication Error Messages
Any errors with the user attributes or information contained in the request, the client is redirected back to the redirectOnError location (if provided, responseURL if one is not provided). 

If the request cannot be decrypted or the digital signature is invalid you will see an error screen on the Dynactive LMS.

## User Logout

When the user logs out they are sent to the redirectOnLogoff parameter sent as part of the user assertion attributes.

## Explanation of SSO Attributes

Attribute        | Explanation                                                                                                                                         |
---------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------|
firstName        | The first name of the user, 1 character minimum                                                                                                     |
lastName         | The last name of the user, 1 character minimum                                                                                                      |
email            | The email address of the user                                                                                                                       |
displayName      | The name the user wants displayed to other users                                                                                                    | 
role             | The type of student. Valid values are STUDENT, INSTRUCTOR, CLASS_INSTRUCTOR, CLIENT_ADMIN                                                                             |
courseAccessList | The courses that the user has access to. Found on the Deploy course section that CLIENT_ADMIN users have access to                                  |
goDirectToCourse | Course identifier that the user should be sent to directly upon finishing authentication.                                                           |
responseUrl      | The URL location that Dynactive LMS should send the user and it's SSO response to for post-authentication processing on the Identity Provider's site|
redirectOnLogin  | Where to send the user to when they have logged in, if you want to send them to a particular location on the LMS                                    |
userUid          | Unique user identifier, 1 character minimum.                                                                                                        |
redirectOnError  | The location to send error responses to if there is an error.  If this value is empty, errors are sent to the responseUrl field                     |
redirectOnLogout  | The location to send the students to when they logout.                     |
classAccessList  | The classes that the user has access to.  Found on the Class Management area that CLIENT_ADMIN and INSTRUCTOR users have access to.                 |


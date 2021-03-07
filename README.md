# PHP Satellite Backend App
Satellite Backend App is an open source backend that can be deployed to any infrastructure that can run PHP hosting.

## Features
- Easy to use syntax
- Regular Expression support
- Error handling through PHP Exceptions
- Parameter validation through PHP function signature
- Support of GET, POST, PUT, DELETE, PATCH, HEAD and OPTIONS
- API's & User Authentication using JWT Tokens.
- Data can be stored & fetched in Collection-Document Manner.


## Getting Started
### Requirements
- Apache Server
- MySQL
- PHP 5.3 and above

### Installation

**.env**
```
DB_HOST=localhost
DB_DATABASE="database-name"
DB_USERNAME="username"
DB_PASSWORD="password"
JWT_KEY="a-32-character-string"
JWT_ISSUER="url-of-jwt-token-issuing-website"
```

**Generating Client Secret Key**
- Step 1 : Generate Client Id & Password.
```
+ POST /api/auth/client/generatecredential.php
Accept: application/json
Content-Type: application/json

{
    "password": "1234567" 
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Id & password Generated. Insert generated credentials to client table in mysql database.",
    "password": "1234567",
    "generated-credentials": {
        "client_id": "<generated-client-id>",
        "password": "<hashed-password>"
    }
}
```
- Step 2 : Add Client Id, Password & AppName in client in mysql database manually.
```
INSERT INTO `client` (`id`, `app_name`, `client_id`, `password`) VALUES (NULL, 'my_new_app', '<generated-client-id>', '<generated-hashed-password>');
```
- Step 3 : Generate Secret key.
```
+ POST /api/auth/client/getsecretkey.php
Accept: application/json
Content-Type: application/json

{
    "client_id": "<generated-client-id>",
    "password": "1234567"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Copy this token & use as SECRET_KEY in client application.",
    "token": "<Client-Secret-Key>"
}
```


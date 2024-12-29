# PHP Satellite Backend App
Satellite Backend App is an open source Backend as a Service(Baas) that can be deployed to any infrastructure that can run PHP.

## Features
- Error handling through PHP Exceptions
- Support of GET, POST, PUT, DELETE, PATCH, HEAD and OPTIONS
- User Register & Login support.
- API's & User Authentication using JWT Tokens.
- In Cloud Datastore the data is stored in Document Collection way.
- Security Rules allow you to control access to your stored data.


## Getting Started
### Requirements
- Apache Server
- MySQL
- PHP 5.3 and above

### Installation

- Step 1 : Put all the files in public_html or htdocs folder of your hosting.
- Step 2 : Import the database schema into mysql phpmyadmin. Database Schema is in **database_shema/v2**.
- Step 3 : Add your database and jwt credentials into .env file.
  
**.env**
```
DB_HOST=localhost
DB_DATABASE="database-name"
DB_USERNAME="username"
DB_PASSWORD="password"
JWT_KEY="a-32-character-string"
JWT_ISSUER="url-of-jwt-token-issuing-website"
ALLOW_ORIGIN=<client-url>
ALLOW_API_TESTING=False
ALLOW_DIRECT_URL_ACCESS=False
```
To test the api using POSTMAN or Isomania set 'ALLOW_API_TESTING=True' in .env file.
Set ALLOW_ORIGIN=* to allow access to all urls.

- Step 4 : Add this to the .htaccess file in directory. (optional)
```
Options -Indexes
```
It disables the directory browsing.


## Using Authentication
You can use Satellite Authentication to let your users authenticate using their email addresses and passwords, and to manage your app's password-based accounts.

### Create Account with "user" role
```
+ POST /api/auth/user/create_user.php
Accept: application/json
Content-Type: application/json

{
    "firstname":"Abhsihek",
    "lastname":"Yadav",
    "email" : "a6h15hek@outlook.com",
    "password":"mypass123"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "User Created."
}
```

### Sign in a user with an email address and password
```
+ POST /api/auth/user/login.php
Accept: application/json
Content-Type: application/json

{
    "firstname":"Abhsihek",
    "lastname":"Yadav",
    "email" : "a6h15hek@outlook.com",
    "password":"mypass123"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Login successfully.",
    "token": "<user-login-token>"
}
```

### Getting Access to everything 
You need to change the role from "user" to "admin" manually into user table in mysql database. Admin role gives superuser ability to user. Useful for administrator of website.
Default Admin Credentials.
```
"email" : "admin@email.com",
"password":"admin"
```

**Change Password**
Change admin password to strong password. use the admin login token as header in authorization to change password.
```
+ POST /api/auth/user/changepassword.php
Accept: application/json
Content-Type: application/json

{
    "password" : "admin2",
    "new_password" : "admin"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "password changed."
}
```


**Generating Client Secret Key**
Only use admin account.
- Step 1 : Create client.
```
+ POST /api/auth/client/createclient.php
Accept: application/json
Content-Type: application/json
{
    "app_name" : "mynewapp2"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Client Created."
}
```
- Step 2 : Generate Secret key.
```
+ POST /api/auth/client/getsecretkey.php
Accept: application/json
Content-Type: application/json
{
    "client_id": "CLI604f4d152c404KCspc"
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
Now you can use this secret key as authorization header to get access to sattelite API's. Store secret key in .env file for frontend application.

**Delete Client**
```
+ POST /api/auth/client/deleteclient.php
Accept: application/json
Content-Type: application/json
{
    "client_id": "CLI604f4d152c404KCspc"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Client deleted.",
}
```

## Using Cloud Datastore
Cloud Datastore is a cloud-hosted, NoSQL database that your iOS, Android, and web apps can access directly. Cloud datastore's NoSQL data model, you store data in documents that contain fields mapping to values. These documents are stored in collections, which are containers for your documents that you can use to organize your data. Documents support many different data types, from simple strings and numbers, to complex, nested objects. You can also create subcollections within documents and build hierarchical data structures that scale as your database grows.  

### Add Data
Cloud Datastore stores data in Documents, which are stored in Collections. Cloud Firestore creates collections and documents implicitly the first time you add data to the document. You do not need to explicitly create collections or documents.
```
+ POST /api/datastore/document/add.php
Accept: application/json
Content-Type: application/json

{
    "collection_name":"users",
    "data_object":{
        "first": "Abhishek",
        "middle": "Mohan",
        "last": "Yadav",
        "born": 2000
    }
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Document added.",
    "document_name": "DOC60449eb3296eenblcS"
}
```

### Read Data
```
+ GET /api/datastore/document/get_documents.php?collection=users&end=10&start=0
Accept: application/json
Content-Type: application/json
```

*Successful Response:*
```
{
    "success": true,
    "documents": [
        {
            "dataObject": {
                "born": 2000,
                "last": "Yadav",
                "first": "Abhishek",
                "middle": "Mohan"
            },
            "document_name": "DOC60449eb3296eenblcS",
            "updatedAt": "2021-03-07 15:06:51",
            "createdAt": "2021-03-07 15:06:51"
        }
    ]
}
```

### Set Data
To create or overwrite a single document, use the set() method:
```
+ POST /api/datastore/document/set.php
Accept: application/json
Content-Type: application/json

{
    "collection_name": "cities",
    "document_name": "LA",
    "data_object" : {
        "name": "Los Angeles",
        "state": "CA",
        "country": "USA"
    }
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Document successfully written!"
}
```
If the document does not exist, it will be created. If the document does exist, its contents will be overwritten with the newly provided data, unless you specify that the data should be merged into the existing document, as follows:

**Update a document or field or nested field with merge**
```
+ POST /api/datastore/document/set.php
Accept: application/json
Content-Type: application/json

{
    "collection_name": "cities",
    "document_name": "LA",
    "merge": true,
    "data_object": {
        "capital": true
    }
}
```

### Update elements in an array
**Add element**
```
+ POST /api/datastore/document/updatearray.php?action=add
Accept: application/json
Content-Type: application/json

{
    "collection_name": "cities",
    "document_name": "DC",
    "arrayfield": "regions",
    "arrayelement": "greater_virginia"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Element added successfully."
}
```
**Remove element**
```
+ POST /api/datastore/document/updatearray.php?action=remove
Accept: application/json
Content-Type: application/json

{
    "collection_name": "cities",
    "document_name": "DC",
    "arrayfield": "regions",
    "arrayelement": "east_coast"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Element removed successfully."
}
```

### Get a document
The following example shows how to retrieve the contents of a single document.
```
+ GET /api/datastore/document/get.php?collection=cities&document=LA
Accept: application/json
Content-Type: application/json
```

*Successful Response:*
```
{
    "success": true,
    "data": {
        "dataObject": {
            "name": "Los Angeles",
            "state": "CA",
            "country": "USA"
        },
        "updatedAt": "2021-03-07 15:54:44",
        "createdAt": "2021-03-07 15:22:26"
    }
}
```

### Delete documents
The following examples demonstrate how to delete documents, fields, and collections.
```
+ POST /api/datastore/document/delete.php
Accept: application/json
Content-Type: application/json

{
    "collection_name" : "cities",
    "document_name" : "LA"
}
```

*Successful Response:*
```
{
    "success": true,
    "data": "Document deleted."
}
```


## Understanding Security Rules
Satellite Security Rules allow you to control access to your stored data. 
Collection read and write permission can be set to public or private.

**private** 
- user that needs to access the data should be the same user that created the data.
- ownership of data will be of user who created that data.
  
**public** 
- any user or client can access the data.
- ownership of data will be of user who created that data but can be accessed by any user or client.

### Defining Security rules while creating collection
```
+ POST /api/datastore/collection/create.php
Accept: application/json
Content-Type: application/json

{
    "collection_name":"posts",
    "write" : "private",
    "read" : "public"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Collection Created."
}
```

### Getting Access to everything 
You need to change the role from "user" to "admin" manually into user table in mysql database. Admin role gives superuser ability to user. Useful for administrator of website.
```
UPDATE `users` SET `role` = 'admin' WHERE `users`.`user_id` = <your-user-id>;
``` 

### Updating Security rules for already created collection.

```
+ POST /api/datastore/collection/update_permission.php
Accept: application/json
Content-Type: application/json

{
    "collection_name" : "users",
    "write" : "private",
    "read" : "private"
}
```

*Successful Response:*
```
{
    "success": true,
    "message": "Collection permission Updated"
}
```

## PHP Satellite Dashboard
Use this Dashboard to manage the your server app.<br/>
Git Repository : https://github.com/a6h15hek/satellite-dashboard

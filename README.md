# IoC Checker

![Screenshot of admin panel](https://raw.githubusercontent.com/CSIRT-SK/ioc-server/master/screenshot.png)

## Deployment
Web server needs to have HTTPS with client certificate verification enabled to work properly.

Create database and import `indicators.sql` into it. This will set up the tables as well as default IOC types.

Create file `dbInfo.php` in `/api/models`. This file needs to define 5 constants:

* `HOST` - database server host (localhost if DB is on the same server as webserver)
* `USER` - database user to perform the api operations
* `PASS` - password for the user
* `DATABASE` - name of the database (where the indicators.sql was imported)
* `ADMIN_CERT` - common name of admin client certificate. This certificate will be required to access administrative API calls

Sample `dbInfo.php`:
```php
<?php
define ('HOST', 'localhost');
define ('USER', 'user');
define ('PASS', 'pass');
define ('DATABASE', 'database');
define ('ADMIN_CERT', 'admin cert');
?>
```

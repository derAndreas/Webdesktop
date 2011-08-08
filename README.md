WebDesktop
==========

## What is the WebDesktop ? ##

The Webdesktop is an online desktop written in [ExtJs4](http://www.sencha.com) as frontend and [Zend Framework](http://framework.zend.com) as backend.
It is used as a sample project to use the MVC pattern from ExtJs4 and ZendFramework.


## HowTo Install? ##

* Clone from the GitHub Project page at https://github.com/derAndreas/Webdesktop
* Download ExtJs4 and Zend Framework
    * Extract ExtJs4 in the folder lib/Ext/
    * Extract Zend Framework in the folder src/library/Zend/
* Edit the configuration files
* Create a MySQL Database and import the db structure
* Secure your configuration files! (see Security)
* Set an Admin password with genAdmin.php


## Edit the configuration files ##

There are two main configuration files from the Zend Framework Backend

1. src/application/configs/application.ini - the main application configuration
2. src/application/configs/webdesktop.ini - webdesktop specific paths

In file application.ini change the following config options:

    resources.db.password_salt
    resources.db.params.dbname
    resources.db.params.username
    resources.db.params.password
    resources.db.params.hostname

In file webdesktop.ini you have to modify every path related line


# Security #
The downloadable files from the GitHub Repo have the ./src folder, containing all Zend Framework relevant stuff in the main httpdocs folder.
For security reasons your should move this folder to a secure loction. This could be any path, that is not accessable via http (or other).
Then you have to change the application path in the index.php

    // Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/src/application'));

An other way is to change the permissons on the ./src folder with chmod.

**IMPORTANT:** If you do not secure your configuration files, your credentials to database, the password salt and so on are readable for everyone!!!


### More Informations ###
*todo*



#### First setup/Forgot Admin password ####
If you setup the script or lost your admin password you cannot change the password in the database directly, because a salted MD5 hash as password is used.
To generate a salted MD5 hash for your password, run the script "genAdmin.php" and use phpMyAdmin or simple shell mysql and update
the column uu_passwort in the user_users table for your user.
After a fresh installation it will look something like this:
    
    USE <DATABSE_NAME>;
    UPDATE `user_users` SET `uu_passwort` = '<PUT THE HASH HERE>' WHERE `user_users`.`uu_id` = 2;


#### Additional informations ####
* This is my first public project ever
* This is my first ExtJS4 project
* This is my first Zend Framework project
* This is my first GitHub, no wait, this is my first version controlled project
* This is my first MarkDown file

Please consider this and be patient - although critics and improvments very welcome!

Regards,
Andreas

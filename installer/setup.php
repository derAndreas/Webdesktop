<?php

// Configuration

/**
 * Define the path to your the "src" folder. 
 * This is where the application is, default in ./src
 * Relative from index.php
 */
$pathToSrcFolder = 'src/';

/**
 * Database credentials
 */
$dbname = '';
$dbuser = '';
$dbpass = '';
$dbhost = '127.0.0.1';

// End Configuration

$passwordSalt = sha1(uniqid());
$firstAdminPass = uniqid();

IF(end(explode(DIRECTORY_SEPARATOR, getcwd())) !== 'installer') {
    exit('Please run this script in the installer folder with php ./setup.php');
}


$appConfigTpl = file_get_contents(sprintf('../%s/application/configs/application.ini.tpl', $pathToSrcFolder));
$wdConfigTpl = file_get_contents(sprintf('../%s/application/configs/webdesktop.ini.tpl', $pathToSrcFolder));

$webdesktopTables = array('modules', 'style_themes', 'style_wallpapers', 'user_acl_actions', 'user_acl_modulecontroller', 'user_acl_roles', 'user_acl_role_inherits', 'user_acl_role_member', 'user_acl_rules', 'user_groups', 'user_launchers', 'user_styles', 'user_users');

$db = new PDO(sprintf('mysql:dbname=%s;host=%s', $dbname, $dbhost), $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));

FOREACH($db->query('SHOW TABLES') AS $table) {
    IF(in_array(strtolower($table[0]), $webdesktopTables)) {
        throw new Exception(sprintf('Cannot proceed. Table "%s" does already exists. Please remove Webdesktop relative tables', $table[0]));
    }
}

$db->beginTransaction();
try {
    $db->exec(file_get_contents('webdesktop.sql'));
    $db->query(sprintf('UPDATE user_users SET uu_passwort = "%s" WHERE uu_id = 2', md5($firstAdminPass . $passwordSalt)));
} catch (Exception $e) {
    $db->rollBack();
    exit ($e->getMessage());
} 

// write config files
file_put_contents(
    sprintf('../%s/application/configs/application.ini', $pathToSrcFolder),
    str_replace(
        array('PASSWORD_SALT', '!DBNAME', '!DBUSER', '!DBPASS', '!DBHOST'), 
        array( $passwordSalt, $dbname, $dbuser, $dbpass, $dbhost), 
        $appConfigTpl
    )
);
file_put_contents(sprintf('../%s/application/configs/webdesktop.ini', $pathToSrcFolder), $wdConfigTpl);

// change some permissions
chmod(sprintf("../%s/data/cache", $pathToSrcFolder), 755);
chmod(sprintf("../%s/data/logs", $pathToSrcFolder), 755);

echo <<<END
Ready, you can now login with
Username: admin
Password: $firstAdminPass

Make sure that you have the Zend Framework in folder src/library/Zend. If you have ZF in a differnt location please update the files
 - index.php (where set_include_path() is)
 - src/application/configs/application.ini

Also make sure that you copied ExtJs4 into lib/Ext/

Thanks for testing. Please report any issue.
END;


?>

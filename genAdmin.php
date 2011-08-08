<?php
#ini_set('display_errors', true);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/src/application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

$error = NULL;

IF(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && !empty($_POST['s']) && isset($_POST['p1'])) {

    require_once 'Zend/Config/Ini.php';

    $c = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    $cDb = $c->production->resources->db;

    IF(strlen($_POST['p1']) < 10 || empty ($_POST['p1'])) {
        $error = 'Please select a password with 10 chars.';
    } ELSEIF($_POST['p1'] !== $_POST['p2']) {
        $error = 'Passwords are not equal';
    } ELSEIF(strlen($cDb->password_salt) < 8) {
        $error = 'Please check your password salt in the application.ini. Should be more than 8 chars!';
    } ELSE {

        $hash = md5($_POST['p1'] . $cDb->password_salt);

        echo <<<END
            <div style="border: 1px solid #808080; background-color: lightgreen">
                Password for the User is:$hash
            </div>
END;

    }
}

?>

<html>
    <head>
        <title>Generate Pass for admin user</title>
    </head>
    <body>
        <h2>Generate a password for the admin</h2>
        <p>
            After you entered the admin password, this script will generate a MD5 salted hash string.<br/>
            Put this string in the password column of the user_users table for the admin user.<br/>
            <h3>Example Or use phpMyAdmin.</h3>
            <code>
                UPDATE user_users SET uu_passwort = "&lt;GENERATED-MD5PASSWORDSALT&gt;" WHERE uu_id = &lt;ID_of_ADMIN&gt;
            </code><BR/>
        </p>
        <?php IF(!empty($error)): ?>
            <div style="border: 1px solid #808080; background-color: lightcoral">
                <ul>
                    <li><?php echo $error; ?></li>
                </ul>
            </div>
        <?php ENDIF; ?>
        <form action="genAdmin.php" method="post">
            Password: <input type="password" name="p1" /> <br/>
            Again: <input type="password" name="p2" />
            <input type="submit" name="s" value="Generate" />
        </form>
    </body>
</html>

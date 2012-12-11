<?php
/**
 * Application options.
 *
 * @author Marko Martinović
 */

/* Facebook API */
define('FB_APP_ID', '');
define('FB_APP_SECRET', '');
define('FB_APP_URL', '');

/* Database */
define('DBMS', 'mysql');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');

/* Maximum number of tweets as status comments */
define('APP_TW_COUNT', 10);

/* Application name and version */
define('APP_NAME', 'Facebook Status Twitter Search');
define('APP_VER', '1.00');

/* To invalidate all current logins */
define('LOGIN_HASH', 'aoubHvLUEMMdkOmToR8B');

/* Table names */
define('DB_TABLE_USERS', 'Users');

/* Error reporting to maximum */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);

// Start session
if (!isset($_SESSION))
    session_start();

try {
    /* Setup database connection */
    $db_con =
    new PDO(DBMS.':dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASSWORD,
        array(
            /* Force MySQL PDO driver to use UTF-8 for the connection */
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'',

            /* Set the error reporting to exception */
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
    );

    // Create table
    $db_con->query(
        'CREATE TABLE IF NOT EXISTS '.DB_TABLE_USERS.' (
            user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_login VARCHAR(100) NOT NULL,
            user_password VARCHAR(32) NOT NULL,
            user_access_token TEXT,
            user_status_id TEXT,
            PRIMARY KEY(user_id),
            UNIQUE KEY(user_login)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8, COLLATE utf8_general_ci;'
    );
} catch(PDOException $e){
    // Catch database related errors
    echo $e->getCode().' : '. $e->getMessage().
        ' on the line '. $e->getLine(). ' in the file '.
        $e->getFile(); exit;
}
?>

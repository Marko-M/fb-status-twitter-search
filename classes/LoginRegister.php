<?php
/**
 * Singleton class for handling login and register.
 *
 * @author Marko Martinović
 */

final class LoginRegister {
    // Hash key used to validate existing logins
    private static $hash_key;

    // Template object
    private static $tpl;

    // PDO object
    private static $db_con;

    private function __construct() {}

    /**
     * Check is current login is valid, is user trying to login
     * and is user trying to register.
     *
     * @param string $hash_key Used to validate existing logins
     */
    public static function check($hash_key){
        global $db_con;
        self::$db_con = $db_con;

        global $tpl;
        self::$tpl = $tpl;

        self::$hash_key = $hash_key;

        if(isset($_GET['logout'])) {
            /* If logging out */
            self::logout();
        } else if (isset($_SESSION['user_hash'])){
            /* If already authenticated */
            if(!self::hash_check($_SESSION['user_login'],
                    $_SESSION['user_password'],
                    $_SESSION['user_hash']))
                self::logout();
        }else if (isset($_POST['login_login']) && isset($_POST['login_password'])){
            // If login form authentication.
            $user_login = trim($_POST['login_login']);
            $user_password = trim($_POST['login_password']);

            if($user_login != '' && $user_password != ''){
                /* If provided login data is not blank */
                self::login($user_login, md5($user_password));
            } else {
                if($user_login == ''){
                    Notice::add(_('Name is required to sign in.'));
                }

                if ($user_password == '') {
                    Notice::add(_('Password is required to sign in.'));
                }

                // Reload to avoid browser prompt about resending POST data.
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit;
            }
        }else if (isset($_POST['register_login']) && isset($_POST['register_password'])){
            // If register form authentication.
            $user_login = trim($_POST['register_login']);
            $user_password = trim($_POST['register_password']);

            if($user_login != '' && $user_password != ''){
                /* If provided register data is not blank */
                self::register($user_login, md5($user_password));
            } else {
                if($user_login == ''){
                    Notice::add(_('Name is required to register.'));
                }

                if ($user_password == '') {
                    Notice::add(_('Password is required to register.'));
                }

                // Reload to avoid browser prompt about resending POST data.
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit;
            }
        } else {
            self::$tpl->display('login_register.tpl.php');
            exit;
        }
    }

    /**
     * Handles user login and sets the login $_SESSION variables.
     *
     * @param string $user_login User login name
     * @param string $user_password User password
     */
    private static function login($user_login, $user_password){
        // Are credentials valid?
        $sql = 'SELECT
                    user_id
                FROM '.DB_TABLE_USERS.'
                WHERE user_login = :user_login AND user_password = :user_password';
        $stmt = self::$db_con->prepare($sql);
        $stmt->bindValue(':user_login', $user_login, PDO::PARAM_STR);
        $stmt->bindValue(':user_password', $user_password, PDO::PARAM_STR);
        $stmt->execute();

        $user_id = $stmt->fetchColumn();
        if ($user_id === false){
            // Credentials not valid
            Notice::add(_('Wrong login or password. Please try again.'));

            // Reload to avoid browser prompt about resending POST data.
            header('Location: '.$_SERVER['REQUEST_URI']);
            exit;
        }

        // Credentials valid
        $_SESSION['user_login'] = $user_login;
        $_SESSION['user_password'] = $user_password;
        $_SESSION['user_id'] = $user_id;

        /* We create hash value using md5 on $hashkey, $user_login,
         *  $user_password and $authUserID. */
        $_SESSION['user_hash'] = md5(self::$hash_key . $user_login . $user_password);

        // Reload to avoid browser prompt about resending POST data.
        header('Location: '.$_SERVER['REQUEST_URI']);
        exit;
    }

    /**
     * Handles user registration.
     *
     * @param string $user_login User registration name
     * @param string $user_password User registration password
     */
    private static function register($user_login, $user_password){
        // Is login already taken?
        $sql = 'SELECT
                    user_id
                FROM '.DB_TABLE_USERS.'
                WHERE user_login = :user_login';

        $stmt = self::$db_con->prepare($sql);
        $stmt->bindValue(':user_login', $user_login, PDO::PARAM_STR);
        $stmt->execute();

        $user_id = $stmt->fetchColumn();
        if ($user_id !== false){
            // Name already taken
            Notice::add(_('Name already taken. Please select another one.'));

            // Reload to avoid browser prompt about resending POST data.
            header('Location: '.$_SERVER['REQUEST_URI']);
            exit;
        }

        // Name not taken, insert new user
        $sql = 'INSERT INTO '.DB_TABLE_USERS.' (user_login, user_password)
                VALUES (:user_login, :user_password)';

        $stmt = self::$db_con->prepare($sql);
        $stmt->bindValue(':user_login', $user_login, PDO::PARAM_STR);
        $stmt->bindValue(':user_password', $user_password, PDO::PARAM_STR);
        $stmt->execute();

        Notice::add(_('You have successfully registered.'));

        // Reload to avoid browser prompt about resending POST data.
        header('Location: '.$_SERVER['REQUEST_URI']);
        exit;
    }

    /**
     * Hash check using hash key used to validate existing logins
     * and to invalidate existing logins if neccessary.
     *
     * @param string $user_login User login name
     * @param string $user_password User login password
     * @param string $user_hash User hash key
     */
    private static function hash_check($user_login, $user_password, $user_hash){
        /* We reproduce the hash built by the login() method - if this fails to
         * match the original hash value, the user is immediately logged out */
        if (md5(self::$hash_key . $user_login . $user_password) == $user_hash){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Handles user logout by clearing session.
     */
    private static function logout(){
        $_SESSION = array();
        session_destroy();
        session_regenerate_id();

        header('Location: index.php');
        exit;
    }
}
?>

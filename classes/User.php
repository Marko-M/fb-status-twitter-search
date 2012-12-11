<?php
/**
 * Class for handling regular (non FB connected users).
 *
 * @author Marko Martinović
 */

class User {
    // User's id
    public $user_id;

    // User's data from database
    public $user_data;

    // PDO object
    protected $db_con;

    public function __construct($user_id){
        global $db_con;

        $this->db_con = $db_con;
        $this->user_id = $user_id;

        // Get data from database
        $this->get_data();
    }

    /**
     * Retrieves regular user's data from database.
     *
     * @return array Associative array with data
     */
    public function get_data(){
       $sql =
        'SELECT user_login FROM '.DB_TABLE_USERS.' WHERE user_id = :user_id';

        $stmt=$this->db_con->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Creates login dialog url
     *
     * @return string Login dialog url
     */
    public function get_login_dialog_url(){
        $_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection

        $url =
        'https://graph.facebook.com/oauth/authorize?'.
            http_build_query(
                array(
                    'client_id' => FB_APP_ID,
                    'redirect_uri' => FB_APP_URL,
                    'state' => $_SESSION['state'], // CSRF protection
                    'scope' => 'user_status,publish_actions'
                    )
            );
        return $url;

    }

    /**
     * Retrieves access token from given code.
     *
     * @param string $code OAuth code from $_REQUEST['code']
     * @return string OAuth access token
     */
    public function get_access_token($code){
        $return = array();

        $url =
        'https://graph.facebook.com/oauth/access_token?'.
            http_build_query(
                array(
                    'client_id' => FB_APP_ID,
                    'redirect_uri' => FB_APP_URL,
                    'client_secret' => FB_APP_SECRET,
                    'code' => $code
                    )
            );

        $response = curl_file_get_contents($url);
        parse_str($response, $return);

        if(!isset($return['access_token'])){
            throw new Exception('Unable to retrieve access token.');
        }

        return $return['access_token'];
    }

    /**
     * Stores access token into database.
     *
     * @param string $token OAuth access token
     */
    public function store_access_token($token){
        $sql =
        'UPDATE '.DB_TABLE_USERS.'
            SET user_access_token = :token
        WHERE
            user_id = :user_id';

        $stmt = $this->db_con->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Disconnect from FB by clearing user_access_token and
     * user_status_id from database.
     */
    public function disconnect(){
        $sql =
        'UPDATE '.DB_TABLE_USERS.'
            SET
                user_access_token = NULL,
                user_status_id = NULL
        WHERE
            user_id = :user_id';

        $stmt = $this->db_con->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
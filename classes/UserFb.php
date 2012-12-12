<?php
/**
 * Class for handling FB connected users.
 *
 * @author Marko Martinović
 */

class UserFb extends User{

    /**
     * Retrieves FB user's data from database.
     *
     * @return array Associative array with data
     */
    public function get_data() {
        $sql =
        'SELECT
            user_login,
            user_access_token,
            user_status_id
        FROM '.DB_TABLE_USERS.'
        WHERE user_id = :user_id';

        $stmt=$this->db_con->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $this->user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update current status in database.
     *
     * @param string $new_status_id Id of new FB status
     */
    public function update_status_id($new_status_id){
        $sql =
        'UPDATE '.DB_TABLE_USERS.'
            SET
                user_status_id = :new_status_id
        WHERE
            user_id = :user_id';

        $stmt = $this->db_con->prepare($sql);
        $stmt->bindValue(':new_status_id', $new_status_id, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Retrieves user's current FB status.
     *
     * @return array Associative array with status id and message
     */
    public function get_current_status(){
        $url =
        'https://graph.facebook.com/me?'.
            http_build_query(
                array(
                    'access_token' => $this->user_data['user_access_token'],
                    'fields' => 'feed',
                    'limit' => 1
                )
            );
        $response = curl_file_get_contents($url);

        $decoded_response = json_decode($response);

        if (isset($decoded_response->error->message)) {
            throw new Exception($decoded_response->error->message, 0);
        }

        if(!isset($decoded_response->feed->data[0]->id) ||
            !isset($decoded_response->feed->data[0]->message)){
            throw new Exception('Unable to fetch current status. Facebook might be down.', 1);
        }

        return array(
            'id' => $decoded_response->feed->data[0]->id,
            'message' => $decoded_response->feed->data[0]->message
            );
    }

    /**
     * Posts message as comment to target FB status.
     *
     * @param string $status_id Id of target FB status
     * @param string $message Message to be posted as comment to target FB status
     * @return string FB status new comment id
     */
    public function post_status_comment($status_id, $message){
        $url =
        'https://graph.facebook.com/'.$status_id.'/comments';
        $data =
        http_build_query(
            array(
                'access_token' => $this->user_data['user_access_token'],
                    'message' => $message
                )
        );

        $response = curl_file_get_contents_post($url, $data);

        $decoded_response = json_decode($response);

        if (isset($decoded_response->error->message)) {
            throw new Exception($decoded_response->error->message, 0);
        }

        if(!isset($decoded_response->id)){
            throw new Exception('Unable to post comments to status.', 1);
        }

        return $decoded_response->id;
    }

    /**
     * Search twitter for related tweets using public search API.
     *
     * @param string $what What to search for
     * @return array Array with search results
     */
    public function get_twitter_search($what){
        $url =
        'http://search.twitter.com/search.json?'.
            http_build_query(
                array(
                    'q' => $what,
                    'rpp' => APP_TW_COUNT
                )
            );
        $response = curl_file_get_contents($url);

        $decoded_response = json_decode($response);

        return $decoded_response;
    }
}

?>

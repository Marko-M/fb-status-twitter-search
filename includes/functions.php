<?php
/**
 * Misc functions.
 *
 * @author Marko Martinović
 */

/**
 * Checks to see is user FB connected.
 *
 * @param int $user_id User id
 * @return bool True if user FB connected, false otherwise
 */
function is_fb_connected($user_id){
    global $db_con;

    $sql = 'SELECT
                COUNT(*)
            FROM '.DB_TABLE_USERS.'
            WHERE user_id = :user_id AND user_access_token IS NOT NULL';

    $stmt = $db_con->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $user_id = $stmt->fetchColumn();
    if ($user_id === '1')
        return true;

    return false;
}

/**
 * This wrapper functions exists in order to circumvent PHP’s
 * strict obeying of HTTP error codes.  In this case, Facebook
 * returns error code 400 which PHP obeys and wipes out
 * the response. Taken from Facebook Developer blog.
 *
 * @param string $url Url to GET.
 * @return mixed Result on success and false otherwise
 */
function curl_file_get_contents($url) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($c, CURLOPT_TIMEOUT, 120);
    $contents = curl_exec($c);

    curl_close($c);

    if ($contents)
        return $contents;
    else
        return false;
}

/**
 * Like curl_file_get_contents() but using POST.
 *
 * @param string $url Url to POST
 * @param string $fields Query string
 * @return mixed Result on success and false otherwise
 */
function curl_file_get_contents_post($url, $data) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($c, CURLOPT_TIMEOUT, 120);
    $contents = curl_exec($c);

    curl_close($c);

    if ($contents)
        return $contents;
    else
        return false;
}
?>

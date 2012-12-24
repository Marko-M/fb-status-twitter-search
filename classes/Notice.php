<?php
/**
 * Singleton class for storing notices to be shown on the next screen.
 *
 * @author Marko Martinović
 */

final class Notice {
    private function __construct() {}

    /**
     * Add new notice.
     *
     * @param string $notice Notice message to add
     */
    public static function add($notice){
        $_SESSION['notice'][] = $notice;
    }

    /**
     * Clear all notices.
     *
     * @param string $notice Notice message to add
     */
    public static function clear(){
        unset($_SESSION['notice']);
    }
}
?>
<?php
/**
 * Application entry point.
 *
 * @author Marko Martinović
 */

// Initialize application
require_once('config.php');

// Include Savant template engine
require_once('savant/Savant3.php');

// Include misc functions
require_once('includes/functions.php');

// Include classes
require_once('classes/Notice.php');
require_once('classes/LoginRegister.php');
require_once('classes/User.php');
require_once('classes/UserFb.php');

// Create new Savant object to use Savant templating engine
$tpl =
new Savant3(
    array(
        // Set template path
        'template_path' => 'templates'
        )
    );

try{
    // Check for login/register
    LoginRegister::check(LOGIN_HASH);

    if(is_fb_connected($_SESSION['user_id'])){
        // Create FB connected user
        $user = new UserFb($_SESSION['user_id']);

        if(isset($_GET['disconnect'])) {
            // FB disconnect triggered
            $user->disconnect();

            header('Location: index.php');
            exit;
        }else{
            try {
                $tweets = array();
                $state = '';
                $message = '';

                // Get current status from FB graph API
                $current_status = $user->get_current_status();

                // Did current state changed from the last time?
                if($user->user_data['user_status_id'] != $current_status['id']){
                    // Update current status in database
                    $user->update_status_id($current_status['id']);

                    // Inform user about status change detected.
                    $state = 'Facebook status has changed since your last visit.';

                    // Search twitter for APP_TW_COUNT number of related tweets
                    $tw_search = $user->get_twitter_search($current_status['message']);
                    if(isset($tw_search->results)){
                        // If Twitter search succeeds
                        $tw_count = count($tw_search->results);

                        /* For each related tweet use FB graph API to post tweet text as
                         * status comment.
                         */
                        foreach ($tw_search->results as $result){
                            $tweets[] = $result->text;
                            $user->post_status_comment($current_status['id'], $result->text);
                        }
                    }else{
                        // If twitter search returns error, usually due to complexity
                        if(isset($tw_search->error)){
                            Notice::add(
                                'Your Facebook status is too complex for related tweets search.'
                            );
                        }

                        $tw_count = 0;
                    }

                    $message =
                        sprintf(
                            '%d related Tweets have been posted as '.
                            'comments to your new Facebook status.',
                            $tw_count
                        );
                }  else {
                    /* Status hasn't changed from the last time. Just print a few
                     * messages to inform user.
                     */
                    $state = 'Facebook status hasn\'t changed since your last visit.';
                    $message =
                        sprintf(
                            'After you post new Facebook status, up to %d related '.
                            'Tweets might be posted as comments.',
                            APP_TW_COUNT
                        );
                }
                $tpl->current = $current_status['message'];
            }  catch (Exception $e){
                // Add notice with error message
                Notice::add($e->getMessage());

                if($e->getCode() === 0){
                    //
                    /* Exception code 0 - OAuthException type of FB graph API
                     * error response. This most probably means acces token has
                     * expired, user has changed password or revoked permissions.
                     * We disconnect and reload to obtain new token.
                     */

                    $user->disconnect();

                    header('Location: index.php');
                    exit;
                }else if($e->getCode() === 1){
                    // Exception code 1 - Other FB related error
                    $state = 'Facebook status change unknown.';
                    $message = 'Facebook is probably facing some difficulties.';
                    $tpl->current = 'Unknown';
                }
            }
        }

        $tpl->tweets = $tweets;
        $tpl->state = $state;
        $tpl->message = $message;
        $tpl->user_data = $user->user_data;
        $tpl->display('user_fb.tpl.php');
    }else{
        // Create regular user.
        $user = new User($_SESSION['user_id']);

        /* First compare $_REQUEST['state'] with $_SESSION['state']
         * as CSRF protection.
         */
        if( isset($_REQUEST['state']) && !empty($_REQUEST['state']) &&
            isset($_SESSION['state']) && !empty($_SESSION['state']) &&
            ($_SESSION['state'] === $_REQUEST['state'])){

            if(isset($_REQUEST['code']) && !empty($_REQUEST['code'])){
                /* If $_REQUEST['code'] is set, user has just returned from
                 * Facebook login dialog. We're ready to requrest access token.
                 */
                try {
                    $token = $user->get_access_token($_REQUEST['code']);

                    // Store access token into database.
                    $user->store_access_token($token);

                    // Add notice to be displayed on the next screen.
                    Notice::add('You have successfully connected your account with Facebook.');
                }  catch (Exception $e){
                    // Most probably couldn't get access token

                    // Add notice with error message
                    Notice::add($e->getMessage());
                }

                header('Location: index.php');
                exit;
            }else if(isset($_REQUEST['error']) && !empty ($_REQUEST['error'])){
                /* If $_REQUEST['error'] is set, user has given up on creating
                 * connection.
                 */
                if($_REQUEST['error'] == 'access_denied'){

                    // Add notice to be displayed on the next screen.
                    Notice::add('You have canceled Facebook connection process.');

                    header('Location: index.php');
                    exit;
                }
            }
        }

        // Set FB login dialog url to be used to connect with Facebook.
        $tpl->login_dialog_url = $user->get_login_dialog_url();

        // User data from database.
        $tpl->user_data = $user->user_data;

        // Display regular user's template.
        $tpl->display('user.tpl.php');
    }
} catch(PDOException $e){
    // Catch database related exceptions and exit with error data.
    echo $e->getCode().' : '. $e->getMessage().
        ' on the line '. $e->getLine(). ' in the file '.
        $e->getFile();
    exit;
}
?>
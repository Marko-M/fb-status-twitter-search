<?php
/**
 * Regular user view template.
 *
 * @author Marko Martinović
 */
?>
        <?php include $this->template('top.tpl.php') ?>
            <?php include $this->template('notice.tpl.php') ?>
            <div id="content">
                 <div id="welcome" class="common">
                    Welcome <?php echo htmlspecialchars($this->user_data['user_login']) ?>.
                </div>
                <div class="common">
                    Please connect your account with Facebook to get started.
                </div>
            </div>
            <div id="tools">
                <div id="connect">
                    <a href="<?php echo $this->login_dialog_url ?>">Connect</a>
                </div>
                <div id="logout">
                    <a href="?logout">Logout</a>
                </div>
            </div>
        <?php include $this->template('bottom.tpl.php') ?>

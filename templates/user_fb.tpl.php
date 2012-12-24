<?php
/**
 * Facebook connected user view template.
 *
 * @author Marko Martinović
 */
?>

        <?php include $this->template('top.tpl.php') ?>
            <?php include $this->template('notice.tpl.php') ?>
            <div id="content">
                <div id="welcome" class="common">
                    Welcome <?php echo htmlspecialchars($this->user_data['user_login']) ?>
                </div>

                <div id ="current" class="common">
                    <div class="label">
                        Your Facebook status:
                    </div>
                    <div class="body">
                        <?php echo $this->current ?>
                    </div>
                </div>

                <div id="state" class="common">
                    <?php echo $this->state ?>
                </div>

                <div id="message" class="common">
                    <?php echo $this->message ?>
                </div>

                <?php if(!empty($this->tweets)): ?>
                    <div id ="tweets" class="common">
                        <div class="label">
                            Tweet list:
                        </div>
                        <div class="body">
                            <?php foreach ($this->tweets as $tweet): ?>
                                <div class="tweet">
                                    <?php echo $tweet ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="tools">
                <div id="disconnect">
                    <a href="?disconnect">Disconnect</a>
                </div>
                <div id="logout">
                    <a href="?logout">Logout</a>
                </div>
            </div>
        <?php include $this->template('bottom.tpl.php') ?>

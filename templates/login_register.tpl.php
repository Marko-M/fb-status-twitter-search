        <?php include $this->template("top.tpl.php") ?>
            <div id="content" class="login_register">
                <div id="login">
                    <form method="post" action="" name="login_form" id="login_form">
                        <div id="login_register_wrapper">
                            <div class="section_title form_section">Have an account?</div>
                            <div class="form_section">
                                <label for="user_login">Name:</label>
                                <input type="text" class="text" name="login_login" id="login_login" />
                            </div>
                            <div class="form_section">
                                <label for="user_password">Password:</label>
                                <input type="password" class="text" name="login_password" id="login_password" />
                            </div>
                            <div class="form_section">
                                <input class="button" type="submit" id="login_submit" name="login_submit" value="Login" />
                            </div>
                        </div>
                    </form>
                </div>
                <div id="register">

                    <form method="post" action="" name="register_form" id="register_form" autocomplete="off">
                        <div id="login_register_wrapper">
                            <div class="section_title form_section">Need an account?</div>
                            <div class="form_section">
                                <label for="user_register">Name:</label>
                                <input type="text" class="text" name="register_login" id="register_login" />
                            </div>
                            <div class="form_section">
                                <label for="user_password">Password:</label>
                                <input type="password" class="text" name="register_password" id="register_password" />
                            </div>
                            <div class="form_section">
                                <input class="button" type="submit" id="register_submit" name="register_submit" value="Register" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php include $this->template("bottom.tpl.php") ?>
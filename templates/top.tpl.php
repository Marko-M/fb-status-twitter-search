<?php
/**
 * Top template.
 *
 * @author Marko Martinović
 */
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <title><?php echo APP_NAME; ?></title>

        <link type="text/css" rel="stylesheet" href="css/fb-status-twitter-search.css" />
    </head>

    <body>
        <div id="wrapper">
            <div id="title" class="common"><?php echo APP_NAME; ?></div>
            <?php include $this->template('notice.tpl.php') ?>
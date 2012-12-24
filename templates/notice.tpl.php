<?php
/**
 * Information notices template.
 *
 * @author Marko Martinović
 */
?>

<?php if (isset($_SESSION['notice'])): ?>
    <div id="notices" class="common">

        <?php foreach ($_SESSION['notice'] as $notice): ?>
        <div class="notice">
            <?php echo htmlspecialchars($notice) ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['notice']); ?>
    <?php unset($notice); ?>
<?php endif; ?>
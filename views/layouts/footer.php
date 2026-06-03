<footer class="footer">
    <p>&copy; <?= date('Y') ?> Online Computer Shop. Web Technologies Project.</p>
</footer>

<?php
    $appBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($appBase === '/' || $appBase === '\\') {
        $appBase = '';
    }
?>
<script>window.APP_BASE_URL = '<?= e($appBase) ?>';</script>
<script src="<?= e($appBase) ?>/public/js/validation.js"></script>
</body>
</html>

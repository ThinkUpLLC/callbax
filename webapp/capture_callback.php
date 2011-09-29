<?php
require_once 'init.php';

$controller = new CaptureCallbackController();
echo $controller->control(true);

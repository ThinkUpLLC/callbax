<?php
require_once 'init.php';

$controller = new CaptureFollowerCountsController();
echo $controller->control(true);

<?php
require_once 'init.php';

$controller = new ProcessCallbackController(true);
echo $controller->control(true);

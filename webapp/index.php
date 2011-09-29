<?php
require_once 'init.php';

$controller = new InstallationListController();
echo $controller->control();

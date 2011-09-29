<?php
include 'init.tests.php';

require_once ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';
require_once ROOT_PATH.'webapp/_lib/extlib/simpletest/web_tester.php';
require_once ROOT_PATH.'webapp/_lib/extlib/simpletest/mock_objects.php';

$all_tests = new TestSuite('All tests');
$all_tests->add(new TestOfCallbackMySQLDAO());
$all_tests->add(new TestOfInstallationMySQLDAO());
$all_tests->add(new TestOfUserMySQLDAO());

$all_tests->add(new TestOfCaptureCallbackController());
$all_tests->add(new TestOfProcessCallbackController());
$all_tests->add(new TestOfInstallationListController());

$tr = new TextReporter();
$all_tests->run( $tr );

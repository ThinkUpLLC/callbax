<?php
include 'init.tests.php';

require_once ROOT_PATH.'webapp/extlibs/simpletest/autorun.php';
require_once ROOT_PATH.'webapp/extlibs/simpletest/web_tester.php';
require_once ROOT_PATH.'webapp/extlibs/simpletest/mock_objects.php';

$all_tests = new TestSuite('All tests');
$all_tests->add(new TestOfCallbackMySQLDAO());
$all_tests->add(new TestOfInstallationMySQLDAO());
$all_tests->add(new TestOfUserMySQLDAO());
$all_tests->add(new TestOfCaptureCallbackController());
$all_tests->add(new TestOfProcessCallbackController());
$all_tests->add(new TestOfInstallationListController());

$tr = new TextReporter();
$all_tests->run( $tr );

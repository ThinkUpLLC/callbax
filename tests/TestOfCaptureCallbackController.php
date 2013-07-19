<?php
require_once dirname(__FILE__).'/init.tests.php';
//require_once ROOT_PATH.'webapp/config.inc.php';
require_once ROOT_PATH.'webapp/extlibs/simpletest/autorun.php';

class TestOfCaptureCallbackController extends CallbaxUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new CaptureCallbackController(true);
        $this->assertNotNull($controller);
        $this->assertIsA($controller, 'CaptureCallbackController');
    }

    public function testControl() {
        //no referer, no version
        $controller = new CaptureCallbackController(true);
        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 0);

        //referer but no version
        $_SERVER['HTTP_REFERER'] = 'http://example.com/';
        $_GET['v'] = null;

        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 0);

        //version but no referer
        $_SERVER['HTTP_REFERER'] = 'http://example.com/';
        $_GET['v'] = null;

        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 0);

        //referer and version
        $_SERVER['HTTP_REFERER'] = 'http://example.com/';
        $_GET['v'] = '0.15';
        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 1);
    }
}
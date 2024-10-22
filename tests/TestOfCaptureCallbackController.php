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

        // referer with no host but folder path
        // http:///tup/?u=rod-foursquare%40arsecandle.org&n=foursquare
        $_SERVER['HTTP_REFERER'] = 'http:///tup/?u=rod-foursquare%40arsecandle.org&n=foursquare';
        $_GET['v'] = '0.15';
        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 1);

        // referer with no host
        // http:///?u=MatthijsP&n=twitter
        $_SERVER['HTTP_REFERER'] = 'http:///?u=MatthijsP&n=twitter';
        $_GET['v'] = '0.15';
        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 1);

        //referer from thinkup.com
        // https://www.thinkup.com/user/MitchWagner/
        $_SERVER['HTTP_REFERER'] = 'https://www.thinkup.com/user/MitchWagner/';
        $_GET['v'] = '0.15';
        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 1);

        //referer from thinkup.com
        // https://anildash.thinkup.com
        $_SERVER['HTTP_REFERER'] = 'https://anildash.thinkup.com';
        $_GET['v'] = '0.15';
        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 1);
    }
}
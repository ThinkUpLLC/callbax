<?php
require_once dirname(__FILE__).'/init.tests.php';
require_once ROOT_PATH.'webapp/config.inc.php';
require_once ROOT_PATH.'webapp/extlibs/simpletest/autorun.php';

class TestOfCallbackMySQLDAO extends CallbaxUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new CallbackMySQLDAO();
        $this->assertNotNull($dao);
    }

    public function testInsert() {
        $dao = new CallbackMySQLDAO();
        $result = $dao->insert('my referrer 1', '0.14');
        $this->assertEqual(1, $result);

        $result = $dao->insert('my referrer 2', '0.14');
        $this->assertEqual(2, $result);

        $result = $dao->insert('my referrer 3', '0.14');
        $this->assertEqual(3, $result);
    }

    public function testDelete() {
        $dao = new CallbackMySQLDAO();
        $result = $dao->insert('my referrer', '0.14');
        $this->assertEqual(1, $result);

        $result = $dao->delete(1);
        $this->assertEqual(1, $result);
    }


    public function testGet() {
        $dao = new CallbackMySQLDAO();
        $result = $dao->insert('my referrer 1', '0.14');
        $this->assertEqual(1, $result);

        $result = $dao->insert('my referrer 2', '0.14');
        $this->assertEqual(2, $result);

        $result = $dao->insert('my referrer 3', '0.14');
        $this->assertEqual(3, $result);

        $results = $dao->get(5);
        $this->assertIsA($results, 'array');
        $this->assertEqual(count($results), 3);
        $this->assertIsA($results[0], 'Callback');

        $results = $dao->get(2);
        $this->assertEqual(count($results), 2);
    }
}
<?php
require_once dirname(__FILE__).'/init.tests.php';
require_once ROOT_PATH.'webapp/config.inc.php';
require_once ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';

class TestOfUserMySQLDAO extends CallbaxUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new UserMySQLDAO();
        $this->assertNotNull($dao);
    }

    public function testInsert() {
        $dao = new UserMySQLDAO();
        //$install_id, $service, $username
        $result = $dao->insert('http://example.com', 'testuser', 'twitter');
        $this->assertEqual(1, $result);

        $result = $dao->insert('http://example2.com/', 'testuser2', 'facebook');
        $this->assertEqual(2, $result);

        //same-same, no new insert
        $result = $dao->insert('http://example.com', 'testuser2', 'facebook');
        $this->assertFalse($result);
    }

    public function testOfGetServiceTotals() {
        $dao = new UserMySQLDAO();
        $result = $dao->getServiceTotals();
        $this->assertEqual(count($result), 0);

        $i = 45;
        while ($i > 0) {
            $service_randomizer = $i%7;
            switch ($service_randomizer) {
                case 1:
                    $service = 'Twitter';
                    break;
                case 2:
                    $service = 'Google+';
                    break;
                case 3:
                    $service = 'Facebook Page';
                    break;
                default:
                    $service = 'Facebook';
                    break;
            }
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'version'=>'0.15', 'last_seen'=>(($i==45)?'2000-12-31 01:00:00':'-'.$i.'h')));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>$service,
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }
        $result = $dao->getServiceTotals();
        $this->assertEqual(count($result), 4);
        $this->assertEqual($result[0]['service'], 'Facebook');
        $this->assertEqual($result[0]['count'], '24');
        $this->assertEqual($result[0]['percentage'], '53');

        $this->assertEqual($result[1]['service'], 'Facebook Page');
        $this->assertEqual($result[1]['count'], '7');
        $this->assertEqual($result[1]['percentage'], '16');

        $this->assertEqual($result[2]['service'], 'Google+');
        $this->assertEqual($result[2]['count'], '7');
        $this->assertEqual($result[2]['percentage'], '16');

        $this->assertEqual($result[3]['service'], 'Twitter');
        $this->assertEqual($result[3]['count'], '7');
        $this->assertEqual($result[3]['percentage'], '16');
    }
}
<?php
require_once dirname(__FILE__).'/init.tests.php';
require_once ROOT_PATH.'webapp/extlibs/simpletest/autorun.php';

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

    public function testInsertGetUpdate() {
        $dao = new UserMySQLDAO();
        //$install_id, $service, $username
        $result = $dao->insert(1, 'twitter', 'testuser');
        $this->assertEqual(1, $result);

        $result = $dao->insert(2, 'facebook', 'testuser2');
        $this->assertEqual(2, $result);

        sleep(1); //make sure last_seen data gets changed
        //update
        $result = $dao->update(1, 'twitter', 'testuser');
        $this->assertEqual(1, $result);
        $user = $dao->get(1, 'twitter', 'testuser');
    }

    public function testDeleteByInstallation() {
        $dao = new UserMySQLDAO();
        //$install_id, $service, $username
        $result = $dao->insert(1, 'testuser', 'twitter');
        $result = $dao->insert(2, 'testuser2', 'facebook');
        $result = $dao->insert(1, 'testuser3', 'facebook');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT * FROM cb_users;");
        $data = $ps->fetchAll();
        $this->assertEqual(3, sizeof($data));

        $dao->deleteByInstallation(1);

        $ps = CallbackMySQLDAO::$PDO->query("SELECT * FROM cb_users;");
        $data = $ps->fetchAll();
        $this->assertEqual(1, sizeof($data));
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
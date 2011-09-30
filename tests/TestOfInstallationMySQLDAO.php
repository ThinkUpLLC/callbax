<?php
require_once dirname(__FILE__).'/init.tests.php';
require_once ROOT_PATH.'webapp/config.inc.php';
require_once ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';

class TestOfInstallationMySQLDAO extends CallbaxUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new InstallationMySQLDAO();
        $this->assertNotNull($dao);
    }

    public function testInsert() {
        $dao = new InstallationMySQLDAO();
        $result = $dao->insert('http://example.com', '0.14');
        $this->assertEqual(1, $result);

        $result = $dao->insert('http://example2.com/', '0.14');
        $this->assertEqual(2, $result);

        $result = $dao->insert('http://example.com', '0.14');
        $this->assertFalse($result);
    }

    public function testGet() {
        $dao = new InstallationMySQLDAO();

        //doesn't exist
        $results = $dao->get('http://example.com');
        $this->assertIsA($results, 'array');
        $this->assertEqual(count($results), 0);

        //does exist
        $dao->insert('http://example.com', '0.14');

        $results = $dao->get('http://example.com');
        $this->assertIsA($results, 'array');
        $this->assertEqual(count($results), 1);
        $this->assertIsA($results[0], 'Installation');
    }

    public function testUpdate() {
        $dao = new InstallationMySQLDAO();
        $dao->insert('http://example.com', '0.11');

        $results = $dao->get('http://example.com');
        $this->assertEqual($results[0]->url, 'http://example.com');
        $this->assertEqual($results[0]->version, '0.11');

        $results = $dao->update('http://example.com', '0.14');
        $this->assertEqual(count($results), 1);
        $results = $dao->get('http://example.com');
        $this->assertEqual($results[0]->url, 'http://example.com');
        $this->assertEqual($results[0]->version, '0.14');
    }

    public function testGetPage() {
        $builders = array();
        $i = 45;
        while ($i > 0) {
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'version'=>'0.15', 'last_seen'=>'-'.$i.'h'));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>'twitter',
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }

        $installation_dao = new InstallationMySQLDAO();

        //test get page 1 of 20 posts with a next page
        $result = $installation_dao->getPage(1, 20);
        $this->assertEqual(count($result['installations']), 20);
        $this->assertEqual($result['next_page'], 2);
        $this->assertFalse($result['prev_page']);

        //test get page 2 of 20 posts with a next page
        $result = $installation_dao->getPage(2, 20);
        $this->assertEqual(count($result['installations']), 20);
        $this->assertEqual($result['next_page'], 3);
        $this->assertEqual($result['prev_page'], 1);

        //test get page 3 of 5 posts with a next page
        $result = $installation_dao->getPage(3, 20);
        $this->assertEqual(count($result['installations']), 5);
        $this->assertFalse($result['next_page']);
        $this->assertEqual($result['prev_page'], 2);
    }

    public function testUpdateUserCount() {
        $builders = array();
        $i = 45;
        while ($i > 0) {
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'version'=>'0.15', 'last_seen'=>'-'.$i.'h'));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>'twitter',
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }

        $installation_dao = new InstallationMySQLDAO();
        //installation doesn't exist
        $result = $installation_dao->updateUserCount(50);
        $this->assertEqual($result, 0);

        //installation does exist, has 1 user
        $result = $installation_dao->updateUserCount(1);
        $this->assertEqual($result, 1);
        $ps = InstallationMySQLDAO::$PDO->query("SELECT user_count FROM cb_installations WHERE id=1;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['user_count'], 1);

        //installation exists, has 2 users
        $user_builder = FixtureBuilder::build('users', array('installation_id'=>1, 'service'=>'twitter',
        'username'=>'user_yay'));
        $builders[] = $user_builder;

        $result = $installation_dao->updateUserCount(1);
        $this->assertEqual($result, 1);
        $ps = InstallationMySQLDAO::$PDO->query("SELECT user_count FROM cb_installations WHERE id=1;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['user_count'], 2);
    }

    public function testGetTotal(){
        $installation_dao = new InstallationMySQLDAO();
        $total = $installation_dao->getTotal();
        $this->assertEqual($total, 0);

        $i = 37;
        while ($i > 0) {
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'version'=>'0.15', 'last_seen'=>'-'.$i.'h'));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>'twitter',
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }

        $total = $installation_dao->getTotal();
        $this->assertEqual($total, 37);
    }

    public function testGetFirstSeenInstallationDate() {
        $installation_dao = new InstallationMySQLDAO();
        $date = $installation_dao->getFirstSeenInstallationDate();
        $this->assertFalse($date);

        $i = 45;
        while ($i > 0) {
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'version'=>'0.15', 'last_seen'=>(($i==45)?'2000-12-31 01:00:00':'-'.$i.'h')));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>'twitter',
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }

        $date = $installation_dao->getFirstSeenInstallationDate();
        $this->assertEqual($date, '2000-12-31 01:00:00');
    }

    public function testOfGetVersionTotals() {
        $dao = new InstallationMySQLDAO();
        $result = $dao->getVersionTotals();
        $this->assertEqual(count($result), 0);

        $i = 57;
        while ($i > 0) {
            $version_randomizer = $i%7;
            switch ($version_randomizer) {
                case 1:
                    $version = '0.12';
                    break;
                case 2:
                    $version = '0.13';
                    break;
                case 3:
                    $version = '0.14';
                    break;
                default:
                    $version = '0.15';
                    break;
            }
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'version'=>$version, 'last_seen'=>(($i==45)?'2000-12-31 01:00:00':'-'.$i.'h')));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>'Twitter',
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }
        $result = $dao->getVersionTotals();

        $this->assertEqual(count($result), 4);

        $this->assertEqual($result[0]['version'], '0.12');
        $this->assertEqual($result[0]['count'], '9');
        $this->assertEqual($result[0]['percentage'], '16');

        $this->assertEqual($result[1]['version'], '0.13');
        $this->assertEqual($result[1]['count'], '8');
        $this->assertEqual($result[1]['percentage'], '14');

        $this->assertEqual($result[2]['version'], '0.14');
        $this->assertEqual($result[2]['count'], '8');
        $this->assertEqual($result[2]['percentage'], '14');

        $this->assertEqual($result[3]['version'], '0.15');
        $this->assertEqual($result[3]['count'], '32');
        $this->assertEqual($result[3]['percentage'], '56');
    }

    public function testOfGetUserCountDistribution() {
        $dao = new InstallationMySQLDAO();
        $result = $dao->getUserCountDistribution();
        $this->assertEqual(count($result), 0);

        $i = 57;
        while ($i > 0) {
            $user_count_randomizer = $i%7;
            switch ($user_count_randomizer) {
                case 1:
                    $user_count = '1';
                    break;
                case 2:
                    $user_count = '2';
                    break;
                case 3:
                    $user_count = '3';
                    break;
                default:
                    $user_count = '4';
                    break;
            }
            $install_builder = FixtureBuilder::build('installations', array('url'=>'http://example.com/thinkup'.$i,
            'user_count'=>$user_count, 'last_seen'=>(($i==45)?'2000-12-31 01:00:00':'-'.$i.'h')));
            $builders[] = $install_builder;
            $id = $install_builder->columns['last_insert_id'];
            $user_builder = FixtureBuilder::build('users', array('installation_id'=>$id, 'service'=>'Twitter',
            'username'=>'user'.$i));
            $builders[] = $user_builder;
            $i --;
        }
        $result = $dao->getUserCountDistribution();

        $this->assertEqual(count($result), 4);

        $this->assertEqual($result[0]['user_count'], 1);
        $this->assertEqual($result[0]['count'], '9');
        $this->assertEqual($result[0]['percentage'], '16');

        $this->assertEqual($result[1]['user_count'], 2);
        $this->assertEqual($result[1]['count'], '8');
        $this->assertEqual($result[1]['percentage'], '14');

        $this->assertEqual($result[2]['user_count'], 3);
        $this->assertEqual($result[2]['count'], '8');
        $this->assertEqual($result[2]['percentage'], '14');

        $this->assertEqual($result[3]['user_count'], 4);
        $this->assertEqual($result[3]['count'], '32');
        $this->assertEqual($result[3]['percentage'], '56');
    }
}
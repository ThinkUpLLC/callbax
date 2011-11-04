<?php
require_once dirname(__FILE__).'/init.tests.php';
require_once ROOT_PATH.'webapp/config.inc.php';
require_once ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';

class TestOfProcessCallbackController extends CallbaxUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new ProcessCallbackController(true);
        $this->assertNotNull($controller);
        $this->assertIsA($controller, 'ProcessCallbackController');
    }

    public function testControl() {
        //no callbacks to process
        $controller = new ProcessCallbackController(true);
        $results = $controller->control();
        $this->assertEqual($results, '');

        //add callbacks to process
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>1,
        'referrer'=>'http://dev.thinkup.com/index.php?u=Gina+Trapani&n=google%2B', 'version'=>'0.15',
        'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>2,
        'referrer'=>'http://dev.thinkup.com/account/?m=manage', 'version'=>'0.14',
        'last_seen'=>'-10m'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>3,
        'referrer'=>'http://dev.thinkup.com/index.php?u=ginatrapani&n=twitter', 'version'=>'0.15',
        'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>4,
        'referrer'=>'http://expertlabs.aaas.org/thinkup01/index.php?u=The+White+House&n=facebook+page',
        'version'=>'0.15', 'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>5,
        'referrer'=>'http://smarterware.org/thinkup/?u=Gina+Trapani&n=facebook+page',
        'version'=>'0.15', 'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>6,
        'referrer'=>'http://smarterware.org/thinkup/index.php?u=Gina+Trapani&n=facebook+page',
        'version'=>'0.15', 'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>7,
        'referrer'=>'http://timomcd.com/thinkup/user/?u=YaYayo&n=twitter&i=timomcd',
        'version'=>'0.15', 'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>8,
        'referrer'=>'http://timomcd.com/thinkup/index.php?u=YaYayo&n=twitter',
        'version'=>'0.15', 'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>9,
        'referrer'=>'http://www.timomcd.com/thinkup/index.php?u=YaYayo&n=twitter',
        'version'=>'0.15', 'last_seen'=>'-1h'));
        $builders[] = FixtureBuilder::build('callbacks', array('id'=>10,
        'referrer'=>'http://www.timomcd.com/thinkup/user/index.php?u=YaYayoLLL&n=twitter',
        'version'=>'0.15', 'last_seen'=>'-1h'));

        $results = $controller->control();
        $this->assertEqual($results, '');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT COUNT(*) AS total FROM cb_installations;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 4);

        $ps = CallbackMySQLDAO::$PDO->query("SELECT * FROM cb_installations;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['url'], 'http://dev.thinkup.com/');
        $this->assertEqual($data[1]['url'], 'http://expertlabs.aaas.org/thinkup01/');
        $this->assertEqual($data[2]['url'], 'http://smarterware.org/thinkup/');
        $this->assertEqual($data[3]['url'], 'http://timomcd.com/thinkup/');

        $ps = CallbackMySQLDAO::$PDO->query("SELECT * FROM cb_users;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['service'], 'Google+');
        $this->assertEqual($data[0]['username'], 'Gina Trapani');
        $this->assertEqual($data[0]['installation_id'], 1);
        $this->assertEqual($data[1]['service'], 'Twitter');
        $this->assertEqual($data[1]['username'], 'ginatrapani');
        $this->assertEqual($data[1]['installation_id'], 1);
        $this->assertEqual($data[2]['service'], 'Facebook Page');
        $this->assertEqual($data[2]['username'], 'The White House');
        $this->assertEqual($data[2]['installation_id'], 2);
        $this->assertEqual($data[3]['service'], 'Facebook Page');
        $this->assertEqual($data[3]['username'], 'Gina Trapani');
        $this->assertEqual($data[3]['installation_id'], 3);

        $ps = CallbackMySQLDAO::$PDO->query("SELECT count(*) AS total FROM cb_callbacks;");
        $data = $ps->fetchAll();
        $this->assertEqual($data[0]['total'], 0);
    }
}
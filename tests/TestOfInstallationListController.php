<?php
require_once dirname(__FILE__).'/init.tests.php';
//require_once ROOT_PATH.'webapp/config.inc.php';
require_once ROOT_PATH.'webapp/extlibs/simpletest/autorun.php';

class TestOfInstallationListController extends CallbaxUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new InstallationListController(true);
        $this->assertNotNull($controller);
        $this->assertIsA($controller, 'InstallationListController');
    }

    public function testControl() {
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

        $controller = new InstallationListController(true);
        $result = $controller->control();
        //echo $result;
    }
}
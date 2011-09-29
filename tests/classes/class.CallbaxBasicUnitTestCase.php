<?php
require_once ROOT_PATH.'webapp/_lib/model/class.Loader.php';

class CallbaxBasicUnitTestCase extends UnitTestCase {

    public function setUp() {
        parent::setUp();
        Loader::register(array(
        ROOT_PATH . 'tests/',
        ROOT_PATH . 'tests/classes/',
        ROOT_PATH . 'tests/fixtures/',
        ));

        $config = Config::getInstance();
        if ($config->getValue('timezone')) {
            date_default_timezone_set($config->getValue('timezone'));
        }
        $this->DEBUG = (getenv('TEST_DEBUG')!==false) ? true : false;

        self::isTestEnvironmentReady();
    }

    public function tearDown() {
        Config::destroyInstance();
        if (isset($_SESSION)) {
            $this->unsetArray($_SESSION);
        }
        $this->unsetArray($_POST);
        $this->unsetArray($_GET);
        $this->unsetArray($_REQUEST);
        $this->unsetArray($_SERVER);
        $this->unsetArray($_FILES);
        Loader::unregister();
        parent::tearDown();
    }

    /**
     * Unset all the values for every key in an array
     * @param array $array
     */
    protected function unsetArray(array &$array) {
        $keys = array_keys($array);
        foreach ($keys as $key) {
            unset($array[$key]);
        }
    }

    /**
     * Move webapp/config.inc.php to webapp/config.inc.bak.php for tests with no config file
     */
    protected function removeConfigFile() {
        if (file_exists(WEBAPP_PATH . 'config.inc.php')) {
            $cmd = 'mv '.WEBAPP_PATH . 'config.inc.php ' .WEBAPP_PATH . 'config.inc.bak.php';
            exec($cmd, $output, $return_val);
            if ($return_val != 0) {
                echo "Could not ".$cmd;
            }
        }
    }

    /**
     * Move webapp/config.inc.bak.php to webapp/config.inc.php
     */
    protected function restoreConfigFile() {
        if (file_exists(WEBAPP_PATH . 'config.inc.bak.php')) {
            $cmd = 'mv '.WEBAPP_PATH . 'config.inc.bak.php ' .WEBAPP_PATH . 'config.inc.php';
            exec($cmd, $output, $return_val);
            if ($return_val != 0) {
                echo "Could not ".$cmd;
            }
        }
    }

    public function __destruct() {
        $this->restoreConfigFile();
    }

    public function debug($message) {
        if($this->DEBUG) {
            $bt = debug_backtrace();
            print get_class($this) . ": line " . $bt[0]['line'] . " - " . $message . "\n";
        }
    }

    /**
     * Preemptively halt test run if testing environment requirement isn't met.
     * Prevents unnecessary/inexplicable failures and data loss.
     */
    public static function isTestEnvironmentReady() {
        require WEBAPP_PATH.'config.inc.php';

        if (!is_writable(WEBAPP_PATH. '_lib/view/compiled_view')) {
            $message = "In order to test your Callbax installation, ".WEBAPP_PATH. '_lib/view/compiled_view'.
            "must be writable.";
        }
        if (!is_writable(WEBAPP_PATH. '_lib/view/compiled_view/cache')) {
            $message = "In order to test your Callbax installation, ".WEBAPP_PATH.
            '_lib/view/compiled_view/cache'. "must be writable.";
        }

        global $TEST_DATABASE;

        if ($CALLBAX_CFG['db_name'] != $TEST_DATABASE) {
            $message = "The database name in webapp/config.inc.php does not match \$TEST_DATABASE in ".
            "tests/config.tests.inc.php. 
In order to test your Callbax installation without losing data, these database names must both point to the same ".
"empty test database.";
        }

        if ($CALLBAX_CFG['cache_pages']) {
            $message = "In order to test your Callbax installation, \$CALLBAX_CFG['cache_pages'] must be set to false.";
        }

        if (isset($message)) {
            die("Stopping tests...Test environment isn't ready.
".$message."
Please try again.
");
        }
    }
}

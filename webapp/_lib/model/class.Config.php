<?php
class Config {
    /**
     *
     * @var Config
     */
    private static $instance;
    /**
     *
     * @var array
     */
    var $config = array();
    /**
     * Private Constructor
     * @param array $vals Optional values to override file config
     * @return Config
     */
    private function __construct($vals = null) {
        if ($vals != null ) {
            $this->config = $vals;
        } else {
            Utils::defineConstants();
            if (file_exists(WEBAPP_PATH . 'config.inc.php')) {
                require WEBAPP_PATH . 'config.inc.php';
                $this->config = $CALLBAX_CFG;
            } else {
                throw new Exception('Callbax\'s configuration file does not exist!');
            }
        }
    }
    /**
     * Get the singleton instance of Config
     * @param array $vals Optional values to override file config
     * @return Config
     */
    public static function getInstance($vals = null) {
        if (!isset(self::$instance)) {
            self::$instance = new Config($vals);
        }
        return self::$instance;
    }
    /**
     * Get the configuration value
     * @param    string   $key   key of the configuration key/value pair
     * @return   mixed    value of the configuration key/value pair
     */
    public function getValue($key) {
        $value = isset($this->config[$key]) ? $this->config[$key] : null;
        return $value;
    }
    /**
     * Provided only for use when overriding config.inc.php values in tests
     * @param string $key
     * @param string $value
     * @return string $value
     */
    public function setValue($key, $value) {
        $value = $this->config[$key] = $value;
        return $value;
    }
    /**
     * Provided only for tests that want to kill Config object in tearDown()
     */
    public static function destroyInstance() {
        if (isset(self::$instance)) {
            self::$instance = null;
        }
    }
    /**
     * Provided for tests which expect an array
     */
    public function getValuesArray() {
        return $this->config;
    }
    /**
     * Returns the GMT offset in hours based on the application's defined timezone.
     *
     * If $time is given, gives the offset for that time; otherwise uses the current time.
     *
     * @param int $time The time to base it on, as anything strtotime() takes; leave blank for current time.
     * @return int The GMT offset in hours.
     */
    public function getGMTOffset($time = 0) {
        $time = $time ? $time : 'now';
        $tz = ($this->getValue('timezone')==null)?date('e'):$this->getValue('timezone');
        // this may be currently required for some setups to avoid fatal php timezone complaints when
        // exec'ing off the streaming child processes.
        date_default_timezone_set($tz);
        return timezone_offset_get( new DateTimeZone($tz), new DateTime($time) ) / 3600;
    }
}

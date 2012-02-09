<?php

abstract class Controller {
    /**
     * @var ViewManager
     */
    protected $view_mgr;
    /**
     * @var string Smarty template filename
     */
    protected $view_template = null;
    /**
     *
     * @var string cache key separator
     */
    const KEY_SEPARATOR='-';
    /**
     *
     * @var bool
     */
    protected $profiler_enabled = false;
    /**
     *
     * @var float
     */
    private $start_time = 0;
    /**
     *
     * @var araray
     */
    protected $header_scripts = array ();

    /**
     *
     * @var array
     */
    protected $json_data = null;

    /**
     *
     * @var str
     */
    protected $content_type = 'text/html; charset=UTF-8'; //default

    /**
     *
     * @var boolean if true we will pass a CSRF token to the view
     */
    protected $view_csrf_token = false; //default

    /**
     * Constructs Controller
     *  @return Controller
     */
    public function __construct($session_started=false) {
        if (!$session_started) {
            session_start();
        }
        try {
            $config = Config::getInstance();
            $this->profiler_enabled = Profiler::isEnabled();
            if ( $this->profiler_enabled) {
                $this->start_time = microtime(true);
            }
            $this->view_mgr = new ViewManager();

        } catch (Exception $e) {
            Utils::defineConstants();
            $cfg_array =  array(
            'site_root_path'=>BASE_URL,
            'source_root_path'=>ROOT_PATH,
            'debug'=>false, 
            'cache_pages'=>false);
            $this->view_mgr = new ViewManager($cfg_array);
        }
    }

    /**
     * Handle request parameters for a particular resource and return view markup.
     *
     * @return str Markup which renders controller results.
     */
    abstract public function control();

    /**
     * Returns cache key as a string,
     * Preface every key with .ht to make resulting file "forbidden" by request thanks to Apache's default rule
     * <FilesMatch "^\.([Hh][Tt])">
     *    Order allow,deny
     *    Deny from all
     *    Satisfy All
     * </FilesMatch>
     *
     * Set to public for the sake of tests only.
     * @return str cache key
     */
    public function getCacheKeyString() {
        $view_cache_key = array();
        // if ($this->getLoggedInUser()) {
        //     array_push($view_cache_key, $this->getLoggedInuser());
        // }
        $keys = array_keys($_GET);
        foreach ($keys as $key) {
            array_push($view_cache_key, $_GET[$key]);
        }
        return '.ht'.$this->view_template.self::KEY_SEPARATOR.(implode($view_cache_key, self::KEY_SEPARATOR));
    }

    /**
     * Generates web page markup
     *
     * @return str view markup
     */
    protected function generateView() {
        // add header javascript if defined
        if ( count($this->header_scripts) > 0) {
            $this->addToView('header_scripts', $this->header_scripts);
        }

        // add CSRF token if enabled and defined
        if ($this->view_csrf_token) {
            $csrf_token = Session::getCSRFToken();
            if (isset($csrf_token)) { $this->addToView('csrf_token', $csrf_token); }
        }

        $this->sendHeader();
        if (isset($this->view_template)) {
            if ($this->view_mgr->isViewCached()) {
                $cache_key = $this->getCacheKeyString();
                if ($this->profiler_enabled && !isset($this->json_data) &&
                strpos($this->content_type, 'text/javascript') === false) {
                    $view_start_time = microtime(true);
                    $cache_source = $this->shouldRefreshCache()?"DATABASE":"FILE";
                    $results = $this->view_mgr->fetch($this->view_template, $cache_key);
                    $view_end_time = microtime(true);
                    $total_time = $view_end_time - $view_start_time;
                    $profiler = Profiler::getInstance();
                    $profiler->add($total_time, "Rendered view from ". $cache_source . ", cache key: <i>".
                    $this->getCacheKeyString(), false).'</i>';
                    return $results;
                } else {
                    return $this->view_mgr->fetch($this->view_template, $cache_key);
                }
            } else {
                if ($this->profiler_enabled && !isset($this->json_data) &&
                strpos($this->content_type, 'text/javascript') === false) {
                    $view_start_time = microtime(true);
                    $results = $this->view_mgr->fetch($this->view_template);
                    $view_end_time = microtime(true);
                    $total_time = $view_end_time - $view_start_time;
                    $profiler = Profiler::getInstance();
                    $profiler->add($total_time, "Rendered view (not cached)", false);
                    return $results;
                } else  {
                    return $this->view_mgr->fetch($this->view_template);
                }
            }
        } else if (isset($this->json_data) ) {
            $this->setContentType('application/json');
            if ($this->view_mgr->isViewCached()) {
                if ($this->view_mgr->is_cached('json.tpl', $this->getCacheKeyString())) {
                    return $this->view_mgr->fetch('json.tpl', $this->getCacheKeyString());
                } else {
                    $this->prepareJSON();
                    return $this->view_mgr->fetch('json.tpl', $this->getCacheKeyString());
                }
            } else {
                $this->prepareJSON();
                return $this->view_mgr->fetch('json.tpl');
            }
        } else {
            throw new Exception(get_class($this).': No view template specified');
        }
    }

    /**
     * Prepares the JSON data in $this->json_data and adds it to the current view under the key "json".
     *
     * @param bool $indent Whether or not to indent the JSON string. Defaults to true.
     * @param bool $stripslashes Whether or not to strip escaped slashes. Default to true.
     * @param bool $convert_numeric_strings Whether or not to convert numeric strings to numbers. Defaults to true.
     */
    private function prepareJSON($indent = true, $stripslashes = true, $convert_numeric_strings = true) {
        if (isset($this->json_data)) {
            $json = json_encode($this->json_data);
            if ($stripslashes) {
                // strip escaped forwardslashes
                $json = preg_replace("/\\\\\//", '/', $json);
            }
            if ($convert_numeric_strings) {
                // converts numeric strings to numbers
                $json = Utils::convertNumericStrings($json);
            }
            if ($indent) {
                // indents JSON strings so they are human readable
                $json = Utils::indentJSON($json);
            }
            $this->addToView('json', $json);
        }
    }

    /**
     * Send content type header
     */
    protected function sendHeader() {
        if ( ! headers_sent() ) { // suppress 'headers already sent' error while testing
            header('Content-Type: ' . $this->content_type, true);
        }
    }
    /**
     * Sets the view template filename
     *
     * @param str $tpl_filename
     */
    protected function setViewTemplate($tpl_filename) {
        $this->view_template = $tpl_filename;
    }

    /**
     * Sets json data structure to output a json string, and sets Content-Type to appplication/json
     *
     * @param array json data
     */
    protected function setJsonData($data) {
        if ($data != null) {
            $this->setContentType('application/json');
        }

        $this->json_data = $data;
    }

    /**
     * Sets Content Type header
     *
     * @param string Content Type
     */
    protected function setContentType($content_type) {
        if ($content_type != 'image/png') {
            $this->content_type = $content_type.'; charset=UTF-8';
        } else {
            $this->content_type = $content_type;
        }
    }

    /**
     * Gets Content Type header
     *
     * @return string Content Type
     */
    public function getContentType() {
        return $this->content_type;
    }

    /**
     * Add javascript to header
     *
     * @param str javascript path
     */
    public function addHeaderJavaScript($script) {
        array_push($this->header_scripts, $script);
    }

    /**
     * Add data to view template engine for rendering
     *
     * @param str $key
     * @param mixed $value
     */
    protected function addToView($key, $value) {
        $this->view_mgr->assign($key, $value);
    }

    /**
     * Invoke the controller
     *
     * Always use this method, not control(), to invoke the controller.
     * @TODO show get 500 error template on Exception
     * (if debugging is true, pass the exception details to the 500 template)
     */
    public function go() {
        try {
            $this->initalizeApp();

            // are we in need of a database migration?
            $classname = get_class($this);
            $results = $this->control();
            if ($this->profiler_enabled && !isset($this->json_data)
            && strpos($this->content_type, 'text/javascript') === false
            && strpos($this->content_type, 'text/csv') === false) {
                $end_time = microtime(true);
                $total_time = $end_time - $this->start_time;
                $profiler = Profiler::getInstance();
                $this->disableCaching();
                $profiler->add($total_time,
                    "total page execution time, running ".$profiler->total_queries." queries.");
                $this->setViewTemplate('_profiler.tpl');
                $this->addToView('profile_items',$profiler->getProfile());
                return  $results . $this->generateView();
            }
        } catch (Exception $e) {
            //Explicitly set TZ (before we have user's choice) to avoid date() warning about using system settings
            Utils::setDefaultTimezonePHPini();
            $content_type = $this->content_type;
            if (strpos($content_type, ';') !== false) {
                $exploded = explode(';', $content_type);
                $content_type = array_shift($exploded);
            }
            switch ($content_type) {
                case 'application/json':
                    $this->setViewTemplate('500.json.tpl');
                    break;
                case 'text/plain':
                    $this->setViewTemplate('500.txt.tpl');
                    break;
                default:
                    $this->setViewTemplate('500.tpl');
            }
            $this->addToView('error_type', get_class($e));
            $this->addErrorMessage($e->getMessage());
            return $this->generateView();
        }
    }

    /**
     * Initalize app
     * Load config file and required plugins
     * @throws Exception
     */
    private function initalizeApp() {
        $classname = get_class($this);
        //Initialize config
        $config = Config::getInstance();
        if ($config->getValue('timezone')) {
            date_default_timezone_set($config->getValue('timezone'));
        }
        if ($config->getValue('debug')) {
            ini_set("display_errors", 1);
            ini_set("error_reporting", E_ALL);
        }
    }

    /**
     * Provided for tests only, to assert that proper view values have been set. (Debug must be equal to true.)
     * @return ViewManager
     */
    public function getViewManager() {
        return $this->view_mgr;
    }

    /**
     * Turn off caching
     * Provided in case an individual controller wants to override the application-wide setting.
     */
    protected function disableCaching() {
        $this->view_mgr->disableCaching();
    }

    /**
     * Check if cache needs refreshing
     * @return bool
     */
    protected function shouldRefreshCache() {
        if ($this->view_mgr->isViewCached()) {
            return !$this->view_mgr->is_cached($this->view_template, $this->getCacheKeyString());
        } else {
            return true;
        }
    }

    /**
     * Set web page title
     * This method only works for views that reference _header.tpl.
     * @param str $title
     */
    public function setPageTitle($title) {
        $this->addToView('controller_title', $title);
    }

    /**
     * Add error message to view.
     * Include field if the message goes on a specific place on the page; otherwise leave it null for the message
     * to be page-level.
     * @param str $msg
     * @param str $field Defaults to null for page-level messages.
     */
    public function addErrorMessage($msg, $field=null) {
        $this->disableCaching();
        $this->view_mgr->addErrorMessage($msg, $field);
    }

    /**
     * Add success message to view
     * Include field if the message goes on a specific place on the page; otherwise leave it null for the message
     * to be page-level.
     * @param str $msg
     * @param str $field Defaults to null for page-level messages.
     */
    public function addSuccessMessage($msg, $field=null) {
        $this->disableCaching();
        $this->view_mgr->addSuccessMessage($msg, $field);
    }

    /**
     * Add informational message to view
     * Include field if the message goes on a specific place on the page; otherwise leave it null for the message
     * to be page-level.
     * @param str $msg
     * @param str $field Defaults to null for page-level messages.
     */
    public function addInfoMessage($msg, $field=null) {
        $this->disableCaching();
        $this->view_mgr->addInfoMessage($msg, $field);
    }

    /**
     * Will enable a CSRF token in the view
     */
    public function enableCSRFToken() {
        $this->view_csrf_token = true;
    }

    /**
     * Get the view CSRF token enabled status
     */
    public function isEnableCSRFToken() {
        return $this->view_csrf_token;
    }

    /**
     * Validate the CSRF token passed in the request data.
     * @throws invalid InvalidCSRFTokenException
     * @return bool True if $_POST['csrf_token'] or $_GET['csrf_token'] is valid
     */
    public function validateCSRFToken() {
        $token = 'no token passed';
        if (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        } else if (isset($_GET['csrf_token'])) {
            $token = $_GET['csrf_token'];
        }
        $session_token = Session::getCSRFToken();
        if ($session_token && $session_token == $token) {
            return true;
        } else {
            throw new InvalidCSRFTokenException($token);
        }
    }
}

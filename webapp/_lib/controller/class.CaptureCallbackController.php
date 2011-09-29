<?php

class CaptureCallbackController extends Controller {
    public function control() {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '' && isset($_GET['v'])) {
            $callback_dao = new CallbackMySQLDAO();
            $callback_dao->insert($_SERVER['HTTP_REFERER'], $_GET['v']);
        }
        return '';
    }
}

<?php

class CaptureCallbackController extends Controller {
    public function control() {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '' && isset($_GET['v'])) {
            if (isset($_GET['usage']) && $_GET['usage'] == 'n') {
                $is_opted_out = true;
            } else {
                $is_opted_out = false;
            }
            $referer = $_SERVER['HTTP_REFERER'];
            $parsed_referer = parse_url($referer);
            //only process URLs with service users defined
            if (isset($parsed_referer['host'])) {
                $callback_dao = new CallbackMySQLDAO();
                $callback_dao->insert($_SERVER['HTTP_REFERER'], $_GET['v'], $is_opted_out);
            }
        }
        return '';
    }
}

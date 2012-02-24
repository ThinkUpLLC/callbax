<?php

class CaptureCallbackController extends Controller {
    public function control() {
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '' && isset($_GET['v'])) {
            if (isset($_GET['usage']) && $_GET['usage'] == 'n') {
                $is_opted_out = true;
            } else {
                $is_opted_out = false;
            }
            $callback_dao = new CallbackMySQLDAO();
            $callback_dao->insert($_SERVER['HTTP_REFERER'], $_GET['v'], $is_opted_out);
        }
        return '';
    }
}

<?php

class ProcessCallbackController extends Controller {
    public function control() {
        $callback_dao = new CallbackMySQLDAO();
        $installation_dao = new InstallationMySQLDAO();
        $user_dao = new UserMySQLDAO();

        $service = null;
        $username = null;
        $url = null;

        $installation_ids_to_update = array();

        $callbacks = $callback_dao->get(20);
        while (count($callbacks) > 0 ) {
            foreach ($callbacks as $callback) {
                $parsed_referer = parse_url($callback->referrer);
                //only process URLs with service users defined
                if (isset($parsed_referer['query'])) {
                    $parsed_query = self::parse_query($parsed_referer['query']);
                    $service = (isset($parsed_query['n']))?$parsed_query['n']:null;
                    $username = (isset($parsed_query['u']))?$parsed_query['u']:null;
                    if (isset($service) && isset($username) && !isset($parsed_query['i'])) {
                        $service = ucwords(urldecode($service));
                        $username = urldecode($username);
                        //prep installation URL
                        //strip www.
                        $host = preg_replace('#^www\.(.+\.)#i', '$1', $parsed_referer['host']);
                        $path = $parsed_referer['path'];
                        //strip index.php
                        $path = str_replace('user/index.php', '', $path);
                        $path = str_replace('index.php', '', $path);

                        //add installation
                        $url = $parsed_referer['scheme'].'://'.$host.$path;
                        $installation = $installation_dao->get($url);
                        if (count($installation) == 0) { //doesn't exist, insert it
                            $installation_id = $installation_dao->insert($url, $callback->version);
                        } else { //update existing record's last_seen and version
                            $installation_id = $installation_dao->update($url, $callback->version);
                            $installation_id = $installation[0]->id;
                        }

                        $user_dao->insert($installation_id, $service, $username);
                        $installation_ids_to_update[] = $installation_id;
                    }
                }
                //update user counts for each installation
                foreach ($installation_ids_to_update as $id) {
                    $installation_dao->updateUserCount($id);
                }

                //delete callback
                $callback_dao->delete($callback->id);
                //reset vars for next loop
                $url = null;
                $service = null;
                $username = null;
                $parsed_referer = null;
            }
            $callbacks = $callback_dao->get(20);
        }
        return '';
    }

    public static function parse_query($query_string) {
        $query_string  = html_entity_decode($query_string);
        $query_string  = explode('&', $query_string);
        $arr  = array();

        foreach($query_string as $val) {
            $x  = explode('=', $val);
            $arr[$x[0]] = $x[1];
        }
        unset($val, $x, $query_string);
        return $arr;
    }
}

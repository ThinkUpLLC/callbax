<?php

class User {
    /**
     * @var int Internal unique ID
     */
    var $id;
    /**
     * @var int Installation ID
     */
    var $installation_id;
    /**
     * @var str Service name like facebook, twitter, facebook page
     */
    var $service;
    /**
     * @var str Service username
     */
    var $username;
    /**
     * @var str Last seen timestamp
     */
    var $last_seen;
    /**
     * @var str First seen timestamp
     */
    var $first_seen;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->installation_id = $row['installation_id'];
            $this->service = $row['service'];
            $this->username = $row['username'];
            $this->last_seen = $row['last_seen'];
            $this->first_seen = $row['first_seen'];
        }
    }
}
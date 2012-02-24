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
     * @var int Total number of user followers/subscribers/friends.
     */
    var $follower_count;
    /**
     * @var str First seen timestamp
     */
    var $first_seen;
    /**
     * @var str Last seen timestamp
     */
    var $last_seen;
    /**
     * @var str Last time the user follower count was updated.
     */
    var $last_follower_count;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->installation_id = $row['installation_id'];
            $this->service = $row['service'];
            $this->username = $row['username'];
            $this->follower_count = $row['follower_count'];
            $this->first_seen = $row['first_seen'];
            $this->last_seen = $row['last_seen'];
            $this->last_follower_count = $row['last_follower_count'];
        }
    }
}
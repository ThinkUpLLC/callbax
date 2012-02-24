<?php
class Installation {
    /**
     * @var int Internal unique ID
     */
    var $id;
    /**
     * @var str Base URL of installation
     */
    var $url;
    /**
     * @var int Total service users detected on this installation.
     */
    var $user_count;
    /**
     * @var str Version installation is running
     */
    var $version;
    /**
     * @var str Last seen timestamp
     */
    var $last_seen;
    /**
     * @var bool Whether or not the installation has opted out of usage tracking
     */
    var $is_opted_out;

    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->url = $row['url'];
            $this->user_count = $row['user_count'];
            $this->version = $row['version'];
            $this->last_seen = $row['last_seen'];
            $this->is_opted_out = PDODAO::convertDBToBool($row['is_opted_out']);
        }
    }
}
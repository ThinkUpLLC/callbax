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
     * @var str Version installation is running
     */
    var $version;
    /**
     * @var str Last seen timestamp
     */
    var $last_seen;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->url = $row['url'];
            $this->version = $row['version'];
            $this->last_seen = $row['last_seen'];
        }
    }
}
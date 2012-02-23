<?php
class Callback {
    /**
     * @var int Internal unique ID
     */
    var $id;
    /**
     * @var str Full raw path of the referring installation
     */
    var $referrer;
    /**
     * @var str Version number installation queried about
     */
    var $version;
    /**
     * @var str Last seen timestamp
     */
    var $last_seen;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->referrer = $row['referrer'];
            $this->version = $row['version'];
            $this->last_seen = $row['last_seen'];
        }
    }
}
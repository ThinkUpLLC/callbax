<?php

interface CallbackDAO {
    /**
     * Writes a callback to the data store.
     * @param str $referrer
     * @param str $version
     * @return int Inserted row ID
     */
    public function insert($referrer, $version);
    /**
     * Get an array of Callback objects by last_seen ASC.
     * @param int $limit Number to return
     * @return array Callback objects
     */
    public function get($limit);
    /**
     * Delete a callback row from the data store.
     * @param int $id Callback ID to delete
     * @return int Number rows affected
     */
    public function delete($id);
}

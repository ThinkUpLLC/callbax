<?php

interface InstallationDAO {
    public function get($url);

    public function insert($url, $version);

    public function update($url, $version);

    public function getTotal($days=null);

    public function getPage($page, $limit);

    public function getFirstSeenInstallationDate();

    public function getVersionTotals();
}

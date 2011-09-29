<?php

interface UserDAO {
    public function insert($install_id, $service, $username);

    public function getServiceTotals();
}

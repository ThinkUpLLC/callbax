<?php
class UserMySQLDAO extends PDODAO {
    public function insert($installation_id, $service, $username) {
        $q  = "INSERT INTO #prefix#users (installation_id, service, username, first_seen, last_seen) ";
        $q .= "VALUES ( :installation_id, :service, :username, NOW(), NOW() ) ";
        $vars = array(
            ':installation_id'=>$installation_id,
            ':service'=>$service,
            ':username'=>$username
        );
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }

    public function update($installation_id, $service, $username) {
        $q  = "UPDATE #prefix#users SET last_seen=NOW() WHERE ";
        $q .= "installation_id=:installation_id AND service=:service AND username=:username;";
        $vars = array(
            ':installation_id'=>$installation_id,
            ':service'=>$service,
            ':username'=>$username
        );
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateFollowerCount($service, $username, $follower_count) {
        $q  = "UPDATE #prefix#users SET follower_count=:follower_count, last_follower_count=NOW() WHERE ";
        $q .= "service=:service AND username=:username;";
        $vars = array(
            ':service'=>$service,
            ':username'=>$username,
            ':follower_count'=>$follower_count
        );
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateLastFollowerCount($service, $username) {
        $q  = "UPDATE #prefix#users SET last_follower_count=NOW() WHERE ";
        $q .= "service=:service AND username=:username;";
        $vars = array(
            ':service'=>$service,
            ':username'=>$username
        );
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getServiceTotals(){
        $q  = "SELECT service, COUNT( * ) AS total_users_per_service FROM #prefix#users GROUP BY service";
        $ps = $this->execute($q);
        $results = $this->getDataRowsAsArrays($ps);

        $total_users = 0;
        foreach ($results as $result) {
            $total_users = $total_users + $result['total_users_per_service'];
        }
        $stats = array();
        foreach ($results as $result) {
            $service_percentage = round(($result['total_users_per_service']*100)/$total_users);
            if ($service_percentage > 0) {
                $stats[] = array('service'=> $result['service'], 'count'=>$result['total_users_per_service'],
                'percentage'=>$service_percentage);
            }
        }
        return $stats;
    }

    public function getTotal($days=null){
        $q  = "SELECT COUNT(*) AS total FROM #prefix#users";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getTotalActive($days=null){
        $q  = "SELECT COUNT(*) AS total FROM #prefix#users ";
        $q .= "WHERE last_seen >= date_sub(current_date, INTERVAL 1 month) ";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function deleteByInstallation($installation_id) {
        $q  = "DELETE FROM #prefix#users ";
        $q .= "WHERE installation_id=:installation_id;";
        $vars = array(
            ':installation_id'=>$installation_id,
        );
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function get($installation_id, $service, $username) {
        $q  = "SELECT * FROM #prefix#users  WHERE ";
        $q .= "installation_id=:installation_id AND service=:service AND username=:username;";
        $vars = array(
            ':installation_id'=>$installation_id,
            ':service'=>$service,
            ':username'=>$username
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'User');
    }

    public function getStaleFollowerCounts($service, $limit=100, $days_ago=30) {
        $q  = "SELECT * FROM #prefix#users  WHERE ";
        $q .= "service=:service AND last_follower_count < DATE_SUB(NOW(), INTERVAL :days_ago DAY) LIMIT :limit;";
        $vars = array(
            ':service'=>$service,
            ':days_ago'=>(int)$days_ago,
            ':limit'=>(int)$limit
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'User');
    }
}

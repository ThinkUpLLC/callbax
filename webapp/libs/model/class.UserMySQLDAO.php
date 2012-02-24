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
            $stats[] = array('service'=> $result['service'], 'count'=>$result['total_users_per_service'],
            'percentage'=>round(($result['total_users_per_service']*100)/$total_users));
        }
        return $stats;
    }

    public function getTotal($days=null){
        $q  = "SELECT COUNT(*) AS total FROM #prefix#users";
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
        return $this->getDataRowsAsObjects($ps, 'User');
    }
}

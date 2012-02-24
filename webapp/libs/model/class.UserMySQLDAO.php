<?php
class UserMySQLDAO extends PDODAO {
    public function insert($installation_id, $service, $username) {
        $q  = "INSERT IGNORE INTO #prefix#users (installation_id, service, username) ";
        $q .= "VALUES ( :installation_id, :service, :username ) ";
        $vars = array(
            ':installation_id'=>$installation_id,
            ':service'=>$service,
            ':username'=>$username
        );
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
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
}

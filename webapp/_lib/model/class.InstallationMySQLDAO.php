<?php
class InstallationMySQLDAO extends PDODAO {

    public function insert($url, $version) {
        $q  = "INSERT IGNORE INTO #prefix#installations  (url, version) VALUES ( :url, :version) ";
        $vars = array(
            ':url'=>$url,
            ':version'=>$version
        );
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }

    public function get($url) {
        $q  = "SELECT * FROM #prefix#installations  WHERE url=:url";
        $vars = array(
            ':url'=>$url
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Installation');
    }

    public function update($url, $version){
        $q  = "UPDATE #prefix#installations SET version=:version, last_seen=NOW() WHERE url=:url";
        $vars = array(
            ':url'=>$url,
            ':version'=>$version
        );
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getTotal($days=null){
        $q  = "SELECT COUNT(*) AS total FROM #prefix#installations";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getPage($page, $limit){
        $q  = "SELECT i.*, u.* FROM #prefix#installations i JOIN #prefix#users u ON u.installation_id = i.id ";
        $q .= "ORDER BY last_seen DESC LIMIT :start_on, :limit";
        $vars = array(
            ':limit'=>($limit+1),
            ':start_on'=>($limit*($page-1))
        );
        $ps = $this->execute($q, $vars);
        $installations = $this->getDataRowsAsArrays($ps);
        if (count($installations) > $limit) {
            array_pop($installations);
            $result['next_page'] = ($page+1);
        } else {
            $result['next_page'] = false;
        }
        $result['prev_page'] = ($page==1)?false:($page-1);
        $result['installations'] = $installations;
        return $result;
    }

    public function getFirstSeenInstallationDate(){
        $q  = "SELECT last_seen FROM #prefix#installations ORDER BY last_seen ASC LIMIT 1";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_seen'];
    }

    public function getVersionTotals(){
        $q  = "SELECT version, COUNT( * ) AS total_installs_per_version FROM #prefix#installations GROUP BY version";
        $ps = $this->execute($q);
        $results = $this->getDataRowsAsArrays($ps);

        $total_users = 0;
        foreach ($results as $result) {
            $total_users = $total_users + $result['total_installs_per_version'];
        }
        $stats = array();
        foreach ($results as $result) {
            $stats[] = array('version'=> $result['version'], 'count'=>$result['total_installs_per_version'],
            'percentage'=>round(($result['total_installs_per_version']*100)/$total_users));
        }
        return $stats;
    }

    public function updateUserCount($installation_id) {
        $q  = "UPDATE #prefix#installations i SET user_count = (SELECT COUNT(*) FROM #prefix#users u ";
        $q .= "WHERE u.installation_id = i.id) WHERE i.id = :installation_id;";
        $vars = array(
            ':installation_id'=>$installation_id
        );
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getUserCountDistribution() {
        $q  = "SELECT user_count, COUNT( * ) AS total_installs_with_user_count FROM #prefix#installations ";
        $q .= "GROUP BY user_count";
        $ps = $this->execute($q);
        $results = $this->getDataRowsAsArrays($ps);

        $total_installs = 0;
        foreach ($results as $result) {
            $total_installs = $total_installs + $result['total_installs_with_user_count'];
        }
        $stats = array();
        foreach ($results as $result) {
            $stats[] = array('user_count'=> $result['user_count'], 'count'=>$result['total_installs_with_user_count'],
            'percentage'=>round(($result['total_installs_with_user_count']*100)/$total_installs));
        }
        return $stats;
    }
}

<?php
class InstallationMySQLDAO extends PDODAO {

    public function insert($url, $version, $is_opted_out = false) {
        $q  = "INSERT IGNORE INTO #prefix#installations  ";
        $q .= "(url, version, is_opted_out) VALUES ( :url, :version, :is_opted_out) ";
        $vars = array(
            ':url'=>$url,
            ':version'=>$version,
            ':is_opted_out'=>$is_opted_out
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

    public function update($url, $version, $is_opted_out){
        $q  = "UPDATE #prefix#installations ";
        $q .= "SET version=:version, is_opted_out=:is_opted_out, last_seen=NOW() WHERE url=:url";
        $vars = array(
            ':url'=>$url,
            ':version'=>$version,
            ':is_opted_out'=>self::convertBoolToDB($is_opted_out)
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

    public function getTotalActive(){
        $q  = "SELECT COUNT(*) AS total FROM #prefix#installations ";
        $q .= "WHERE last_seen >= date_sub(current_date, INTERVAL 1 week) ";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getPage($page, $limit){
        $q  = "SELECT u.*, i.* FROM #prefix#installations i JOIN #prefix#users u ON u.installation_id = i.id ";
        //$q .= "ORDER BY i.id DESC, u.last_seen ASC LIMIT :start_on, :limit";
        $q .= "ORDER BY follower_count DESC LIMIT :start_on, :limit";
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

    public function getPageActiveInstallations($page, $limit){
        $q  = "SELECT u.*, i.* FROM #prefix#installations i JOIN #prefix#users u ON u.installation_id = i.id ";
        $q .= "WHERE i.last_seen >= date_sub(current_date, INTERVAL 1 week) ";
        //$q .= "ORDER BY i.id DESC, u.last_seen ASC LIMIT :start_on, :limit";
        $q .= "ORDER BY follower_count DESC LIMIT :start_on, :limit";
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

    public function getAll(){
        $q  = "SELECT i.*, u.* FROM #prefix#installations i JOIN #prefix#users u ON u.installation_id = i.id ";
        $q .= "ORDER BY i.id  DESC, last_seen ASC";
        $ps = $this->execute($q);
        $result['installations'] = $this->getDataRowsAsArrays($ps);
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
            $version_percentage = round(($result['total_installs_per_version']*100)/$total_users);
            if ($version_percentage > 0) {
                $stats[] = array('version'=> $result['version'], 'count'=>$result['total_installs_per_version'],
                'percentage'=>$version_percentage);
            }
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
        $upper_threshold = 7;
        $q  = "SELECT user_count, COUNT( * ) AS total_installs_with_user_count FROM #prefix#installations ";
        $q .= "GROUP BY user_count";
        $ps = $this->execute($q);
        $results = $this->getDataRowsAsArrays($ps);

        $total_installs = 0;
        foreach ($results as $result) {
            $total_installs = $total_installs + $result['total_installs_with_user_count'];
        }
        $stats = array();
        $more_than_threshold_total = 0;
        foreach ($results as $result) {
            if ($result['user_count'] < $upper_threshold) {
                $stats[] = array(
                'user_count'=> $result['user_count'],
                'count'=>$result['total_installs_with_user_count'],
                'percentage'=>round(($result['total_installs_with_user_count']*100)/$total_installs));
            } else {
                $more_than_threshold_total += $result['total_installs_with_user_count'];
            }
        }
        if ($total_installs > 0){
            $stats[] = array('user_count'=> $upper_threshold.'+',
            'count'=>$more_than_threshold_total,
            'percentage'=>round(($more_than_threshold_total*100)/$total_installs));
        }
        return $stats;
    }

    public function getHostingProviderDistribution($total_installs) {
        $hosts = array('amazonaws', 'phpfog', 'localhost');
        $hosts_total = 0;
        $stats = array();
        foreach ($hosts as $host) {
            $q = "SELECT count(*) AS total_installs FROM #prefix#installations WHERE url like '%".$host."%'";
            $ps = $this->execute($q);
            $results = $this->getDataRowsAsArrays($ps);
            foreach ($results as $result) {
                $stats[] = array(
                'host'=> $host,
                'count'=>$result['total_installs'],
                'percentage'=>round(($result['total_installs']*100)/$total_installs));
            }
            $hosts_total += $result['total_installs'];
        }
        $stats[] = array(
        'host'=> "other",
        'count'=>($total_installs -$hosts_total),
        'percentage'=>round((($total_installs -$hosts_total)*100)/$total_installs));
        return $stats;
    }

    public function getTotalOptOuts(){
        $q  = "SELECT COUNT(*) AS total FROM #prefix#installations WHERE is_opted_out=1;";
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }
}

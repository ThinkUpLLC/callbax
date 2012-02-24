<?php
class CallbackMySQLDAO extends PDODAO {
    public function insert($referrer, $version, $is_opted_out = false) {
        $is_opted_out = self::convertBoolToDB($is_opted_out);
        $q  = "INSERT INTO #prefix#callbacks ";
        $q .= "(referrer, version, is_opted_out) ";
        $q .= "VALUES ( :referrer, :version, :is_opted_out ) ";
        $vars = array(
            ':referrer'=>$referrer,
            ':version'=>$version,
            ':is_opted_out'=>$is_opted_out
        );
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }

    public function get($limit) {
        $q  = "SELECT * FROM #prefix#callbacks ";
        $q .= "LIMIT :limit";
        $vars = array(
            ':limit'=>$limit
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Callback');
    }

    public function delete($id) {
        $q  = "DELETE from #prefix#callbacks WHERE id=:id;";
        $vars = array(
            ':id'=>$id
        );
        $ps = $this->execute($q, $vars);
        return $this->getDeleteCount($ps);
    }
}

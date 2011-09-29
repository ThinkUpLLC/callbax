<?php
class CallbackMySQLDAO extends PDODAO implements CallbackDAO {
    public function insert($referrer, $version) {
        $q  = "INSERT INTO #prefix#callbacks ";
        $q .= "(referrer, version) ";
        $q .= "VALUES ( :referrer, :version ) ";
        $vars = array(
            ':referrer'=>$referrer,
            ':version'=>$version
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

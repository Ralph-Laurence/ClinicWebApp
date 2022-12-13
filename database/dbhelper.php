<?php
 
class DbHelper
{ 
    public function insert($pdo, $table, $data = array())
    {
        if ($pdo == null)
            die("Server Error");
  
        $columnNamesClean = array_keys($data);
        $columnNamesJoin = implode(",", $columnNamesClean);
        $columnNamesParam = array();

        foreach ($data as $k => $v) {
            array_push($columnNamesParam, ":$k");
        }

        $columnNamesParamJoin = implode(",", $columnNamesParam);
        
        $sql = "INSERT INTO $table($columnNamesJoin) VALUES($columnNamesParamJoin)";

        $sth = $pdo->prepare($sql);
        
        foreach($data as $k => &$v)
        {
            $sth->bindParam(":$k", $v);
        }

        $sth->execute();
    }

    public function get($pdo, $table)
    {
        if ($pdo == null)
            die("Server Error");

        $sql = "SELECT * FROM $table";
        $sth = $pdo->prepare($sql);
        $sth->execute();

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    } 
}
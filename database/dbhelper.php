<?php
//
// This class wraps the basic database operations so that
// we dont have to retype the same code over and over again
// for the same functionality. Also, it helps us to save
// a lot of time and improves readability and maintainablity
// of our code.
//
class DbHelper
{ 
    // Insert an array of data into the database.
    // $pdo     -> The connection object
    // $table   -> The name of the table we will store the data into
    // $data    -> KEY-VALUE pair array in which the KEY represents the
    //             column or what we call as fields and the VALUE is the
    //             value itself
    public function insert($pdo, $table, $data = array())
    {
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");
  
        // get all the column names in the array
        // then join them as single string separated by comma.
        // We will use the joined string to build a QUERY
        $columnNamesClean = array_keys($data);
        $columnNamesJoin = implode(",", $columnNamesClean);
        $columnNamesParam = array();

        // foreach column name, we create a placeholder prefixed with colon.
        // This is a good practice to avoid SQL Injection attacks.
        foreach ($data as $k => $v) {
            array_push($columnNamesParam, ":$k");
        }

        $columnNamesParamJoin = implode(",", $columnNamesParam);
        
        // We then create the final query
        $sql = "INSERT INTO $table($columnNamesJoin) VALUES($columnNamesParamJoin)";
        $sth = $pdo->prepare($sql);
        
        // We will bind the values provided from the array
        foreach($data as $k => &$v)
        {
            $sth->bindParam(":$k", $v);
        }

        // finally execute the query
        $sth->execute();
    }

    public function update($pdo, $table, $data = array(), $condition = array())
    {
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");

        // get all the column names from the keyvalue pair ($data) array
        $columnNamesClean = array_keys($data);

        // build the column parameter string array
        // with syntax as: 
        // col=?
        $columnNamesParam = array();

        foreach($columnNamesClean as $col)
        {
            $paramStr = $col."=?";

            array_push($columnNamesParam, $paramStr);
        }

        // then join the column parameter as single string
        $colParamStrings = implode(",", $columnNamesParam);

        // get the condition column names from keyvalue pair array
        $conditionColumns = array_keys($condition);

        // build the condition parameter string array
        // with syntax as:
        // col=?
        $condNamesParam = array();

        foreach($conditionColumns as $col)
        {
            $paramStr = $col."=?";
            
            array_push($condNamesParam, $paramStr);
        }

        // then join the condition parameter as single string 
        $condParamStrings = implode(" AND ", $condNamesParam);

        // build the final query
        $sql = "UPDATE $table SET $colParamStrings WHERE $condParamStrings";

        // execute the query then bind their values per parameters
        $sth = $pdo->prepare($sql);

        // value binding index (the question mark position)
        $paramIndex = 1;

        foreach(array_values($data) as $v)
        {
            $sth->bindValue($paramIndex, $v);
            $paramIndex++;
        }

        // condition value binding
        foreach(array_values($condition) as $v)
        {
            $sth->bindValue($paramIndex, $v);
            $paramIndex++;
        }

        $sth->execute(); 
    }

    public function delete($pdo, $table, $condition = array())
    { 
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");

        $condColNames = array_keys($condition);
        $condColumnParams = array();

        foreach ($condColNames as $col)
        {
            $paramStr = $col."=?";
            array_push($condColumnParams, $paramStr);
        }

        $condParamString = implode(" AND ", $condColumnParams);

        $sql = "DELETE FROM $table WHERE $condParamString";

        echo $sql;
    }

    // Select all records from the given database table. The returning data
    // will be of type Key-Value Pair or what we call the ASSOCIATIVE ARRAY.
    // $pdo         -> The connection object
    // $table       -> The name of the table we will retrieve the records from
    // $orderBy     -> ASC  = means all records are ordered ascending by default.
    //              -> DESC = for sorting the results in descending order.
    // $orderColumn -> How do we want to order/sort the results? 
    //                 Is it by name, id, age, etc... ?
    public function get($pdo, $table, $orderBy = 'ASC', $orderColumn = '')
    {
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");

        // We will use this to hold the Sorting mode
        $sort = '';

        // Only apply the sorting when the order mode and columns are provided
        if ($orderColumn != '' && ($orderBy == 'ASC' || $orderBy == 'DESC'))
            $sort = " ORDER BY $orderColumn $orderBy";

        // Create the query together with the sort mode
        // then execute the query
        $sql = "SELECT * FROM $table" . $sort;
        $sth = $pdo->prepare($sql);
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    } 

    // Check if a column's value exists in the database.
    // We do this by counting the occurrences of a specific value.
    // $pdo         -> The connection object
    // $table       -> The name of the table we will retrieve the records from
    // $needle      -> The value we want to find
    // $haystack    -> The field (column) where we will find the value
    public function exists($pdo, $table, $needle = "", $haystack ="")
    {
        $sql = "SELECT COUNT(*) FROM $table WHERE $haystack = '$needle'";
        $count = $pdo->query($sql)->fetchColumn();

        return !empty($count);
    }
}
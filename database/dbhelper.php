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
    public $ORDER_MODE_ASC = "ASC";
    public $ORDER_MODE_DESC = "DESC";

    private $PDO = null;

    function __construct($pdo = null)
    {
        $this->PDO = $pdo;
    }

    public function getInstance()
    {
        return $this->PDO;
    }

    // Insert an array of data into the database.
    // $pdo     -> The connection object
    // $table   -> The name of the table we will store the data into
    // $data    -> KEY-VALUE pair array in which the KEY represents the
    //             column or what we call as fields and the VALUE is the
    //             value itself
    public function insert($table, $data = array())
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
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
        $sth = $this->PDO->prepare($sql);
        
        // We will bind the values provided from the array
        foreach($data as $k => &$v)
        {
            $sth->bindParam(":$k", $v);
        }

        // finally execute the query
        $sth->execute();
    }

    public function update($table, $data = array(), $condition = array())
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
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
        $sth = $this->PDO->prepare($sql);

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
    // Delete a record from the database.
    // $pdo         -> The connection object
    // $table       -> The name of the table we will store the data into
    // $condition   -> KEY-VALUE pair array in which the KEY represents the
    //             column or what we call as fields and the VALUE is the
    //             value itself which will be used to match a condition
    public function delete($pdo, $table, $condition = array())
    { 
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");

        // get all column names from the condition
        $condColNames = array_keys($condition);
        $condColumnParams = array();

        // create column parameter placeholders (question mark)
        foreach ($condColNames as $col)
        {
            $paramStr = $col."=?";
            array_push($condColumnParams, $paramStr);
        }

        // build the parameter placeholder as single string
        $condParamString = implode(" AND ", $condColumnParams);

        // build the final query
        $sql = "DELETE FROM $table WHERE $condParamString";

        // bind parameter values then execute the query
        $sth = $pdo->prepare($sql);
        
        $iterator = 1;

        foreach(array_values($condition) as $val)
        {
            $sth->bindValue($iterator, $val);

            // the iterator is an index for binding values
            $iterator++;
        }

        $result = $sth->execute();
        return $result; // TRUE | FALSE

    }

    public function deleteWhereIn($table, $column = "", $in = array())
    { 
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");

        if (empty($in))
            return; 

        // build the placeholder string
        $placeholders = rtrim(str_repeat("?,", count($in)), ",");

        // build the final query
        $sql = "DELETE FROM $table WHERE $column IN ($placeholders)"; //  $condParamString
        
        // prepare the query
        $sth = $this->PDO->prepare($sql);

        // then bind parameter values
        $iterator = 1;

        foreach($in as $val)
        {
            $sth->bindValue($iterator, $val);

            // the iterator is an index for binding values
            $iterator++;
        }
        
        $result = $sth->execute();
        return $result; // TRUE | FALSE
    }

    // Select all records from the given database table. The returning data
    // will be of type Key-Value Pair or what we call the ASSOCIATIVE ARRAY.
    // $pdo         -> The connection object
    // $table       -> The name of the table we will retrieve the records from
    // $orderBy     -> ASC  = means all records are ordered ascending by default.
    //              -> DESC = for sorting the results in descending order.
    // $orderColumn -> How do we want to order/sort the results? 
    //                 Is it by name, id, age, etc... ?
    public function get($table, $orderBy = 'ASC', $orderColumn = '')
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");

        // We will use this to hold the Sorting mode
        $sort = '';

        // Only apply the sorting when the order mode and columns are provided
        if ($orderColumn != '' && ($orderBy == 'ASC' || $orderBy == 'DESC'))
            $sort = " ORDER BY $orderColumn $orderBy";

        // Create the query together with the sort mode
        // then execute the query
        $sql = "SELECT * FROM $table" . $sort;
        $sth = $this->PDO->prepare($sql);
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    } 

    public function getWhere($pdo, $table, $condition, $orderBy = 'ASC', $orderColumn = '')
    {
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");

        // We will use this to hold the Sorting mode
        $sort = '';

        // Only apply the sorting when the order mode and columns are provided
        if ($orderColumn != '' && ($orderBy == 'ASC' || $orderBy == 'DESC'))
            $sort = " ORDER BY $orderColumn $orderBy";

        // build the condition, store them here as individual string
        $conditions = [];

        // get field names and format as: fieldName=?
        foreach(array_keys($condition) as $k)
        {
            array_push($conditions, "$k=?");
        }

        // build the condition strings as single string
        $condString = implode(" AND ", $conditions);

        // Create the query together with the sort mode
        // then execute the query
        $sql = "SELECT * FROM $table WHERE $condString" . $sort;
        $sth = $pdo->prepare($sql);

        // bind condition values
        // $i = the parameter binding index

        $i = 1;

        foreach(array_values($condition) as $v)
        {
            $sth->bindValue($i, $v);
            $i++;
        }
 
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    }

    // Select all rows having specific fields
    // $pdo         -> The connection object
    // $table       -> The name of the table we will retrieve the records from
    // $fields      -> The columns to retrieve 
    //
    public function select($table, $fields = array(), $condition = array(), $sort = "ASC", $sortColumn = "", $singleRow = false)
    {
        // Stop execution if there is no connection object 
        // if fields list is empty
        if ($this->PDO == null || empty($fields))
            die("Server Error");

        // join fieldnames as single string separated by comma
        $field = implode(",", $fields);

        // the query string
        $sql = "SELECT $field FROM $table ";

        // Only build condition when required
        if (!empty($condition))
        {
            // build the condition, store them here as individual string
            $conditions = [];

            // get field names and format as: fieldName=?
            foreach(array_keys($condition) as $k)
            {
                array_push($conditions, "$k=?");
            }

            // build the condition strings as single string
            $condString = implode(" AND ", $conditions);

            // concatenate condition into query
            $sql .= "WHERE {$condString}";
        }
         
        // Sort if required
        if (!empty($sort) && !empty($sortColumn))
            $sql .= " ORDER BY {$sortColumn} {$sort}";

        // Prepare query
        $sth = $this->PDO->prepare($sql);
 
        // bind condition values
        // $i = the parameter binding index
        if (!empty($condition))
        {
            $i = 1;

            foreach(array_values($condition) as $v)
            {
                $sth->bindValue($i, $v);
                $i++;
            }
        }
        
        // execute the query
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = ($singleRow) ? $sth->fetch(PDO::FETCH_ASSOC) : $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    }

    // Select rows having values found in haystack
    // $pdo         -> The connection object
    // $table       -> The name of the table we will retrieve the records from
    // $fields      -> The columns to retrieve
    // $haystack    -> The values to test the needle
    // $needle      -> The column to match
    //
    public function selectIn($pdo, $table, $fields = array(), $needle = "", $haystack = array())
    {
        // Stop execution if there is no connection object
        // or if haystack is empty
        if ($pdo == null || empty($haystack))
            die("Server Error");

        // If fields list is empty, defaults to select * (all)
        $field = (empty($fields)) ? "*" : implode(",", $fields);

        // create binding placeholders for haystacks
        $bindings = implode(",", array_fill(0, count($haystack), '?'));
        
        // prepare / build the query
        $sql = "SELECT $field FROM $table WHERE $needle IN ($bindings)";
        $sth = $pdo->prepare($sql);
        
        // bind the values from haystack onto the prepared query
        $paramIndex = 1;

        foreach($haystack as $h)
        {
            $sth->bindValue($paramIndex, $h);
            $paramIndex++;
        }

        // execute the query
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
        // Stop execution if there is no connection object
        if ($pdo == null || empty($condition))
            die("Server Error"); 

        $sql = "SELECT COUNT(*) FROM $table WHERE $haystack = '$needle'";
        $count = $pdo->query($sql)->fetchColumn();

        return !empty($count);
    }

    // Get just the specific column value in a table 
    // based on a condition then return it
    function getValue($table, $column, $condition = array())
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
  
        // build the condition, store them here as individual string
        $conditions = [];

        // get field names and format as: fieldName=?
        foreach (array_keys($condition) as $k) {
            array_push($conditions, "$k=?");
        }

        // build the condition strings as single string
        $condString = implode(" AND ", $conditions);

        // Create the query together with the sort mode
        // then execute the query
        $sql = "SELECT $column FROM $table WHERE $condString";
        $sth = $this->PDO->prepare($sql);
        
        // bind condition values
        // $i = the parameter binding index
        $i = 1;

        foreach (array_values($condition) as $v) 
        {
            $sth->bindValue($i, $v);
            $i++;
        }

        $sth->execute();

        // We expect to return a single value
        $result = $sth->fetchColumn();

        return $result ?? "";
    }

    // Clean up the the database table.
    // Removes all records and resets the autoincrement id
    function truncate($table)
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error"); 

        $sth = $this->PDO->prepare("TRUNCATE TABLE $table"); 
        $sth->execute();
    }

    // Count columns in a table.
    // Counts the ID column by default.
    public function count($pdo, $table, $condition = array(), $column = "id")
    {
        // Stop execution if there is no connection object
        if ($pdo == null || empty($condition))
            die("Server Error");

        // build the condition, store them here as individual string
        $conditions = [];

        // get field names and format as: fieldName=?
        foreach (array_keys($condition) as $k) {
            array_push($conditions, "$k=?");
        }

        // build the condition strings as single string
        $condString = implode(" AND ", $conditions);

        $sql = "SELECT COUNT(?) FROM $table WHERE $condString";
        $sth = $pdo->prepare($sql);
        $sth->bindValue(1, $column);

        // bind condition values
        // $i = the parameter binding index

        $i = 2;

        foreach (array_values($condition) as $v) {
            $sth->bindValue($i, $v);
            $i++;
        }

        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $sth->fetchColumn();

        return $result ?? 0;
    }
    //
    // Fetch multiple rows from a given query
    //
    public function queryFetchAll($pdo, $sql)
    {
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");
 
        $sth = $pdo->prepare($sql);
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    } 

    public function queryFetch($pdo, $sql)
    {
        // Stop execution if there is no connection object
        if ($pdo == null)
            die("Server Error");
 
        $sth = $pdo->prepare($sql);
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        return $result ?? [];
    } 
    //
    // Execute specific sql query
    //
    public function query($sql)
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
 
        $sth = $this->PDO->prepare($sql);
        return $sth->execute(); 
    } 
    //
    // Generate positional parameters (?) for binding
    //
    public function generateBinders(array $array)
    {
        return implode(",", array_fill(0, count($array), '?'));
    }

    //============================================//
    //              REVISED FUNCTIONS             //
    //============================================//

    // Insert multiple rows at once
    public function insertMany($table, $fields = array(), $values = array())
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");

        // Create The final query;
        // Add a semi-colon prefix to each fieldnames. Preg_Filter does this using /^/ anchor.
        $sql = "INSERT INTO $table(" . implode(",", $fields) . ") VALUES(" . implode(",", preg_filter('/^/', ':', $fields)) . ")";

        // Prepare $pdo
        $sth = $this->PDO->prepare($sql);

        // finally execute the query and bind the values 
        foreach ($values as $data) {

            $sth->execute($data);
        }
    }

    /**
     * Insert a new record into database
     */
    public function save($table, $data = array())
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
    
        $colNames   = implode(",", array_keys($data));
        $values     = Utils::prefixJoin(":", array_keys($data));

        $sql = "INSERT INTO $table($colNames) VALUES($values)";

        $sth = $this->PDO->prepare($sql);
        $sth->execute($data); 
    }

    /**
     * Fetch rows from database according to given query.
     * 
     * @param bool $singleRow Force return a single row
     */
    public function fetchAll(string $sql, bool $singleRow = false)
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
 
        $sth = $this->PDO->prepare($sql);
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $singleRow ? $sth->fetch(PDO::FETCH_ASSOC) : $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    } 

    public function fetchAllParam(string $sql, array $params, bool $singleRow = false)
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
 
        $sth = $this->PDO->prepare($sql);

        $i = 1;
        foreach ($params as $v)
        {
            $sth->bindValue($i, $v);
            $i++;
        }
        
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $singleRow ? $sth->fetch(PDO::FETCH_ASSOC) : $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    }

    public function selectCols(array $cols, string $table, bool $singleRow = false, $order = "ASC", $by = "id")
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
 
        $fields = implode(",", $cols);

        $order = (!empty($order) && !empty($by)) ? "ORDER BY $by $order" : "";

        $sth = $this->PDO->prepare("SELECT $fields FROM $table $order");
        $sth->execute();

        // We expect to return an array containing the records found in database.
        $result = $singleRow ? $sth->fetch(PDO::FETCH_ASSOC) : $sth->fetchAll(PDO::FETCH_ASSOC);

        return $result ?? [];
    } 

    /**
     * Insert multiple values at once.
     * 
     * Sample input format : 
     * $arr1 = array([], [], []....) 
     * 
     * @param array $data Must be an array of indexed-arrays
     */
    public function insertRange(string $table, array $fieldNames, array $data)
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");

        // Generate positional parameters
        $bindings = $this->generateBinders($fieldNames);

        $sql = "INSERT INTO $table (" . implode(",", $fieldNames) . ") VALUES ($bindings)";
        $sth = $this->PDO->prepare($sql);
        
        foreach($data as $d)
        {
            $sth->execute($d);
        }
    }

    // Get just the specific column value in a table 
    // based on a condition then return it
    function selectValue($table, $column, $condition = array())
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");

        $condString = Utils::suffixJoin("=?", array_keys($condition));

        // Create the query together with the sort mode
        // then execute the query
        $sql = "SELECT $column FROM $table WHERE $condString";
        $sth = $this->PDO->prepare($sql);
        
        // bind condition values
        // $i = the parameter binding index
        $i = 1;

        foreach (array_values($condition) as $v) 
        {
            $sth->bindValue($i, $v);
            $i++;
        }

        $sth->execute();

        // We expect to return a single value
        $result = $sth->fetchColumn();

        return $result ?? "";
    }
    /**
     * Find the record with matching condition then return single row
     */
    public function findWhere($table, $condition)
    {
        // Stop execution if there is no connection object
        if ($this->PDO == null)
            die("Server Error");
 
        // build the condition as single string 
        $condString = Utils::suffixJoin("=?", array_keys($condition));
        
        $sql = "SELECT * FROM $table WHERE $condString";
        
        $sth = $this->PDO->prepare($sql);

        // bind condition values
        // $i = the parameter binding index
        $i = 1;

        foreach(array_values($condition) as $v)
        {
            $sth->bindValue($i, $v);
            $i++;
        }
 
        $sth->execute();

        // fetch single row
        // We expect to return an array containing the records found in database.
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        return $result ?? [];
    }
}
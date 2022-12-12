<?php 

class Helpers
{
    /**
     * Find the last inserted id into table.
     * $conn must be of type PDO.
     */
    public static function getLastId($conn, $table, $id = "id")
    {
        if ($conn == null)
            die("Something went wrong while generating form data.");

        $sql = "SELECT $id FROM $table ORDER BY $id DESC LIMIT 1"; 
        $result = $conn->query($sql)->fetch(PDO::FETCH_COLUMN);

        if ($result == null)
            $result = 0;

        return $result;
    }
}
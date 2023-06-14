<?php 

class SettingsIni
{
    public const settingsIniPath = BASE_DIR . "storage/settings/settings.ini";

    // Section Keys
    public $sect_General    = "General";
    public $sect_Database   = "Database";
    public $sect_SysInfo    = "SysInfo";

    // General Ini key names
    public $iniKey_RecordYear      = "record_year";
    public $iniKey_LockEditAfter   = "lock_edit_after_days";
    public $iniKey_DefaultDoctor   = "default_physician";
    public $iniKey_CheckupComplete = "checkup_form_on_complete";
    
    // Database Ini key names
    public $iniKey_Host            = "sql_host";
    public $iniKey_Uid             = "sql_uid";
    public $iniKey_Pass            = "sql_password";
    public $iniKey_DbName          = "sql_dbname";
    public $iniKey_Port            = "sql_port";

    // SysInfo Ini key names
    public $iniKey_AppVersion      = "app_version";
    
    /**
     * Read ini data from file
     */
    public function Read()
    {
        if (!file_exists(self::settingsIniPath))
        {
            die("Failed to load initialization data.");
        }

        $iniArray = parse_ini_file(self::settingsIniPath, true);

        return $iniArray ?? [];
    }
    /**
     * Write the ini data to file
     */
    public function Write($array, $file)
    {
        $res = array();
        
        foreach ($array as $key => $val) 
        {
            if (is_array($val)) 
            {
                $res[] = "[$key]";

                foreach ($val as $skey => $sval) $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
            } else $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
        }

        $this->safefilerewrite($file, implode("\r\n", $res));
    }

    private function safefilerewrite($fileName, $dataToSave)
    {
        if ($fp = fopen($fileName, 'w')) {
            $startTime = microtime(TRUE);
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite) usleep(round(rand(0, 100) * 1000));
            } while ((!$canWrite) and ((microtime(TRUE) - $startTime) < 5));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

    /**
     * Update an ini value in section
     */
    public function UpdateSection($section, $key, $value)
    {
        $ini = $this->Read();
        $ini[$section][$key] = $value;

        $this->Write($ini, self::settingsIniPath);
    }

    public function GetValue($section, $key)
    {
        $ini = $this->Read();
        
        return $ini[$section][$key];
    }
}

?>
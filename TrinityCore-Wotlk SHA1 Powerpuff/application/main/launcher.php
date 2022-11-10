<?php
header('Content-Type: application/json; charset=UTF-8');

class Launcher
{
    public static function NewDBConnection()
    {
        global $config;
        $connection = mysqli_connect(
            $config['mysqli']['launcher']['hostname'],
            $config['mysqli']['launcher']['user'],
            $config['mysqli']['launcher']['pass'],
            $config['mysqli']['launcher']['database'],
            $config['mysqli']['launcher']['port']
        );
        return $connection;
    }

    public static function GetDeletePatchList()
    {
        global $config;

        $mysqli = self::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT patch FROM deletepatch'))
            {
                $query->execute();
                $query->bind_result($patch);
                $query->store_result();

                $jsonArray = array();

                while ($query->fetch()) 
                {
                    $rowArray['patch'] = $patch;

                    array_push($jsonArray, $rowArray);
                }
                echo json_encode($jsonArray, JSON_PRETTY_PRINT);
                $query->close();
            }
            mysqli_close($mysqli);
        }
    }
}

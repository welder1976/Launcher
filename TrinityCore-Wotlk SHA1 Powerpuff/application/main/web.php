<?php
header('Content-Type: application/json; charset=UTF-8');

class Web
{
    public static function NewDBConnection()
    {
        global $config;
        $connection = mysqli_connect(
            $config['mysqli']['web']['hostname'],
            $config['mysqli']['web']['user'],
            $config['mysqli']['web']['pass'],
            $config['mysqli']['web']['database'],
            $config['mysqli']['web']['port']
        );
        return $connection;
    }

    public static function GetAccountBalance($user, $pass)
    {
        if (auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $mysqli = self::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($query = $mysqli->prepare('SELECT dp, vp FROM `account` WHERE `id` = ?'))
            {
                $query->bind_param('i', $accountId);
                $query->execute();
                $query->bind_result($dp, $vp);
                $query->store_result();

                $jsonArray = array();

                if($query->num_rows === 0)
                {
                    $row_array['dp'] = 0;
                    $row_array['vp'] = 0;
                    array_push($jsonArray, $row_array);
                }
                else
                {
                    while ($query->fetch()) 
                    {
                        $row_array['dp'] = $dp;
                        $row_array['vp'] = $vp;
                        array_push($jsonArray, $row_array);
                    }
                }
                $query->free_result();
                $query->close();
            }

            mysqli_close($mysqli);

            echo json_encode($jsonArray, JSON_PRETTY_PRINT);
        }
    }
}

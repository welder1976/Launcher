<?php
header('Content-Type: application/json; charset=UTF-8');

class Characters
{
    public static function NewDBConnection($realmId)
    {
        global $config;
        $connection = mysqli_connect(
            $config['mysqli']['realms'][$realmId]['hostname'],
            $config['mysqli']['realms'][$realmId]['user'],
            $config['mysqli']['realms'][$realmId]['pass'],
            $config['mysqli']['realms'][$realmId]['database'],
            $config['mysqli']['realms'][$realmId]['port']
        );
        return $connection;
    }

    public static function GetCharactersList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $final_array = array();

            foreach ($config['mysqli']['realms'] as $realm)
            {
                $mysqli = mysqli_connect($realm['hostname'],
                                         $realm['user'],
                                         $realm['pass'],
                                         $realm['database'],
                                         $realm['port']);

                mysqli_set_charset($mysqli, "utf8");

                if ($query = $mysqli->prepare('SELECT `name`, `gender`, `level`, `race`, `class` FROM `characters` WHERE `account`= ?'))
                {
                    $query->bind_param('i', $accountId);
                    $query->execute();
                    $query->bind_result($charName, $charGender, $charLevel, $charRace, $charClass);

                    $realmArray = array();
                    while ($query->fetch()) 
                    {
                        $row_array['realm']     = $realm['name'];
                        $row_array['name']      = $charName;
                        $row_array['gender']    = $charGender;
                        $row_array['level']     = $charLevel;
                        $row_array['race']      = $charRace;
                        $row_array['class']     = $charClass;
                        
                        array_push($realmArray, $row_array);
                    }

                    if (!empty($realmArray))
                        array_push($final_array, $realmArray);

                    $query->close();
                }
            }
            
            if (!empty($final_array))
            {
                echo json_encode($final_array, JSON_PRETTY_PRINT);
            }
        }
    }

    public static function GetRealmCharactersList($user, $pass, $realmid)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $final_array = array();
            
            if (array_key_exists($realmid, $config['mysqli']['realms']))
            {
                $mysqli = mysqli_connect($config['mysqli']['realms'][$realmid]['hostname'],
                                        $config['mysqli']['realms'][$realmid]['user'],
                                        $config['mysqli']['realms'][$realmid]['pass'],
                                        $config['mysqli']['realms'][$realmid]['database'],
                                        $config['mysqli']['realms'][$realmid]['port']);

                mysqli_set_charset($mysqli, "utf8");

                if ($query = $mysqli->prepare('SELECT `name`, `gender`, `level`, `race`, `class` FROM `characters` WHERE `account`= ?'))
                {
                    $query->bind_param('i', $accountId);
                    $query->execute();
                    $query->bind_result($charName, $charGender, $charLevel, $charRace, $charClass);

                    $realmArray = array();
                    while ($query->fetch()) 
                    {
                        $row_array['realm']     = $config['mysqli']['realms'][$realmid]['name'];
                        $row_array['name']      = $charName;
                        $row_array['gender']    = $charGender;
                        $row_array['level']     = $charLevel;
                        $row_array['race']      = $charRace;
                        $row_array['class']     = $charClass;

                        array_push($realmArray, $row_array);
                    }

                    if (!empty($realmArray))
                        array_push($final_array, $realmArray);

                    $query->close();
                }
            }

            if (!empty($final_array))
            {
                echo json_encode($final_array, JSON_PRETTY_PRINT);
            }
        }
    }
    
    public static function GetTopPvPList($limit)
    {
        global $config;

        $final_array = array();

        foreach ($config['mysqli']['realms'] as $realm)
        {
            $mysqli = mysqli_connect($realm['hostname'],
                                     $realm['user'],
                                     $realm['pass'],
                                     $realm['database'],
                                     $realm['port']);

            mysqli_set_charset($mysqli, "utf8");

            if ($query = $mysqli->prepare('SELECT `name`, `arenaPoints`, `totalHonorPoints`, `totalKills` 
                FROM `characters` WHERE `totalKills` != 0 ORDER BY `totalKills` DESC LIMIT ?'))
            {
                $query->bind_param('i', $limit);
                $query->execute();
                $query->bind_result($charName, $arenaPoints, $totalHonorPoints, $totalKills);

                $realmArray = array();

                while ($query->fetch()) 
                {
                    $row_array['name'] = $charName;
                    $row_array['arenaPoints'] = $arenaPoints;
                    $row_array['totalHonorPoints'] = $totalHonorPoints;
                    $row_array['totalKills'] = $totalKills;
                    $row_array['realm'] = $realm['name'];
                    
                    array_push($realmArray, $row_array);
                }

                if (!empty($realmArray))
                {
                    array_push($final_array, $realmArray);
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
        }

        if (!empty($final_array))
        {
            echo json_encode($final_array, JSON_PRETTY_PRINT);
        }
    }
    
    public static function GetOnlinePlayersList()
    {
        global $config;

        $final_array = array();

        if ($config['onlinePlayers']['enable'])
        {
            $limit = 2147483647;

            if ($config['onlinePlayers']['limit'] != 0)
            {
                $limit = $config['onlinePlayers']['limit'];
            }

            foreach ($config['mysqli']['realms'] as $realm)
            {
                $mysqli = mysqli_connect($realm['hostname'],
                                        $realm['user'],
                                        $realm['pass'],
                                        $realm['database'],
                                        $realm['port']);

                mysqli_set_charset($mysqli, "utf8");

                if ($query = $mysqli->prepare('SELECT `name`, `level`, `race`, `gender`, `class` 
                    FROM `characters` WHERE `online` != 0 LIMIT ?'))
                {
                    $query->bind_param('i', $limit);
                    $query->execute();
                    $query->bind_result($charName, $charLevel, $charRace, $charGender, $charClass);

                    $realmArray = array();

                    while ($query->fetch()) 
                    {
                        $row_array['name'] = $charName;
                        $row_array['level'] = $charLevel;
                        $row_array['race'] = $charRace;
                        $row_array['gender'] = $charGender;
                        $row_array['class'] = $charClass;
                        $row_array['realm'] = $realm['name'];
                        
                        array_push($realmArray, $row_array);
                    }

                    if (!empty($realmArray))
                    {
                        array_push($final_array, $realmArray);
                    }
                    $query->close();
                }
                // else
                // {
                    // echo("Error description: " . $mysqli->error);
                // }
            }

            if (!empty($final_array))
            {
                echo json_encode($final_array, JSON_PRETTY_PRINT);
            }
        }
    }
}

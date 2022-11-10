<?php
header('Content-Type: application/json; charset=UTF-8');

class Avatars
{
    public static function GetDBAvatars($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $avatarsArray = array();

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT `id`, `name`, `url` FROM `avatars_list` ORDER BY `id` DESC'))
                {
                    $query->execute();
                    $query->bind_result($avatarId, $avatarName, $avatarUrl);

                    while ($query->fetch()) 
                    {
                        $row_array['id'] = $avatarId;
                        $row_array['name'] = $avatarName;
                        $row_array['url'] = $avatarUrl;

                        array_push($avatarsArray, $row_array);
                    }
                    $query->close();
                }
                mysqli_close($mysqli);
            }

            if (!empty($avatarsArray))
            {
                echo json_encode($avatarsArray, JSON_PRETTY_PRINT);
            }
        }
    }
    
    public static function GetAccountIdAvatarUrl($user, $pass, $md5secpa, $accountName)
    {
        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            $accountId = Auth::GetAccountId($accountName);

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT `avatar_url` FROM `user_avatars` WHERE `account_id`= ?'))
                {
                    $query->bind_param('i', $accountId);
                    $query->execute();
                    $query->bind_result($avatarUrl);
    
                    while ($query->fetch()) 
                    {
                        return $avatarUrl;
                    }
                    $query->close();
                }
                mysqli_close($mysqli);
            }
        }
    }
    
    public static function GetSelfAvatar($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;
            $jsonObj = new \stdClass();
            $jsonObj->avatarUrl = '';

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            $accountId = Auth::GetAccountId($user);

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT `avatar_url` FROM `user_avatars` WHERE `account_id`= ?'))
                {
                    $query->bind_param('i', $accountId);
                    $query->execute();
                    $query->bind_result($avatarUrl);
    
                    while ($query->fetch()) 
                    {
                        $jsonObj->avatarUrl = $avatarUrl;
                    }
                    $query->close();
                }
                mysqli_close($mysqli);
                echo json_encode($jsonObj, JSON_PRETTY_PRINT);
            }
        }
    }
    
    public static function SetSelfAvatar($user, $pass, $db_avatar_url)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            if(substr($db_avatar_url, 0, 27) === "/Nighthold Launcher;component/")
            {
                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('REPLACE INTO user_avatars (account_id, avatar_url) VALUES (?, ?)'))
                    {
                        $query->bind_param('is', $accountId, $db_avatar_url);
                        $query->execute();
                        $query->close();
                    }
                    mysqli_close($mysqli);
                }
            }
            else
            {
                $mysqliLauncher = Launcher::NewDBConnection();

                mysqli_set_charset($mysqliLauncher, "utf8");

                if ($mysqliLauncher == true)
                {
                    // good way to check if the user doesn't set an outside avatar url that doesn't exist in our db
                    if ($queryLauncher = $mysqliLauncher->prepare('SELECT `id`, `name`, `url` FROM `avatars_list` WHERE `url`=?'))
                    {
                        $queryLauncher->bind_param('s', $db_avatar_url);
                        $queryLauncher->execute();
                        $queryLauncher->bind_result($avatarId, $avatarName, $avatarUrl);

                        $queryLauncher->store_result();

                        while ($queryLauncher->fetch()) 
                        {
                            if ($db_avatar_url == $avatarUrl)
                            {
                                $mysqliAuth = Auth::NewDBConnection();

                                mysqli_set_charset($mysqliAuth, "utf8");

                                if ($mysqliAuth == true)
                                {
                                    if ($query2 = $mysqliAuth->prepare('REPLACE INTO user_avatars (account_id, avatar_url) VALUES (?, ?)'))
                                    {
                                        $query2->bind_param('is', $accountId, $db_avatar_url);
                                        $query2->execute();
                                        $query2->close();
                                    }
                                    // else
                                    // {
                                        // echo("Error description: " . $mysqliAuth->error);
                                    // }
                                    mysqli_close($mysqliAuth);
                                }
                            }
                        }
                        $queryLauncher->close();
                    }
                    mysqli_close($mysqliLauncher);
                }
            }
        }
    }
}

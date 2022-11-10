<?php
header('Content-Type: application/json; charset=UTF-8');

class CiSessions
{
    private static function CanAccessSessionsList($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['sessions_list']);
    }
    
    public static function PingMeAlive($user)
    {
        if (!empty($user))
        {
            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('REPLACE INTO `ci_sessions` (`account_name`, `last_session_id`, `last_ip`, `last_seen`) VALUES (?, ?, ?, CURRENT_TIMESTAMP())'))
                {
                    $sid = session_id();
                    $sip = $_SERVER['REMOTE_ADDR'];

                    $query->bind_param('sss', $user, $sid, $sip);
                    $query->execute();

                    if ($mysqli->affected_rows != 0)
                    {
                        echo "Ping me alive success. ";
                    }
                    else
                    {
                        echo "Ping me alive did not have any effect.";
                    }
                    $query->close();
                }
                mysqli_close($mysqli);
            }
        }
    }
    
    public static function GetActiveSessionsList($user, $pass, $md5secpa)
    {
        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $accountId = Auth::GetAccountId($user);

            if (self::CanAccessSessionsList($accountId, -2))
            {
                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('SELECT
                        account_name,
                        last_session_id,
                        last_ip,
                        last_seen
                        FROM ci_sessions
                        WHERE UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_seen) <= 30'))
                    {
                        $query->execute();
                        $query->bind_result($account_name, $last_session_id, $last_ip, $last_seen);

                        $jsonArray = array();

                        while ($query->fetch()) 
                        {
                            $rowArray['avatar_url'] = Avatars::GetAccountIdAvatarUrl($user, $pass, $md5secpa, $account_name);
                            $rowArray['account_id'] = Auth::GetAccountId($account_name);
                            $rowArray['account_name'] = $account_name;
                            $rowArray['last_session_id'] = $last_session_id;
                            $rowArray['last_ip'] = $last_ip;
                            $rowArray['last_seen'] = $last_seen;

                            array_push($jsonArray, $rowArray);
                        }
                        echo json_encode($jsonArray, JSON_PRETTY_PRINT);
                        $query->close();
                    }
                    // else
                    // {
                        // echo("Error description: " . $mysqli->error);
                    // }
                    mysqli_close($mysqli);
                }
            }
        }
    }
}
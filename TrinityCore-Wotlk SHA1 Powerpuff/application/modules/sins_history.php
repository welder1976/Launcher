<?php
header('Content-Type: application/json; charset=UTF-8');

class SinsHistory
{
    public static function GetSinsHistory($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $sinsArray = array();

            // account bans array
            $row_array['accBanLogs'] = array();
            $row_array['accBanLogs'] = self::GetAccountBansHistoryArr($user);

            // character bans array
            $row_array['charBanLogs'] = array();
            $row_array['charBanLogs'] = self::GetCharacterBansHistoryArr($user);

            // mute array

            array_push($sinsArray, $row_array);

            echo json_encode($sinsArray, JSON_PRETTY_PRINT);
        }
    }

    private static function GetAccountBansHistoryArr($accountName)
    {
        $row_array['accBanLogs'] = array();

        $accountId = Auth::GetAccountId($accountName);

        $mysqli = Auth::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT
                (SELECT `username` FROM account WHERE id = account_banned.id) AS accname,
                FROM_UNIXTIME(banDate, \'%d %b %Y %h:%i:%s\') as banDate,
                FROM_UNIXTIME(unbanDate, \'%d %b %Y %h:%i:%s\') as unbanDate,
                CONCAT(
                    FLOOR((unbanDate-banDate)/60/60/24), \'d \',
                    MOD(HOUR(SEC_TO_TIME(unbanDate-banDate)), 24), \'h \',
                    MINUTE(SEC_TO_TIME(unbanDate-banDate)), \'m \',
                    second(SEC_TO_TIME(unbanDate-banDate)), \'s\'
                ) as duration,
                account_banned.bannedby,
                account_banned.banreason
                FROM account_banned
                LEFT JOIN account ON account_banned.id = account.id WHERE account_banned.id = ?
                ORDER BY account_banned.bandate DESC'))
            {
                $query->bind_param('i', $accountId);
                $query->execute();
                $query->bind_result($accName, $banDate, $unbanDate, $banDuration, $bannedBy, $banReason);

                while ($query->fetch()) 
                {
                    // $row_array2['accountName'] = $accName;
                    $row_array2['banDate'] = $banDate;
                    $row_array2['unbanDate'] = $unbanDate;
                    $row_array2['duration'] = $banDuration;
                    // $row_array2['bannedBy'] = $bannedBy;
                    $row_array2['reason'] = $banReason;

                    array_push($row_array['accBanLogs'], $row_array2);
                }
                $query->close();
            }
            mysqli_close($mysqli);
        }

        return $row_array['accBanLogs'];
    }

    private static function GetCharacterBansHistoryArr($accountName)
    {
        global $config;

        $accountId = Auth::GetAccountId($accountName);

        $row_array['charBanLogs'] = array();

        foreach ($config['mysqli']['realms'] as $realm)
        {
            $mysqli = mysqli_connect($realm['hostname'],
                                     $realm['user'],
                                     $realm['pass'],
                                     $realm['database'],
                                     $realm['port']);

            mysqli_set_charset($mysqli, "utf8");

            if ($query = $mysqli->prepare('SELECT
                characters.name,
                FROM_UNIXTIME(character_banned.bandate, \'%d %b %Y %h:%i:%s\') as banDate,
                CONCAT(
                    FLOOR((unbanDate-banDate)/60/60/24), \'d \',
                    MOD(HOUR(SEC_TO_TIME(unbanDate-banDate)), 24), \'h \',
                    MINUTE(SEC_TO_TIME(unbanDate-banDate)), \'m \',
                    second(SEC_TO_TIME(unbanDate-banDate)), \'s\'
                ) as duration,
                FROM_UNIXTIME(character_banned.unbandate, \'%d %b %Y %h:%i:%s\') as unbanDate,
                character_banned.banreason
                FROM character_banned
                LEFT JOIN characters ON character_banned.guid = characters.guid WHERE characters.account = ?'))
            {
                $query->bind_param('i', $accountId);
                $query->execute();
                $query->bind_result($charName, $charBanDate, $banDuration, $charUnbanDate, $charBanReason);

                while ($query->fetch()) 
                {
                    $row_array2['charName'] = $charName;
                    $row_array2['banDate'] = $charBanDate;
                    $row_array2['duration'] = $banDuration;
                    $row_array2['unbanDate'] = $charUnbanDate;
                    $row_array2['realm'] = $realm['name'];
                    $row_array2['reason'] = $charBanReason;

                    array_push($row_array['charBanLogs'], $row_array2);
                }

                $query->close();
            }
        }

        return $row_array['charBanLogs'];
    }
}

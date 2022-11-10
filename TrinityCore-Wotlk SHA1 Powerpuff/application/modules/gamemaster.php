<?php
header('Content-Type: application/json; charset=UTF-8');

class GameMaster
{
    public static function CanAccessGMPanel($user, $pass)
    {
        $jsonObj = new \stdClass();
        $jsonObj->canAccess = false;

        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;
            $accountId = Auth::GetAccountId($user);
            $jsonObj->canAccess = in_array(Auth::GetAccountRankId($accountId), $config['gmPermissions']['gm_panel']);
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    public static function CanAccessAdminPanel($user, $pass)
    {
        $jsonObj = new \stdClass();
        $jsonObj->canAccess = false;

        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;
            $accountId = Auth::GetAccountId($user);
            $jsonObj->canAccess = in_array(Auth::GetAccountRankId($accountId), $config['gmPermissions']['admin_panel']);
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    private static function CanReadTickets($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['tickets_list']);
    }
    
    private static function CanAccessBansList($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['bans_list']);
    }
    
    private static function CanAccessMuteLogs($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['mute_logs']);
    }
    
    private static function CanAccessShopLogs($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['shop_logs']);
    }
    
    private static function CanAccessPlayerInfo($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['player_info']);
    }
    
    private static function CanUnbanAccounts($accountId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['unban_accounts']);
    }
    
    private static function CanAccessSoapLogs($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['soap_logs']);
    }
    
    public static function GetTicketsList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $ticketArray = array();

            foreach ($config['mysqli']['realms'] as $realm)
            {
                $mysqli = mysqli_connect($realm['hostname'],
                                         $realm['user'],
                                         $realm['pass'],
                                         $realm['database'],
                                         $realm['port']);

                mysqli_set_charset($mysqli, "utf8");

                if (self::CanReadTickets($accountId, $realm['id']))
                {
                    if ($query = $mysqli->prepare('SELECT gm_ticket.id, characters.name, characters.online, characters.race, characters.class, characters.gender, 
                        gm_ticket.description, gm_ticket.createTime, gm_ticket.lastModifiedTime, (SELECT NAME FROM characters WHERE guid = gm_ticket.assignedto) AS AssignedTo,
                        gm_ticket.comment FROM gm_ticket LEFT JOIN characters ON gm_ticket.playerGuid = characters.guid WHERE gm_ticket.closedBy = 0 AND gm_ticket.completed = 0'))
                    {
                        $query->execute();
                        $query->bind_result($ticketId, $ticketName, $ticketOnline, $ticketRace, $ticketClass, $ticketGender, $ticketMessage, $ticketCreateTime, $ticketLastModified, $ticketAssignedTo, $ticketComment);

                        while ($query->fetch()) 
                        {
                            if ($ticketAssignedTo == null)
                                $ticketAssignedTo = "N/A";

                            $row_array['ticketId'] = $ticketId;
                            $row_array['ticketName'] = $ticketName;
                            $row_array['ticketOnline'] = $ticketOnline;
                            $row_array['ticketRace'] = $ticketRace;
                            $row_array['ticketClass'] = $ticketClass;
                            $row_array['ticketGender'] = $ticketGender;
                            $row_array['ticketMessage'] = $ticketMessage;
                            $row_array['ticketCreateTime'] = $ticketCreateTime;
                            $row_array['ticketLastModified'] = $ticketLastModified;
                            $row_array['ticketAssignedTo'] = $ticketAssignedTo;
                            $row_array['ticketComment'] = $ticketComment;
                            $row_array['ticketRealmName'] = $realm['name'];
                            $row_array['ticketRealmId'] = $realm['id'];

                            array_push($ticketArray, $row_array);
                        }
                        $query->close();
                    }
                }
                mysqli_close($mysqli);
            }

            if (!empty($ticketArray))
            {
                echo json_encode($ticketArray, JSON_PRETTY_PRINT);
            }
        }
    }
    
    private static function GetAccBansList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $bansArray = array();

            $mysqli = Auth::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if (self::CanAccessBansList($accountId, -2))
                {
                    if ($query = $mysqli->prepare('SELECT (SELECT `username` FROM account WHERE id = account_banned.id) AS accname, account_banned.bandate, account_banned.unbandate, account_banned.bannedby, account_banned.banreason
                        FROM account_banned LEFT JOIN account ON account_banned.id = account.id WHERE account_banned.active = 1 ORDER BY account_banned.bandate DESC'))
                    {
                        $query->execute();
                        $query->bind_result($accName, $banDate, $unbanDate, $bannedBy, $banReason);
    
                        while ($query->fetch()) 
                        {
                            $row_array['banType'] = 1;
                            $row_array['accOrCharName'] = $accName;
                            $row_array['banDate'] = $banDate;
                            $row_array['unbanDate'] = $unbanDate;
                            $row_array['bannedBy'] = $bannedBy;
                            $row_array['banReason'] = $banReason;

                            array_push($bansArray, $row_array);
                        }
                        $query->close();
                    }
                }
                mysqli_close($mysqli);
            }

            if (!empty($bansArray))
            {
                return $bansArray;
            }
        }
        return null;
    }
    
    private static function GetCharBansList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $bansArray = array();

            foreach ($config["mysqli"]['realms'] as $realm)
            {
                $mysqli = mysqli_connect($realm['hostname'], 
                                        $realm['user'], 
                                        $realm['pass'], 
                                        $realm['database'], 
                                        $realm['port']);

                mysqli_set_charset($mysqli, "utf8");

                if (self::CanAccessBansList($accountId, $realm['id']))
                {
                    if ($query = $mysqli->prepare('SELECT (SELECT `name` FROM characters WHERE guid = character_banned.guid)  AS charname, character_banned.bandate, character_banned.unbandate, 
                        character_banned.bannedby, character_banned.banreason FROM character_banned LEFT JOIN characters ON character_banned.guid = characters.guid WHERE character_banned.active = 1
                        ORDER BY character_banned.bandate DESC'))
                    {
                        $query->execute();
                        $query->bind_result($charName, $banDate, $unbanDate, $bannedBy, $banReason);

                        while ($query->fetch()) 
                        {
                            $row_array['banType'] = 2;
                            $row_array['accOrCharName'] = $charName;
                            $row_array['banDate'] = $banDate;
                            $row_array['unbanDate'] = $unbanDate;
                            $row_array['bannedBy'] = $bannedBy;
                            $row_array['banReason'] = $banReason;
                            $row_array['realmName'] = $realm['name'];
                            $row_array['realmId'] = $realm['id'];

                            array_push($bansArray, $row_array);
                        }
                        $query->close();
                    }
                }
                mysqli_close($mysqli);
            }

            if (!empty($bansArray))
            {
                return $bansArray;
            }
        }
        return null;
    }
    
    public static function GetAllBansList($user, $pass)
    {
        $allBansArray = array();
        array_push($allBansArray, self::GetAccBansList($user, $pass));
        array_push($allBansArray, self::GetCharBansList($user, $pass));
        echo json_encode($allBansArray, JSON_PRETTY_PRINT);
    }
    
    public static function GetMuteLogs($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $mutesArray = array();

            $mysqli = Auth::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if (self::CanAccessMuteLogs($accountId, -2))
                {
                    if ($query = $mysqli->prepare('SELECT account.username, account_muted.mutedate, account_muted.mutedate + (account_muted.mutetime*60) as unmuteDate, 
                        account_muted.mutetime, account_muted.mutedby, account_muted.mutereason FROM account_muted LEFT JOIN account ON account.id = account_muted.guid ORDER BY mutedate DESC'))
                    {
                        $query->execute();
                        $query->bind_result($username, $muteDate, $unmuteDate, $muteTime, $mutedBy, $muteReason);

                        while ($query->fetch()) 
                        {
                            $row_array['username'] = $username;
                            $row_array['muteDate'] = $muteDate;
                            $row_array['unmuteDate'] = $unmuteDate;
                            $row_array['muteTime'] = $muteTime;
                            $row_array['mutedBy'] = $mutedBy;
                            $row_array['muteReason'] = $muteReason;

                            array_push($mutesArray, $row_array);
                        }
                        $query->close();
                        echo json_encode($mutesArray, JSON_PRETTY_PRINT);
                    }
                }
                mysqli_close($mysqli);
            }
        }
    }

    public static function GetSoapLogs($user, $pass, $md5secpa)
    {
        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $logsArray = array();

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if (self::CanAccessSoapLogs($accountId, -2))
                {

                    if ($query = $mysqli->prepare('SELECT id, account_id, account_name, date, realm_id, command FROM soap_logs ORDER BY date DESC'))
                    {
                        $query->execute();
                        $query->bind_result($cID, $cAccountID, $cAccountName, $cDate, $cRealmId, $cCommand);

                        while ($query->fetch()) 
                        {
                            if (array_key_exists($cRealmId, $config["mysqli"]['realms']))
                            {
                                $row_array['id'] = $cID;
                                $row_array['accountId'] = $cAccountID;
                                $row_array['accountName'] = $cAccountName;
                                $row_array['date'] = $cDate;
                                $row_array['realmName'] = $config["mysqli"]['realms'][$cRealmId]['name'];
                                $row_array['command'] = $cCommand;

                                array_push($logsArray, $row_array);
                            }
                        }

                        $query->close();
                        echo json_encode(Tools::convert_from_latin1_to_utf8_recursively($logsArray), JSON_PRETTY_PRINT);
                    }
                }
                mysqli_close($mysqli);
            }
        }
    }

    private static function GetAccountBanLogsArray($accountName)
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
                    $row_array2['accountName'] = $accName;
                    $row_array2['banDate'] = $banDate;
                    $row_array2['unbanDate'] = $unbanDate;
                    $row_array2['duration'] = $banDuration;
                    $row_array2['bannedBy'] = $bannedBy;
                    $row_array2['banReason'] = $banReason;

                    array_push($row_array['accBanLogs'], $row_array2);
                }
                $query->close();
            }
            mysqli_close($mysqli);
        }

        return $row_array['accBanLogs'];
    }
    
    private static function GetCharacterBanLogsArray($targetPlayerName, $realmId)
    {
        $row_array['charBanLogs'] = array();

        $mysqli = Characters::NewDBConnection($realmId);

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT
                FROM_UNIXTIME(banDate, \'%d %b %Y %h:%i:%s\') as banDate,
                FROM_UNIXTIME(unbanDate, \'%d %b %Y %h:%i:%s\') as unbanDate,
                CONCAT(
                    FLOOR((unbanDate-banDate)/60/60/24), \'d \',
                    MOD(HOUR(SEC_TO_TIME(unbanDate-banDate)), 24), \'h \',
                    MINUTE(SEC_TO_TIME(unbanDate-banDate)), \'m \',
                    second(SEC_TO_TIME(unbanDate-banDate)), \'s\'
                ) as duration,
                bannedBy,
                banReason
                FROM character_banned
                LEFT OUTER JOIN characters ON characters.guid = character_banned.guid 
                WHERE LOWER(characters.name) = LOWER(\''.$targetPlayerName.'\')'))
            {
                $query->execute();
                $query->bind_result($banDate, $unbanDate, $duration, $bannedBy, $banReason);
    
                while ($query->fetch()) 
                {
                    $row_array2['player'] = $targetPlayerName;
                    $row_array2['banDate'] = $banDate;
                    $row_array2['unbanDate'] = $unbanDate;
                    $row_array2['duration'] = $duration;
                    $row_array2['bannedBy'] = $bannedBy;
                    $row_array2['banReason'] = $banReason;
                    array_push($row_array['charBanLogs'], $row_array2);
                }
                $query->close();
            }
        }
        return $row_array['charBanLogs'];
    }
    
    public static function GetPlayerInfo($user, $pass, $targetPlayerName, $realmId)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $gmAccountId = Auth::GetAccountId($user);

            $pinfoArray = array();

            $mysqliChar = Characters::NewDBConnection($realmId);
			
			mysqli_set_charset($mysqliChar, "utf8");
			
            if ($mysqliChar == true)
            {
                if (self::CanAccessPlayerInfo($gmAccountId, -2))
                {
                    if ($query = $mysqliChar->prepare('SELECT
                        characters.account AS accountID,
                        characters.name AS playerName,
                        characters.race AS playerRace,
                        characters.class AS playerClass,
                        characters.gender AS playerGender,
                        characters.level AS playerLevel,
                        guild.name AS guildName,
                        CONCAT(
                            FLOOR((characters.totaltime)/60/60/24), \'d \',
                            MOD(HOUR(SEC_TO_TIME(characters.totaltime)), 24), \'h \',
                            MINUTE(SEC_TO_TIME(characters.totaltime)), \'m \',
                            second(SEC_TO_TIME(characters.totaltime)), \'s\'
                        ) as totalPlayedTime,
                        characters.online,
                        FROM_UNIXTIME(characters.logout_time, \'%d %b %Y at %H:%i:%s\') as lastLogout,
                        characters.money,
                        characters.arenaPoints,
                        characters.totalHonorPoints,
                        characters.totalKills,
                        characters.todayKills,
                        characters.yesterdayKills
                        FROM characters
                        LEFT JOIN guild_member ON characters.guid = guild_member.guid
                        LEFT OUTER JOIN guild ON guild_member.guildid = guild.guildid 
                        WHERE LOWER(characters.name) = LOWER(\''.$targetPlayerName.'\')'))
                    {
                        $query->execute();
                        $query->bind_result($accountId, $playerName, $playerRace, $playerClass, $playerGender, $playerLevel,
                            $guildName, $totalPlayedTime, $online, $lastLogout, $money, $arenaPoints, $totalHonorPoints, $totalKills, 
                            $todayKills, $yesterdayKills);

                        $query->store_result();

                        while ($query->fetch()) 
                        {
                            $row_array['accountName'] = Auth::GetAccountName($accountId);
                            $row_array['accountId'] = $accountId;
                            $row_array['accountRankColor'] = $config['GMRanks'][Auth::GetAccountRankId($accountId, -2)][0];
                            $row_array['accountRankName'] = $config['GMRanks'][Auth::GetAccountRankId($accountId, -2)][1];
                            $row_array['playerName'] = $playerName;
                            $row_array['playerRace'] = $playerRace;
                            $row_array['playerClass'] = $playerClass;
                            $row_array['playerGender'] = $playerGender;
                            $row_array['playerLevel'] = $playerLevel;
                            $row_array['guildName'] = $guildName;
                            $row_array['realmName'] = $config["mysqli"]['realms'][$realmId]['name'];
                            $row_array['realmId'] = $realmId;
                            $row_array['totalPlayedTime'] = $totalPlayedTime;
                            $row_array['online'] = $online;
                            $row_array['lastLogout'] = $lastLogout;
                            $row_array['money'] = $money;
                            $row_array['arenaPoints'] = $arenaPoints;
                            $row_array['totalHonorPoints'] = $totalHonorPoints;
                            $row_array['totalKills'] = $totalKills;
                            $row_array['todayKills'] = $todayKills;
                            $row_array['yesterdayKills'] = $yesterdayKills;

                            // account bans array
                            $row_array['accBanLogs'] = array();
                            $row_array['accBanLogs'] = self::GetAccountBanLogsArray($row_array['accountName']);

                            // character bans array
                            $row_array['charBanLogs'] = array();
                            $row_array['charBanLogs'] = self::GetCharacterBanLogsArray($targetPlayerName, $realmId);

                            // mute logs for this account
                            $mysqliAuth = Auth::NewDBConnection();
                            
                            if ($mysqliAuth == true)
                            {
                                if ($query3 = $mysqliAuth->prepare('SELECT 
                                    FROM_UNIXTIME(muteDate, \'%d %b %Y %h:%i:%s\') as muteDate,
                                    CONCAT(muteTime, \' minutes\') as muteTime, mutedBy, muteReason
                                    FROM account_muted WHERE guid = ?'))
                                {
                                    $query3->bind_param('i', $accountId);
                                    $query3->execute();
                                    $query3->bind_result($muteDate, $muteTime, $mutedBy, $muteReason);

                                    $row_array['muteLogs'] = array();

                                    $query3->store_result();

                                    while ($query3->fetch()) 
                                    {
                                        $row_array3['muteDate'] = $muteDate;
                                        $row_array3['muteTime'] = $muteTime;
                                        $row_array3['mutedBy'] = $mutedBy;
                                        $row_array3['muteReason'] = $muteReason;
                                        array_push($row_array['muteLogs'], $row_array3);
                                    }
                                    $query3->free_result();
                                    $query3->close();
                                }
                            }
                            // end of mute logs

                            // account vp and dp points
                            
                            $mysqliWeb = Auth::NewDBConnection();
                            
                            if ($mysqliWeb == true)
                            {
                                if ($query4 = $mysqliWeb->prepare('SELECT bonuses, votes FROM `account_donate` WHERE `id` = ?'))
                                {
                                    $query4->bind_param('i', $accountId);
                                    $query4->execute();
                                    $query4->bind_result($vp, $dp);

                                    $row_array['vpDP'] = array();

                                    $query4->store_result();

                                    while ($query4->fetch()) 
                                    {
                                        $row_array4['vp'] = $vp;
                                        $row_array4['dp'] = $dp;
                                        array_push($row_array['vpDP'], $row_array4);
                                    }
                                    $query4->free_result();
                                    $query4->close();
                                }
                            }
                            // end of mute logs

                            array_push($pinfoArray, $row_array);
                        }
                        $query->free_result();
                        $query->close();
                        echo json_encode($pinfoArray, JSON_PRETTY_PRINT);
                    }
                    // else
                    // {
                        // echo("Error description: " . $mysqliChar->error);
                    // }
                }
                mysqli_close($mysqliChar);
            }
        }
    }
    
    public static function GMRanks($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $ranksArray = array();

            foreach ($config['GMRanks'] as $gmRank => $gmRankArr)
            {
                $row_array['name'] = $gmRankArr[1];
                $row_array['rank'] = $gmRank;

                array_push($ranksArray, $row_array);
            }
            echo json_encode($ranksArray, JSON_PRETTY_PRINT);
        }
    }
    
    public static function GetRealms($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $realmsArray = array();

            foreach ($config["mysqli"]['realms'] as $realm)
            {
                $row_array['realmId'] = $realm['id'];
                $row_array['realmName'] = $realm['name'];

                array_push($realmsArray, $row_array);
            }
            echo json_encode($realmsArray, JSON_PRETTY_PRINT);
        }
    }
    
    public static function UnbanAccount($gmUser, $gmPass, $targetAcc)
    {
        if (Auth::IsValidLogin($gmUser, $gmPass))
        {
            global $config;

            $accountId = Auth::GetAccountId($gmUser);

            if (self::CanUnbanAccounts($accountId))
            {
                $mysqli = Auth::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    $query = $mysqli->prepare('UPDATE `account_banned` SET `active` = 0 WHERE `id` = ?');

                    assert($query);

                    $targetAccId = Auth::GetAccountId($targetAcc);

                    $query->bind_param('i', $targetAccId);
                    $query->execute();
                }
                mysqli_close($mysqli);
            }
        }
    }
}
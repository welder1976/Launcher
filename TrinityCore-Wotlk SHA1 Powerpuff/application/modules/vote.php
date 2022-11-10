<?php
header('Content-Type: application/json; charset=UTF-8');

class Vote
{
    private static function GetSiteCooldownByID($siteID)
    {
        $mysqli = Web::NewDBConnection();

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT (hour_interval*3600) FROM vote_sites WHERE id = ?'))
            {
                $query->bind_param('i', $siteID);
                $query->execute();
                $query->bind_result($CDSeconds);

                while ($query->fetch()) 
                {
                    return $CDSeconds;
                }
            }
            $query->close();
        }
        // else
        // {
            // echo("Error description: " . $mysqli->error);
        // }
        mysqli_close($mysqli);
        return 0;
    }

    private static function GetSitePointsAmountByID($siteID)
    {
        $mysqli = Web::NewDBConnection();

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT points_per_vote FROM vote_sites WHERE id = ?'))
            {
                $query->bind_param('i', $siteID);
                $query->execute();
                $query->bind_result($points);

                while ($query->fetch()) 
                {
                    return $points;
                }
            }
            $query->close();
        }
        // else
        // {
            // echo("Error description: " . $mysqli->error);
        // }
        mysqli_close($mysqli);
        return 0;
    }
    
    public static function GetAccountSiteIDCooldown($accountID, $siteID)
    {
        $mysqli = Web::NewDBConnection();

        $pSiteCD = self::GetSiteCooldownByID($siteID);

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT MAX(time), UNIX_TIMESTAMP() AS unixNow FROM vote_log WHERE user_id = ? AND vote_site_id = ?'))
            {
                $query->bind_param('ii', $accountID, $siteID);
                $query->execute();
                $query->bind_result($dateVoted, $unixNow);

                while ($query->fetch()) 
                {
                    if ($dateVoted != 0)
                    {
                        if (($unixNow - $dateVoted) >= $pSiteCD)
                        {
                            return 0;
                        }
                        else
                        {
                            return $pSiteCD - ($unixNow - $dateVoted);
                        }
                    }
                }

                $query->close();
            }
        }
        // else
        // {
            // echo("Error description: " . $mysqli->error);
        // }
        mysqli_close($mysqli);
        return 0;
    }
    
    public static function GetVotesList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $mysqli = Web::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");
    
            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT id, vote_sitename, vote_image, vote_url, points_per_vote FROM vote_sites'))
                {
                    $query->execute();
                    $query->bind_result($siteID, $siteName, $imageUrl, $voteUrl, $points);
                    $query->store_result();

                    $jsonArray = array();

                    while ($query->fetch()) 
                    {
                        $rowArray['siteID'] = $siteID;
                        $rowArray['siteName'] = $siteName;
                        $rowArray['cooldownSecLeft'] = self::GetAccountSiteIDCooldown(Auth::GetAccountId($user), $siteID);
                        $rowArray['imageUrl'] = $imageUrl;
                        $rowArray['voteUrl'] = $voteUrl;
                        $rowArray['points'] = $points;

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
    
    public static function RegisterVoteCooldown($accountID, $siteID, $points)
    {
        $mysqli = Web::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        $pIP = $_SERVER['REMOTE_ADDR'];

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('INSERT INTO `vote_log` (`vote_site_id`, `user_id`, `ip`, `time`) VALUES (?, ?, ?, UNIX_TIMESTAMP())'))
            {
                $query->bind_param('iis', $siteID, $accountID, $pIP);
                $query->execute();

                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                $query->close();
            }
        }
        mysqli_close($mysqli);
        return false;
    }
    
    public static function RewardAccountVotePoints($accountID, $points)
    {
        $mysqli = Web::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('UPDATE `account_data` SET vp = vp + ? WHERE id = ?'))
            {
                $query->bind_param('ii', $points, $accountID);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                $query->close();
            }   
        }
        mysqli_close($mysqli);
        return false;
    }
    
    public static function SelfVoteClick($user, $pass, $siteID)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Invalid authentification";
        $jsonObj->voteRegistered = false;

        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountID = Auth::GetAccountId($user);
            $points = self::GetSitePointsAmountByID($siteID);
            $cooldown = self::GetAccountSiteIDCooldown($accountID, $siteID);

            if ($cooldown <= 0)
            {
                if (self::RegisterVoteCooldown(Auth::GetAccountId($user), $siteID, $points))
                {
                    if (self::RewardAccountVotePoints($accountID, $points))
                    {
                        $jsonObj->responseMsg = "Vote registered, you earned ".$points.".";
                        $jsonObj->voteRegistered = true;
                    }
                    else
                    {
                        $jsonObj->responseMsg = "Vote not registered, no rows were affected for account.";
                    }
                }
                else
                {
                    $jsonObj->responseMsg = "Vote not registered, no rows were affected for cooldowns.";
                }
            }
            else
            {
                $jsonObj->responseMsg = "Vote not registered, cooldown left: ".$cooldown." seconds!";
            }
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
}

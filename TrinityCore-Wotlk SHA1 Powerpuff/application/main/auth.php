<?php
header('Content-Type: application/json; charset=UTF-8');

class Auth
{
    public static function NewDBConnection()
    {
        global $config;
        $connection = mysqli_connect(
            $config['mysqli']['auth']['hostname'],
            $config['mysqli']['auth']['user'],
            $config['mysqli']['auth']['pass'],
            $config['mysqli']['auth']['database'],
            $config['mysqli']['auth']['port']
        );
        return $connection;
    }
    
    public static function GetLoginResponse($user, $pass)
    {
        $jsonObj = new \stdClass();
        $jsonObj->username = 'unknown';
        $jsonObj->response = 'No login response';
        $jsonObj->logged = false;

        $testAuthConnection = self::NewDBConnection();

        mysqli_set_charset($testAuthConnection, "utf8");

        if (self::NewDBConnection())
        {
            mysqli_close($testAuthConnection);

            if (self::IsValidLogin($user, $pass))
            {
                $jsonObj->username = $user;
                $jsonObj->response = 'Ok';
                $jsonObj->logged = true;
            }
            else
            {
                $jsonObj->username = '';
                $jsonObj->response = 'Incorrect username/password';
                $jsonObj->logged = false;
            }
        }
        // else
        // {
            // echo mysqli_connect_errno() . ":" . mysqli_connect_error();
        // }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    public static function GetAccountRankName($user, $pass)
    {
        global $config;

        $jsonObj = new \stdClass();
        $jsonObj->rankColor = $config['GMRanks'][0][0];
        $jsonObj->rankName = $config['GMRanks'][0][1];

        if (self::IsValidLogin($user, $pass))
        {
            $mysqli = self::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            $accountId = self::GetAccountId($user);

            if ($query = $mysqli->prepare('SELECT gmLevel FROM `account_access` WHERE ID = ?'))
            {
                $query->bind_param('i', $accountId);
                $query->execute();
                $query->bind_result($gmLevel);
                
                while ($query->fetch())
                {
                    if (array_key_exists($gmLevel, $config['GMRanks']))
                    {
                        $jsonObj->rankColor = $config['GMRanks'][$gmLevel][0];
                        $jsonObj->rankName = $config['GMRanks'][$gmLevel][1];
                    }
                }
                $query->close();
            }
            mysqli_close($mysqli);
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }

    public static function GetAccountId($accountName)
    {
        global $config;

        $mysqli = self::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            $query = $mysqli->prepare('SELECT id FROM account WHERE username = ?');
            
            assert($query);
            
            $query->bind_param('s', $accountName);
            $query->execute();
            $query->bind_result($accountId);
            
            while ($query->fetch())
            {
                mysqli_close($mysqli);
                return $accountId;
            }
        }
        mysqli_close($mysqli);
        return 0;
    }

    public static function GetAccountName($accountId)
    {
        global $config;

        $mysqli = self::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            $query = $mysqli->prepare('SELECT username FROM account WHERE id = ?');

            assert($query);

            $query->bind_param('i', $accountId);
            $query->execute();
            $query->bind_result($accountName);

            while ($query->fetch())
            {
                mysqli_close($mysqli);
                return $accountName;
            }
        }
        mysqli_close($mysqli);
        return 'Unknown';
    }
	
	public static function SetMotherBoard($Acc, $motherboard)
    {
        global $config;

        $mysqli = self::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            $query = $mysqli->prepare('UPDATE account set MotherBoard = ? WHERE id = ?');

            assert($query);
			
			$accId = self::GetAccountId($Acc);
			
            $query->bind_param('ii',$motherboard, $accId);
            $query->execute();
        }
        mysqli_close($mysqli);
    }

    public static function GetAccountRankId($accountId, $realmId = -2)
    {
        global $config;

        $mysqli = self::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            $query = $mysqli->prepare('SELECT gmLevel, RealmID FROM account_access WHERE ID = ?');

            assert($query);

            $query->bind_param('i', $accountId);
            $query->execute();
            $query->bind_result($rankId, $dbRealmId);

            while ($query->fetch())
            {
                if ($realmId == -2)
                {
                    return $rankId;
                }

                if ($dbRealmId == $realmId || $dbRealmId == -1)
                {
                    return $rankId;
                }
            }
        }
        mysqli_close($mysqli);
        return 0;
    }
    
    public static function IsValidLogin($user, $pass)
    {
        global $config;

        $mysqli = self::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($config['srp6'])
            {
                $query = $mysqli->prepare('SELECT username, salt, verifier FROM account WHERE username = ?');

                assert($query);

                $query->bind_param('s',$user);
                $query->execute();
                $query->bind_result($dbUsername, $salt, $verifier);

                while ($query->fetch())
                {
                    if (Srp6Lib::VerifySRP6Login($user, $pass, $salt, $verifier))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            }
            else
            {
                $query = $mysqli->prepare('SELECT username FROM account WHERE username = ? AND sha_pass_hash = ?');

                assert($query);

                $passhash = sha1(strtoupper($user.':'.$pass));

                $query->bind_param('ss', $user, $passhash);
                $query->execute();
                $query->bind_result($dbUsername);

                while ($query->fetch())
                {
                    if ($dbUsername)
                    {
                        return true;
                    }
                }
            }
        }
        mysqli_close($mysqli);
        return false;
    }
    
    public static function IsValidSecPa($user, $pass, $md5secpa)
    {
        global $config;

        if (self::IsValidLogin($user, $pass))
        {
            $mysqli = self::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                $query = $mysqli->prepare('SELECT username FROM account WHERE username = ? AND sec_pa = ?');

                assert($query);

                $query->bind_param('ss',$user, $md5secpa);
                $query->execute();
                $query->bind_result($dbUsername);

                while ($query->fetch())
                {
                    if ($dbUsername)
                    {
                        return true;
                    }
                }
            }
            mysqli_close($mysqli);
        }
        return false;
    }
    
    public static function GetSecPaResponse($user, $pass, $md5secpa)
    {
        global $config;
        $jsonObj = new \stdClass();
        $jsonObj->response = false;

        if (self::IsValidSecPa($user, $pass, $md5secpa))
        {
            $jsonObj->response  = true;
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    public static function GetAccountStanding($user, $pass)
    {
        $jsonObj = new \stdClass();
        $jsonObj->standing = 'Unknown';
        $jsonObj->banTimeLeft = '?';

        if (self::IsValidLogin($user, $pass))
        {
            global $config;

            $mysqli = self::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            $accountId = self::GetAccountId($user);

            if ($query = $mysqli->prepare('SELECT bandate, unbandate, UNIX_TIMESTAMP() as currTime FROM `account_banned` WHERE `active` = 1 AND id = ?'))
            {
                $query->bind_param('i', $accountId);
                $query->execute();
                $query->bind_result($banDate, $unbanDate, $currTime);

                $jsonObj->standing = 'Great';
                $jsonObj->banTimeLeft = 'None';

                while ($query->fetch()) 
                {
                    $seconds = intval($unbanDate - $currTime);
                    $days = floor($seconds / (24*60*60));
                    $hours = floor(($seconds - ($days*24*60*60)) / (60*60));
                    $minutes = floor(($seconds - ($days*24*60*60)-($hours*60*60)) / 60);
                    $seconds = ($seconds - ($days*24*60*60) - ($hours*60*60) - ($minutes*60)) % 60;
                    $banLeft = $days.' days, '.$hours.' hours, '.$minutes.' minutes, '.$seconds.' seconds';

                    $jsonObj->standing = 'Suspended';

                    if (intval($unbanDate - $currTime) < 0)
                    {
                        $jsonObj->banTimeLeft = 'Permanent';
                    }
                    else
                    {
                        $jsonObj->banTimeLeft = $banLeft;
                    }
                }
                $query->close();
            }
            mysqli_close($mysqli);
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
}

<?php
header('Content-Type: application/json; charset=UTF-8');

class CharactersMarket
{
    public static function GetCharacterInfo($guidOrName, $realmid)
    {
        global $config;
        $charJSON = array();

        $PSQL = 'SELECT * FROM characters WHERE guid = ?';
        if (!is_numeric($guidOrName)) {
            $PSQL = 'SELECT * FROM characters WHERE name = ?';
        }

        if (array_key_exists($realmid, $config['mysqli']['realms']))
        {
            $mysqli = Characters::NewDBConnection($realmid);

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare($PSQL))
                {
                    if (!is_numeric($guidOrName)) {
                        $query->bind_param('s', $guidOrName);
                    }
                    else {
                        $query->bind_param('i', $guidOrName);
                    }

                    $query->execute();
                    $res = $query->get_result();

                    $charJSON = $res->fetch_assoc();
                    $charJSON['realm_name'] = $config['mysqli']['realms'][$realmid]['name'];

                    $query->close();
                }
                /* 
                else
                {
                    echo("Error description: " . $mysqli->error);
                }
                */

                mysqli_close($mysqli);
            }
        }
        return json_encode($charJSON, JSON_PRETTY_PRINT);

        /* Example how to use:
            $json = self::GetCharacterInfo($guidOrName, $realmid);
            $charJSON = json_decode($json);
            $charJSON['name'] or $charJSON->name
        */
    }
    
    public static function GetOwnCharactersList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $accountId = Auth::GetAccountId($user);

            $final_array = array();

            foreach ($config['mysqli']['realms'] as $realm)
            {
                $mysqli = Characters::NewDBConnection($realm['id']);

                mysqli_set_charset($mysqli, "utf8");

                if ($query = $mysqli->prepare('SELECT guid, name, class, race, gender, level FROM characters WHERE `account`= ?'))
                {
                    $query->bind_param('i', $accountId);
                    $query->execute();
                    $query->bind_result($cGuid, $cName, $cClass, $cRace, $cGender, $cLevel);

                    $realmArray = array();
                    while ($query->fetch()) 
                    {
                        $row_array['guid']          = $cGuid;
                        $row_array['name']          = $cName;
                        $row_array['class']         = $cClass;
                        $row_array['race']          = $cRace;
                        $row_array['gender']        = $cGender;
                        $row_array['level']         = $cLevel;
                        $row_array['realm_id']      = $realm['id'];
                        $row_array['realm_name']    = $realm['name'];
                        
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

    public static function GetMarketList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");
    
            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT id, seller_account, guid, price_dp, realm_id FROM characters_market'))
                {
                    $query->execute();
                    $query->bind_result($id, $seller_account, $guid, $price_dp, $realmid);
                    $query->store_result();

                    $jsonArray = array();

                    while ($query->fetch()) 
                    {
                        $json = self::GetCharacterInfo($guid, $realmid);
                        $charData = json_decode($json);

                        if (!empty($charData))
                        {
                            $rowArray['market_id']      = $id;
                            $rowArray['guid']           = $charData->guid;
                            $rowArray['name']           = $charData->name;
                            $rowArray['race']           = $charData->race;
                            $rowArray['class']          = $charData->class;
                            $rowArray['gender']         = $charData->gender;
                            $rowArray['level']          = $charData->level;
                            $rowArray['price_dp']       = $price_dp;
                            $rowArray['realm_id']       = $realmid;
                            $rowArray['realm_name']     = $charData->realm_name;

                            array_push($jsonArray, $rowArray);
                        }
                    }

                    $query->close();

                    echo json_encode($jsonArray, JSON_PRETTY_PRINT);
                }
                /* 
                else
                {
                    echo("Error description: " . $mysqli->error);
                }
                */
                mysqli_close($mysqli);
            }
        }
    }

    public static function GetOwnListing($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountId = Auth::GetAccountId($user);

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");
    
            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT id, seller_account, guid, price_dp, realm_id FROM characters_market WHERE seller_account = ?'))
                {
                    $query->bind_param('i', $accountId);
                    $query->execute();
                    $query->bind_result($id, $seller_account, $guid, $price_dp, $realmid);
                    $query->store_result();

                    $jsonArray = array();

                    while ($query->fetch()) 
                    {
                        $json = self::GetCharacterInfo($guid, $realmid);
                        $charData = json_decode($json);

                        if (!empty($charData))
                        {
                            $rowArray['market_id']      = $id;
                            $rowArray['guid']           = $charData->guid;
                            $rowArray['name']           = $charData->name;
                            $rowArray['race']           = $charData->race;
                            $rowArray['class']          = $charData->class;
                            $rowArray['gender']         = $charData->gender;
                            $rowArray['level']          = $charData->level;
                            $rowArray['price_dp']       = $price_dp;
                            $rowArray['realm_id']       = $realmid;
                            $rowArray['realm_name']     = $charData->realm_name;

                            array_push($jsonArray, $rowArray);
                        }
                    }

                    $query->close();

                    echo json_encode($jsonArray, JSON_PRETTY_PRINT);
                }
                /* 
                else
                {
                    echo("Error description: " . $mysqli->error);
                }
                */
                mysqli_close($mysqli);
            }
        }
    }

    private static function GetUserDP($user_id)
    {
        $mysqli = Auth::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT bonuses FROM account_donate WHERE id = ?'))
            {
                $query->bind_param('i', $user_id);
                $query->execute();
                $query->bind_result($balance_dp);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $balance_dp;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }

        return 0;
    }

    private static function GetPriceDP($id)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT price_dp FROM characters_market WHERE id = ?'))
            {
                $query->bind_param('i', $id);
                $query->execute();
                $query->bind_result($price_dp);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $price_dp;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }
        
        return 0;
    }

    private static function RetrieveUserDP($user, $pass, $amount)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountId = Auth::GetAccountId($user);

            $mysqli = Auth::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('UPDATE account_donate SET bonuses = bonuses - ? WHERE id = ?'))
                {
                    $query->bind_param('ii', $amount, $accountId);
                    $query->execute();

                    if ($mysqli->affected_rows != 0)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                    $query->close();
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
            mysqli_close($mysqli);
        }
    }

    private static function RewardSellerWithDP($sellerAccountId, $amount)
    {
        $mysqli = Web::NewDBConnection();
    
        mysqli_set_charset($mysqli, "utf8");
    
        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('UPDATE bonuses SET bonuses = bonuses + ? WHERE id = ?'))
            {
                $query->bind_param('ii', $amount, $sellerAccountId);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                $query->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        mysqli_close($mysqli);
    }

    private static function GetMarketItemRealmId($id)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT realm_id FROM characters_market WHERE id = ?'))
            {
                $query->bind_param('i', $id);
                $query->execute();
                $query->bind_result($realm_id);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $realm_id;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }
        
        return 0;
    }

    private static function KickOnlineCharacter($guid, $realmid)
    {
        ob_start();

        global $config;

        $json = self::GetCharacterInfo($guid, $realmid);
        $charJSON = json_decode($json);
        $rowArray['name'] = $charJSON->name;
                        
        $json = SoapHandler::SendRequest($config['soap'][$realmid]['user'], 
            $config['soap'][$realmid]['pass'], 
                "kick ".$charJSON->name." Character Purchased from the Marketplace!!", $realmid);

        ob_end_clean();
            
        $transaction = json_decode($json);

        return $transaction->success;
    }

    private static function DeleteMarketGuid($guid, $realmId)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('DELETE FROM characters_market WHERE guid = ? and realm_id = ?'))
            {
                $query->bind_param('ii', $guid, $realmId);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                
                $query->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        mysqli_close($mysqli);
    }

    private static function TransferCharacterToAccount($guid, $realmid, $user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountId = Auth::GetAccountId($user);

            $mysqli = Characters::NewDBConnection($realmid);

            mysqli_set_charset($mysqli, "utf8");

            if (!self::IsCharacterOffline($guid, $realmid))
            {
                self::KickOnlineCharacter($guid, $realmid);
                sleep(5);
            }

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('UPDATE characters SET account = ? WHERE guid = ?'))
                {
                    $query->bind_param('ii', $accountId, $guid);
                    $query->execute();

                    if ($mysqli->affected_rows != 0)
                    {
                        self::DeleteMarketGuid($guid, $realmid);
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                    $query->close();
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
            mysqli_close($mysqli);
        }

        return false;
    }

    private static function IsCharacterOffline($guid, $realmid)
    {
        $mysqli = Characters::NewDBConnection($realmid);

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT * FROM characters WHERE guid = ? AND online = 0'))
            {
                $query->bind_param('i', $guid);
                $query->execute();

                while ($query->fetch()) 
                {
                    return true;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }
        return false;
    }

    private static function GetCharacterGuidByMarketId($id)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT guid FROM characters_market WHERE id = ?'))
            {
                $query->bind_param('i', $id);
                $query->execute();
                $query->bind_result($guid);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $guid;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }

        return 0;
    }
    
    private static function GetSellerAccountId($market_id)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT seller_account FROM characters_market WHERE id = ?'))
            {
                $query->bind_param('i', $market_id);
                $query->execute();
                $query->bind_result($sellerAccountId);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $sellerAccountId;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }

        return 0;
    }
    
    private static function IsMarketItemAvailable($market_id)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT guid FROM characters_market WHERE id = ?'))
            {
                $query->bind_param('i', $market_id);
                $query->execute();
                $query->bind_result($guid);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return true;
                }
                $query->close();
            }
            // else
            // {
                // echo("Error description: " . $mysqli->error);
            // }
            mysqli_close($mysqli);
        }

        return false;
    }

    private static function LogMarketPurchase($buyer_acc_id, $seller_acc_id, $market_id, $character_guid, $price_dp)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('INSERT INTO characters_market_logs (buyer_id, seller_id, market_id, character_guid, price_dp) VALUES (?, ?, ?, ?, ?)'))
            {
                $query->bind_param('iiiii', $buyer_acc_id, $seller_acc_id, $market_id, $character_guid, $price_dp);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                $query->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        mysqli_close($mysqli);
    }

    public static function PurchaseId($id, $user, $pass)
    {
        ob_start();
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Unable to purchase, try again later..";
        $jsonObj->response = false;

        $accountID = Auth::GetAccountId($user);

        if (self::IsMarketItemAvailable($id))
        {
            if (self::GetSellerAccountId($id) == $accountID)
            {
                $jsonObj->responseMsg = "Can't purchase own characters..";
                $jsonObj->response = false; 
            }
            else
            {
                if (Auth::IsValidLogin($user, $pass))
                {
                    if (self::GetUserDP($accountID) < self::GetPriceDP($id))
                    {
                        $jsonObj->responseMsg = "Not enough DP balance!!";
                        $jsonObj->response = false;
                    }
                    else
                    {
                        ob_end_clean();

                        $guid = self::GetCharacterGuidByMarketId($id);

                        $realmid = self::GetMarketItemRealmId($id);

                        if (self::RetrieveUserDP($user, $pass, self::GetPriceDP($id)))
                        {
                            $jsonObj->responseMsg = "Successfully spent ".self::GetPriceDP($id)." DP!";
                            $jsonObj->response = true;
                            
                            $seller_acc_id = self::GetSellerAccountId($id);
                            
                            self::LogMarketPurchase($accountID, $seller_acc_id, $id, self::GetCharacterGuidByMarketId($id), self::GetPriceDP($id));

                            // now reward the seller with DP
                            self::RewardSellerWithDP($seller_acc_id, self::GetPriceDP($id));

                            // now transfer it to the buyer
                            self::TransferCharacterToAccount($guid, $realmid, $user, $pass);
                        }
                        else
                        {
                            $jsonObj->responseMsg = "Not enough DP balance!";
                            $jsonObj->response = false;
                        }
                    }
                }
            }
        }
        else
        {
            $jsonObj->responseMsg = "Character no longer available..";
            $jsonObj->response = false; 
        }

        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    private static function IsCharacterOwner($user, $pass, $guid, $realmid)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountID = Auth::GetAccountId($user);

            $mysqli = Characters::NewDBConnection($realmid);

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT name FROM characters WHERE guid = ? AND account = ?'))
                {
                    $query->bind_param('ii', $guid, $accountID);
                    $query->execute();
                    $query->bind_result($charName);
                    $query->store_result();

                    while ($query->fetch()) 
                    {
                        return true;
                    }
                    $query->close();
                }
                // else
                // {
                    // echo("Error description: " . $mysqli->error);
                // }
                mysqli_close($mysqli);
            }
        }

        return false;
    }

    private static function RestoreCharacterToAccountOwner($guid, $ownerId, $realmid)
    {
        $mysqli = Characters::NewDBConnection($realmid);
    
        mysqli_set_charset($mysqli, "utf8");
    
        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('UPDATE characters SET account = ? WHERE guid = ?'))
            {
                $query->bind_param('ii', $ownerId, $guid);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                $query->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        mysqli_close($mysqli);
    }

    private static function TemporaryRemoveCharacterFromAccount($guid, $realmid)
    {
        $mysqli = Characters::NewDBConnection($realmid);
    
        mysqli_set_charset($mysqli, "utf8");
    
        if (!self::IsCharacterOffline($guid, $realmid))
        {
            self::KickOnlineCharacter($guid, $realmid);
            sleep(5);
        }
    
        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('UPDATE characters SET account = 0 WHERE guid = ?'))
            {
                $query->bind_param('i', $guid);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
                $query->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        mysqli_close($mysqli);
    }

    private static function AddCharacterToMarket($seller_acc_id, $guid, $price_dp, $realmid)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('INSERT INTO characters_market (seller_account, guid, price_dp, realm_id) VALUES (?, ?, ?, ?)'))
            {
                $query->bind_param('iiii', $seller_acc_id, $guid, $price_dp, $realmid);
                $query->execute();
    
                if ($mysqli->affected_rows != 0)
                {
                    self::TemporaryRemoveCharacterFromAccount($guid, $realmid);
                    return true;
                }
                else
                {
                    return false;
                }
                $query->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        mysqli_close($mysqli);
    }
    
    public static function ListCharacterForSale($user, $pass, $guid, $price_dp, $realmid)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Unable to list sale, try again later..";
        $jsonObj->response = false;

        if (Auth::IsValidLogin($user, $pass))
        {
            $seller_acc_id = Auth::GetAccountId($user);

            if (self::IsCharacterOwner($user, $pass, $guid, $realmid))
            {
                if ($price_dp <= 0)
                {
                    $jsonObj->responseMsg = "Price can't be 0 or less..";
                    $jsonObj->response = false;
                }
                else
                {
                    self::AddCharacterToMarket($seller_acc_id, $guid, $price_dp, $realmid);
                    $jsonObj->responseMsg = "Your character was enlisted for sale";
                    $jsonObj->response = true;
                }
            }
            else
            {
                $jsonObj->responseMsg = "You don't own that character..";
                $jsonObj->response = false;
            }
        }
        else
        {
            $jsonObj->responseMsg = "Unable to list sale, try again later..";
            $jsonObj->response = false;
        }

        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    public static function CancelCharacterSale($user, $pass, $guid, $realmid)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Unable to cancel character sale, try again later..";
        $jsonObj->response = false;

        if (Auth::IsValidLogin($user, $pass))
        {
            $seller_acc_id = Auth::GetAccountId($user);

            if (self::RestoreCharacterToAccountOwner($guid, $seller_acc_id, $realmid))
            {
                self::DeleteMarketGuid($guid, $realmid);
                $jsonObj->responseMsg = "Your character was restored";
                $jsonObj->response = true;
            }
            else
            {
                $jsonObj->responseMsg = "You don't own that character..";
                $jsonObj->response = false;
            }
        }
        else
        {
            $jsonObj->responseMsg = "Unable to cancel character sale, try again later..";
            $jsonObj->response = false;
        }

        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
}

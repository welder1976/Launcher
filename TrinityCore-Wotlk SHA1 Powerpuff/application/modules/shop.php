<?php
header('Content-Type: application/json; charset=UTF-8');

class Shop
{
    public static function GetShopList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            global $config;

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");
    
            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT id, title, description, img_url, price_dp, price_vp, category, soap_command, realm_id FROM shop_list'))
                {
                    $query->execute();
                    $query->bind_result($id, $title, $description, $img_url, $price_dp, $price_vp, $category, $soap_command, $realm_id);
                    $query->store_result();

                    $jsonArray = array();

                    while ($query->fetch()) 
                    {
                        $rowArray['id'] = $id;
                        $rowArray['title'] = $title;
                        $rowArray['description'] = $description;
                        $rowArray['img_url'] = $img_url;
                        $rowArray['price_dp'] = $price_dp;
                        $rowArray['price_vp'] = $price_vp;
                        $rowArray['category'] = $category;
                        // $rowArray['soap_command'] = $soap_command;
                        $rowArray['realmid'] = $realm_id;

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
    
    public static function GetPriceDP($id)
    {
        global $config;

        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT price_dp FROM shop_list WHERE id = ?'))
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

    public static function GetPriceVP($id)
    {
        global $config;

        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT price_vp FROM shop_list WHERE id = ?'))
            {
                $query->bind_param('i', $id);
                $query->execute();
                $query->bind_result($price_vp);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $price_vp;
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

    public static function GetUserDP($user_id)
    {
        global $config;

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

    public static function GetUserVP($user_id)
    {
        global $config;

        $mysqli = Web::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT votes FROM account_donate WHERE id = ?'))
            {
                $query->bind_param('i', $user_id);
                $query->execute();
                $query->bind_result($balance_vp);
                $query->store_result();

                $jsonArray = array();

                while ($query->fetch()) 
                {
                    return $balance_vp;
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
    
    public static function RetrieveUserDP($user, $pass, $amount)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountId = Auth::GetAccountId($user);

            global $config;

            $mysqli = Web::NewDBConnection();

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
    
    public static function RetrieveUserVP($user, $pass, $amount)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountId = Auth::GetAccountId($user);

            global $config;

            $mysqli = Web::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('UPDATE account_donate SET votes = votes - ? WHERE id = ?'))
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

    public static function GetShopItemCommand($id)
    {
        global $config;

        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT soap_command FROM shop_list WHERE id = ?'))
            {
                $query->bind_param('i', $id);
                $query->execute();
                $query->bind_result($soap_command);
                $query->store_result();

                while ($query->fetch()) 
                {
                    return $soap_command;
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

    public static function GetShopItemRealmId($id)
    {
        global $config;

        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT realm_id FROM shop_list WHERE id = ?'))
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

    public static function PurchaseId($user, $pass, $id, $byCurrencyType, $playerName = "Unknown", $accountName = "Unknown")
    {
        ob_start();
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Unable to purchase, try again later..";
        $jsonObj->response = false;

        $accountID = Auth::GetAccountId($user);
        $command = self::GetShopItemCommand($id);
        $command = str_replace("{PLAYER}", $playerName, $command);
        $command = str_replace("{ACCOUNT}", $accountName, $command);

        global $config;

        if ($byCurrencyType == 0) // by dp
        {
            if (self::GetUserDP($accountID) < self::GetPriceDP($id))
            {
                $jsonObj->responseMsg = "Not enough DP balance!!";
                $jsonObj->response = false;
            }
            else
            {
                $json = SoapHandler::SendRequest($config['soap'][self::GetShopItemRealmId($id)]['user'], $config['soap'][self::GetShopItemRealmId($id)]['pass'], $command, self::GetShopItemRealmId($id));
                $transaction = json_decode($json);

                ob_end_clean();

                if ($transaction->success)
                {
                    if (self::RetrieveUserDP($user, $pass, self::GetPriceDP($id)))
                    {
                        if ($transaction->success)
                        {
                            $jsonObj->responseMsg = "Transaction successfull, spent ".self::GetPriceDP($id)." DP!";
                            $jsonObj->response = true;
                        }
                    }
                    else
                    {
                        $jsonObj->responseMsg = "Not enough DP balance!";
                        $jsonObj->response = false;
                    }
                }
                else
                {
                    $jsonObj->responseMsg = "Server error: ".$transaction->responseMsg;
                    $jsonObj->response = false;
                }
            }
        }
        else // by vp
        {
            if (self::GetUserVP($accountID) < self::GetPriceVP($id))
            {
                $jsonObj->responseMsg = "Not enough VP balance!!";
                $jsonObj->response = false;
            }
            else
            {
                $json = SoapHandler::SendRequest($config['soap'][self::GetShopItemRealmId($id)]['user'], $config['soap'][self::GetShopItemRealmId($id)]['pass'], $command, self::GetShopItemRealmId($id));
                $transaction = json_decode($json);

                ob_end_clean();

                if ($transaction->success)
                {
                    if (self::RetrieveUserVP($user, $pass, self::GetPriceVP($id)))
                    {
                        if ($transaction->success)
                        {
                            $jsonObj->responseMsg = "Transaction successfull, spent ".self::GetPriceDP($id)." VP!";
                            $jsonObj->response = true;
                        }
                    }
                    else
                    {
                        $jsonObj->responseMsg = "Not enough VP balance!";
                        $jsonObj->response = false;
                    }
                }
                else
                {
                    $jsonObj->responseMsg = "Server error: ".$transaction->responseMsg;
                    $jsonObj->response = false;
                }
            }
        }

        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
}

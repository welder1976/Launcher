<?php
header('Content-Type: application/json; charset=UTF-8');

class News
{
    private static function CanAccessNewsManager($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['news_manager']);
    }
    
    private static function CanCreateArticles($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['news_create']);
    }
    
    private static function CanEditArticles($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['news_edit']);
    }
    
    private static function CanDeleteArticles($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['news_delete']);
    }
    
    public static function GetLatestNews($expansionId, $limit)
    {
        global $config;

        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT id, title, date, articleUrl, imageUrl, expansionID
                FROM news WHERE expansionID = '.$expansionId.' ORDER BY id DESC LIMIT '.$limit.''))
            {
                $query->execute();
                $query->bind_result($articleId, $articleTitle, $articleDate, $articleUrl, $imageUrl, $expID);

                $jsonArray = array();

                while ($query->fetch()) 
                {
                    $rowArray['articleId'] = $articleId;
                    $rowArray['articleTitle'] = $articleTitle;
                    $rowArray['articleDate'] = $articleDate;
                    $rowArray['articleUrl'] = $articleUrl;
                    $rowArray['imageUrl'] = $imageUrl;
                    $rowArray['expansionId'] = $expID;
                    
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
    
    public static function CreateArticle($user, $pass, $md5secpa, $expansionId, $newTitle, $newArticleUrl, $newImageUrl)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Invalid authentification";

        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $accountId = Auth::GetAccountId($user);

            if (self::CanAccessNewsManager($accountId, -2) && self::CanCreateArticles($accountId, -2))
            {
                global $config;

                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('INSERT INTO `news` (`title`, `articleUrl`, `imageUrl`, `expansionID`) VALUES (?, ?, ?, ?)'))
                    {
                        $query->bind_param('sssi', $newTitle, $newArticleUrl, $newImageUrl, $expansionId);
                        $query->execute();

                        if ($mysqli->affected_rows != 0)
                        {
                            $jsonObj->responseMsg = "Created new article on expansion id ".$expansionId.".";
                        }
                        else
                        {
                            $jsonObj->responseMsg = "No rows were affected, possible duplicates.";
                        }
                            
                        $query->close();
                    }
                    else
                    {
                        $jsonObj->responseMsg = "SQL Query Error: ".$mysqli->error;
                    }
                }
                else
                {
                    $jsonObj->responseMsg = "Could not connect to the database server!";
                }
                mysqli_close($mysqli);
            }
            else
            {
                $jsonObj->responseMsg = "No permission to create articles!";
            }
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    public static function EditArticle($user, $pass, $md5secpa, $articleId, $expansionId, $newTitle, $newArticleUrl, $newImageUrl)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Invalid authentification";

        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $accountId = Auth::GetAccountId($user);

            if (self::CanAccessNewsManager($accountId, -2) && self::CanEditArticles($accountId, -2))
            {
                global $config;

                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('UPDATE `news` SET `title`= ?, `articleUrl`= ?, `imageUrl`=?
                        WHERE `id`= ? AND `expansionID`= ?'))
                    {
                        $query->bind_param('sssii', $newTitle, $newArticleUrl, $newImageUrl, $articleId, $expansionId);
                        $query->execute();

                        if ($mysqli->affected_rows != 0)
                        {
                            $jsonObj->responseMsg = "Edited article id ".$articleId." on expansion id ".$expansionId.".";
                        }
                        else
                        {
                            $jsonObj->responseMsg = "No rows were affected, because that article was not found or data is the same.";
                        }

                        $query->close();
                    }
                    else
                    {
                        $jsonObj->responseMsg = "SQL Query Error: ".$mysqli->error;
                    }
                }
                else
                {
                    $jsonObj->responseMsg = "Could not connect to the database server!";
                }
                mysqli_close($mysqli);
            }
            else
            {
                $jsonObj->responseMsg = "No permission to edit articles!";
            }
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
    
    public static function DeleteArticle($user, $pass, $md5secpa, $articleId, $expansionId)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Invalid authentification";

        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $accountId = Auth::GetAccountId($user);

            if (self::CanAccessNewsManager($accountId, -2) && self::CanDeleteArticles($accountId, -2))
            {
                global $config;

                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('DELETE FROM `news` WHERE `id`= ? AND `expansionID`= ?'))
                    {
                        $query->bind_param('ii', $articleId, $expansionId);
                        $query->execute();

                        if ($mysqli->affected_rows != 0)
                        {
                            $jsonObj->responseMsg = "Deleted article id ".$articleId." on expansion id ".$expansionId.".";
                        }
                        else
                        {
                            $jsonObj->responseMsg = "No rows were affected, because that article was not found.";
                        }
                            
                        $query->close();
                    }
                    else
                    {
                        $jsonObj->responseMsg = "SQL Query Error: ".$mysqli->error;
                    }
                }
                else
                {
                    $jsonObj->responseMsg = "Could not connect to the database server!";
                }
                mysqli_close($mysqli);
            }
            else
            {
                $jsonObj->responseMsg = "No permission to delete articles!";
            }
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
}

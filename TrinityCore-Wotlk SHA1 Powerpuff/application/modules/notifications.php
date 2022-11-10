<?php
header('Content-Type: application/json; charset=UTF-8');

class Notifications
{
    private static function CanAccessNotificationsManager($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['notifications_manager']);
    }

    private static function CanCreateNotifications($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['notifications_create']);
    }

    private static function CanDeleteNotifications($accountId, $realmId)
    {
        global $config;
        return in_array(Auth::GetAccountRankId($accountId, $realmId), $config['gmPermissions']['notifications_delete']);
    }

    public static function CreateNotification($user, $pass, $md5secpa, $newSubject, $newMessage, $newImageUrl, $newRedirectUrl, $newAccountID)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Invalid authentification";

        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $accountId = Auth::GetAccountId($user);

            if (self::CanAccessNotificationsManager($accountId, -2) && self::CanCreateNotifications($accountId, -2))
            {
                global $config;

                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('INSERT INTO `notifications` (`subject`, `message`, `imageUrl`, `redirectUrl`, `accountID`) VALUES (?, ?, ?, ?, ?)'))
                    {
                        $query->bind_param('ssssi', $newSubject, $newMessage, $newImageUrl, $newRedirectUrl, $newAccountID);
                        $query->execute();

                        if ($mysqli->affected_rows != 0)
                        {
                            $jsonObj->responseMsg = "Created new notification.";
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
                $jsonObj->responseMsg = "No permission to create notifications!";
            }
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }

    public static function GetNotificationsList($user, $pass)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountID = Auth::GetAccountId($user);

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT id, subject, message, imageUrl, redirectUrl FROM notifications WHERE accountID = 0 OR accountID = ? ORDER BY id DESC'))
                {
                    $query->bind_param('i', $accountID);
                    $query->execute();
                    $query->bind_result($notificationID, $notificationSubject, $notificationMessage, $notificationImageUrl, $notificationRedirectUrl);
                    $query->store_result();

                    $jsonArray = array();

                    while ($query->fetch()) 
                    {
                        $rowArray['id'] = $notificationID;
                        $rowArray['subject'] = $notificationSubject;
                        $rowArray['message'] = $notificationMessage;
                        $rowArray['imageUrl'] = $notificationImageUrl;
                        $rowArray['redirectUrl'] = $notificationRedirectUrl;
                        $rowArray['isMarkedAsRead'] = self::IsNotificationRead($accountID, $notificationID);

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

    public static function GetNotificationsListAdmin($user, $pass, $md5secpa)
    {
        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if ($mysqli == true)
            {
                if ($query = $mysqli->prepare('SELECT id, subject, message, imageUrl, redirectUrl, accountID FROM notifications ORDER BY id DESC'))
                {
                    $query->execute();
                    $query->bind_result($notificationID, $notificationSubject, $notificationMessage, $notificationImageUrl, $notificationRedirectUrl, $accountID);
                    $query->store_result();

                    $jsonArray = array();

                    while ($query->fetch()) 
                    {
                        $rowArray['id'] = $notificationID;
                        $rowArray['subject'] = $notificationSubject;
                        $rowArray['message'] = $notificationMessage;
                        $rowArray['imageUrl'] = $notificationImageUrl;
                        $rowArray['redirectUrl'] = $notificationRedirectUrl;
                        if ($accountID != 0)
                        {
                            $rowArray['mention'] = "@".Auth::GetAccountName($accountID)." (".$accountID.")";
                        }
                        else
                        {
                            $rowArray['mention'] = null;
                        }

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

    public static function NotificationExists($notificationID)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT id FROM notifications WHERE id = ?'))
            {
                $query->bind_param('i', $notificationID);
                $query->execute();
                $query->bind_result($rID);
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

    public static function IsNotificationRead($accountID, $notificationID)
    {
        $mysqli = Launcher::NewDBConnection();

        mysqli_set_charset($mysqli, "utf8");

        if ($mysqli == true)
        {
            if ($query = $mysqli->prepare('SELECT notificationID FROM notifications_read WHERE notificationID = ? AND accountID = ?'))
            {
                $query->bind_param('ii', $notificationID, $accountID);
                $query->execute();
                $query->bind_result($rID);
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

    public static function MarkNotificationAsRead($user, $pass, $notificationID)
    {
        if (Auth::IsValidLogin($user, $pass))
        {
            $accountId = Auth::GetAccountId($user);

            $mysqli = Launcher::NewDBConnection();

            mysqli_set_charset($mysqli, "utf8");

            if (self::NotificationExists($notificationID))
            {
                if ($mysqli == true)
                {
                    if ($query = $mysqli->prepare('REPLACE INTO `notifications_read` (`accountID`, `notificationID`) VALUES (?, ?)'))
                    {
                        $query->bind_param('ii', $accountId, $notificationID);
                        $query->execute();

                        if ($mysqli->affected_rows != 0)
                        {
                            echo 'notification marked as read successfully';
                        }
                        $query->close();
                    }
                }
                mysqli_close($mysqli);
            }
        }
    }

    public static function DeleteNotification($user, $pass, $md5secpa, $notificationID)
    {
        $jsonObj = new \stdClass();
        $jsonObj->responseMsg = "Invalid authentification";

        if (Auth::IsValidLogin($user, $pass) && Auth::IsValidSecPa($user, $pass, $md5secpa))
        {
            $accountId = Auth::GetAccountId($user);

            if (self::CanAccessNotificationsManager($accountId, -2) && self::CanDeleteNotifications($accountId, -2))
            {
                global $config;

                $mysqli = Launcher::NewDBConnection();

                mysqli_set_charset($mysqli, "utf8");

                if ($mysqli == true)
                {
                    // Delete from notifications table
                    if ($query = $mysqli->prepare('DELETE FROM `notifications` WHERE id = ?'))
                    {
                        $query->bind_param('i', $notificationID);
                        $query->execute();

                        if ($mysqli->affected_rows != 0)
                        {
                            $jsonObj->responseMsg = "Deleted notification id ".$notificationID.".";
                        }
                        else
                        {
                            $jsonObj->responseMsg = "No rows were affected, because that notification was not found.";
                        }
                            
                        $query->close();
                    }
                    else
                    {
                        $jsonObj->responseMsg = "SQL Query Error: ".$mysqli->error;
                    }

                    // Delete from notifications_Read table
                    if ($query2 = $mysqli->prepare('DELETE FROM `notifications_read` WHERE notificationID = ?'))
                    {
                        $query2->bind_param('i', $notificationID);
                        $query2->execute();
                        $query2->close();
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
                $jsonObj->responseMsg = "No permission to delete notifications!";
            }
        }
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }
}

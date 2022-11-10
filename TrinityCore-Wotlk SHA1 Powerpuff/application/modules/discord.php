<?php

class Discord
{
    public static function NewIssueReport($reportBy, $launcherVersion, $issueAt, $issueMessage)
    {
        global $config;
        
        //=======================================================================================================
        // Compose message. You can use Markdown
        // Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
        //========================================================================================================
        
        $timestamp = date("c", strtotime("now"));
        
        $mentions = "";
        
        if ($config['discord']['mentions_enable'])
        {
            $mentions = "Automatically mentioned users:";
            foreach ($config['discord']['mentions'] as $mention)
            {
                $mentions .= " <@".$mention.">";
            }
        }
        
        $json_data = json_encode([
            // Message
            "content" => $mentions,
            
            // Username
            // "username" => "",
        
            // Avatar URL.
            // Uncoment to replace image set in webhook
            //"avatar_url" => "",
        
            // Text-to-speech
            "tts" => false,
        
            // File upload
            // "file" => "",
        
            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => "New launcher issue report",
        
                    // Embed Type
                    "type" => "rich",
        
                    // Embed Description
                    "description" => ":exclamation: Exception error",
        
                    // URL of title link
                    // "url" => "",
        
                    // Timestamp of embed must be formatted as ISO8601
                    "timestamp" => $timestamp,
        
                    // Embed left border color in HEX
                    "color" => $config['discord']['bordercolor'],
        
                    // Footer
                    "footer" => [
                        "text" => "By: ".$reportBy,
                        "icon_url" => "https://i.dlpng.com/static/png/6342390_preview.png"
                    ],
        
                    // Image to send
                    // "image" => [
                        // "url" => ""
                    // ],
        
                    // Thumbnail
                    // "thumbnail" => [
                    // "url" => ""
                    // ],
        
                    // Author
                    // "author" => [
                        // "name" => $reportBy,
                        // "url" => ""
                    // ],
        
                    // Additional Fields array
                    "fields" => [
                        // Field 1
                        [
                            "name" => "Filename and Line",
                            "value" => $issueAt,
                            "inline" => false
                        ],
                        // Field 2
                        [
                            "name" => "Error Message",
                            "value" => $issueMessage,
                            "inline" => false
                        ],
                        // Field 3
                        [
                            "name" => "Launcher Version",
                            "value" => $launcherVersion,
                            "inline" => true
                        ]
                        // Etc..
                    ]
                ]
            ]
        
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        
        
        $ch = curl_init( $config['discord']['webhookurl'] );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        
        $response = curl_exec( $ch );
        // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
        // echo $response;
        curl_close( $ch );
    }
}

<?php

$config['srp6'] = false;             // "false" for SHA or "true" for SRP6 depending on your emulator support

$config['mysqli'] = array(

    // AUTH DATABASE ---------------------------------
    'auth' => array(
        'hostname'                  => '127.0.0.1',
        'port'                      => '3306',
        'user'                      => 'trinity',
        'pass'                      => 'trinity',
        'database'                  => 'auth'
    ),
    
    // AUTH DB AGAIN.. BECAUSE YOU HOLD BONUSES AND VOTES IN ACCOUNT_DONATE IN AUTH DB ---------------------------------
    'web' => array(
        'hostname'                  => '127.0.0.1',
        'port'                      => '3306',
        'user'                      => 'trinity',
        'pass'                      => 'trinity',
        'database'                  => 'auth'
    ),
    
    // LAUNCHER DATABASE ---------------------------------
    'launcher' => array(
        'hostname'                  => '127.0.0.1',
        'port'                      => '3306',
        'user'                      => 'trinity',
        'pass'                      => 'trinity',
        'database'                  => 'launcher'
    ),
    
    'realms' => array(
    // realm ONE
        1 => array(
            'id'                    => 1, // realm id and must match in auth.realmlist
            'name'                  => 'TrinityCore Realm One',
            'hostname'              => '127.0.0.1',
            'port'                  => 3306,
            'user'                  => 'trinity',
            'pass'                  => 'trinity',
            'database'              => 'characters'
        ),
    // realm TWO
        // 3 => array(
            // 'id'                    => 3, // realm id and must match in auth.realmlist
            // 'name'                  => 'Exiled',
            // 'hostname'              => 'localhost',
            // 'port'                  => 3306,
            // 'user'                  => 'acore',
            // 'pass'                  => 'acore',
            // 'database'              => 'characters'
        // ),
    // realm THREE, ETC
        // 6 => array(
            // 'id'                    => 3, // realm id and must match in auth.realmlist
            // 'name'                  => 'Exiled',
            // 'hostname'              => 'localhost',
            // 'port'                  => 3306,
            // 'user'                  => 'acore',
            // 'pass'                  => 'acore',
            // 'database'              => 'characters'
        // ),
    ),
);

$config['soap'] = array(
    1 => array(     // realm id and must match in auth.realmlist
        'address'   => '127.0.0.1',
        'port'      => 7878,
        'user'      => 'admin',
        'pass'      => 'admin',
        'uri'       => 'urn:TC'
    ),
    // 3 => array(     // realm id and must match in auth.realmlist
        // 'address'   => '127.0.0.1',
        // 'port'      => 7878,
        // 'user'      => 'admin',
        // 'pass'      => 'admin',
        // 'uri'       => 'urn:TC'
    // ),
    // 3 => array(     // realm id and must match in auth.realmlist
        // 'address'   => '127.0.0.1',
        // 'port'      => 7878,
        // 'user'      => 'admin',
        // 'pass'      => 'admin',
        // 'uri'       => 'urn:TC'
    // )
);

// GM RANK NAMES [6 DIGITS HEX COLOR CODE, RANK NAME]
$config['GMRanks'] = array(
    0 => array('FFFFFF', 'Player'),
    1 => array('00FF00', 'GM'),
    2 => array('6DFFC1', 'Head GM'),
    3 => array('00CA74', 'Administrator'),
    4 => array('00CA74', 'Console'),
	
);

// Allow access to specific ranks for:
$config['gmPermissions'] = array(
    // gm and admin panels access
    'gm_panel'          => array(1, 2, 3, 4),
    'admin_panel'       => array(3, 4),
    
    // gm panel pages
    'tickets_list'      => array(1, 2, 3, 4),
    'bans_list'         => array(1, 2, 3, 4),
    'mute_logs'         => array(1, 2, 3, 4),
    'player_info'       => array(1, 2, 3, 4),
    
    // gm panel permissions
    'unban_accounts'    => array(1, 2, 3, 4),
    
    // admin panel pages
    'news_manager'          => array(3, 4),
    'news_create'           => array(3, 4),
    'news_edit'             => array(3, 4),
    'news_delete'           => array(3, 4),
    'notifications_manager' => array(3, 4),
    'notifications_create'  => array(3, 4),
    'notifications_delete'  => array(3, 4),
    'soap_logs'             => array(3, 4),
    'sessions_list'         => array(3, 4),
);

$config['discord'] = array(
    // Discord webook url for launcher error reports
    'webhookurl' => 'https://your_discord_webhook_url',

    // Embed left border color in HEX
    "bordercolor" => hexdec("FF0000"),

    // Enable or disable mentions in reports
    'mentions_enable' => false,
    
    // User ids (enable developer mode in discord then right click on a user "Copy ID")
    'mentions' => array(            
        615778269660184576,
        // 139020834009382912,
    ),
);

$config['onlinePlayers'] = array(
    // Enable or disable the online players list
    'enable' => true,
    // How many online players to be displayed per realm (0 = no limit)
    'limit' => 0
);

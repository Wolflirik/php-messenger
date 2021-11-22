<?php

return [
    'domain'    => '//example.com', // domain without protocol, for example: //test.com or //beta.test.com, etc.. 

    'ws_routes'         => [
        'add_message' => 'addMessage',
        'get_messages' => 'getMessages',
        'add_room' => 'addRoom',
        'search_user' => 'searchUser',
        'remove_missed' => 'removeMissed',
    ]
];

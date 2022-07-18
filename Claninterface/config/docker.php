<?php

use Wargaming\Language\DE;
use Wargaming\Server\EU;

return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    /*
    * Security and encryption configuration
    *
    * - salt - A random string used in security hashing methods.
    *   The salt value is also used as the encryption key.
    *   You should treat it as extremely sensitive data.
    */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],


    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => env("DB_HOST",'localhost'),
            /*
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            //'port' => 'non_standard_port_number',

            'username' => env("DB_USER",'root'),
            'password' => env("DB_PASS",'123456'),

            'database' => env("DB_DATABASE",'claninterface'),

            /*
             * You can use a DSN string to set the entire configuration
             */
            'url' => env('DATABASE_URL'),
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => env("EMAIL_HOST",'localhost'),
            'port' => env("EMAIL_PORT",25),
            'username' => env("EMAIL_USER"),
            'password' => env("EMAIL_PASS"),
            'client' => env("EMAIL_CLIENT",null),
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL',null),
            'tls' => env("EMAIL_TLS",true)
        ],
    ],

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => [env("EMAIL_FROM_MAIL",'some@email.com') => env("EMAIL_FROM_NAME","WoT-Claninterface")],
            /*
             * Will by default be set to config value of App.encoding, if that exists otherwise to UTF-8.
             */
            //'charset' => 'utf-8',
            //'headerCharset' => 'utf-8',
        ],
    ],


    "Provider"=>[ // für die rechtliches Seite
        "name" => env("PROVIDER_NAME","your Name"),
        "mail" => env("PROVIDER_MAIL","your Mail"),
        "tel"  => env("PROVIDER_TEL","your Tel")
    ],
/*
 * Information where to find the Teamspeak
 * - Host: IP or URL
 * - Port: (default 10011) Port of Query
 * - UID: UID of the virtual Server
 */
    'TeamspeakQueryConnection' => [
        'host' => env("TSQ_HOST",'127.0.0.1'),
        'port' => env("TSQ_PORT",10011),
        'uid' => env("TSQ_UID",'1'),
    ],
    /*
     * Login Data for the TeamspeakQuery
     * - user: User
     * - pass: Password
     * - LoginName: Name Webinterface will use (extended: by  unique ID)
     */
    'TeamspeakQueryLogin' => [
        'user' => env("TSQ_USER",'TeamspeakQueryLoginUser'),
        'pass' => env("TSQ_PASS",'TeamspeakQueryLoginPasswd'),
        'loginName' => env("TSQ_NICK",'WebInterface by LFS96')
    ],
    /*
     * Servergruppen die Admins sind und welche über ereignisse informiert werden sollen
     */
    'Teamspeak' => [
        'AdminGroups' => json_decode(env("TEAMSPEAK_ADMIN_GROUPS"),TRUE), //Admin können nicht gekickt/gebannt werden
        'NoticeGroups' => json_decode(env("TEAMSPEAK_NOTICE_GROUPS"),TRUE) // Werden durch das Claninterface direkt im TS informiert
    ],
    /*
     * Wargaming Developer API connection
     * - authkey: KEY
     * - expectedValues: URL to wn8exp.json
     * - lang: language of response
     * - server: url of server
     */
    "Wargaming" => [
        "authkey" => env("WGAPI_AUTHKEY",'0123456789abcdef0123456789abcdef'),
        'expectedValues' => 'https://static.modxvm.com/wn8-data-exp/json/wn8exp.json',
        'lang' => (new DE()),
        'server' => (new EU(""))
    ],
    /*
     * Einstellungen zu Spielerdaten
     */
    "PlayerData" => [
        "DelAfterDaysLeft" => 14, //Wann sollen Spielerdaten gelöscht werden.
    ],
    /*
     * Footer message block
     */
    'footer' => [
        'enable' => filter_var(env("FOOTER_ENABLE",true),FILTER_VALIDATE_BOOLEAN),
        'text' => env("FOOTER_TEXT","Hier kann eure Footer Nachricht stehen."),
        'link' => [
            'text' => env("FOOTER_LINK_TEXT","Google"),
            'url' => env("FOOTER_LINK_URL",'https://www.google.com/'),
            'target' => env("FOOTER_LINK_TARGET", '_blank'),
        ],
    ],
    /*
     * Define Teamspeak rooms that are used for Events
     * Will be used bei checking, if user joined an event
     */
    'battle_rooms' => json_decode(env("BATTLE_ROOMS"),TRUE),
];

<?php

/**
 * This is the config file for the Speed framework, each config consists of a pair of ```key```=>```value``` group
 *
 * Users can get the config value use the class [[supjos\config\Config]] and the method ```getConfig``` to get the
 * config value from the config
 *
 *  The class supjos\config\Config's constructor has one parameter which you can assign it with a different config
 * file name. for example:
 *
 * $config = App::createObject(['class'=>supjos\config\Config:getClass(), 'dsn.config']);
 * Then the $config->getConfigs() will return the value from the ```dsn.config``` file, if exists, otherwise return
 * null. means not exists.
 *
 * If you want to get all the config values use the method ```getConfigs``` instead.
 *
 * License: MIT
 * Created By: Josin 2017-8-18 19:02:29
 *
 * @since 0.0.2
 */
return [
    'db' => [
        'dsn'      => 'mysql:host=localhost;dbname=supjos',
        'username' => 'root',
        'password' => 'Root@localhost',
        'charset'  => 'utf8',
        'prefix'   => 'www_'
    ]
];

<?php

use Dotenv;

function fl_get_config($key)
{
    switch($key) {
        case 'public_key':
            if (function_exists('config')) {
                return config('app.FL_PUBLIC_KEY');
            }
            load_env();
            return $_ENV['FL_PUBLIC_KEY'];
        case 'secret_key':
            if (function_exists('config')) {
                return config('app.FL_SECRET_KEY');
            }
            load_env();
            return $_ENV['FL_SECRET_KEY'];
        case 'environment':
            if (function_exists('config')) {
                return config('app.FL_MODE');
            }
            load_env();
            return $_ENV['FL_MODE'];
        default:
            load_env();
            return $_ENV[$key];
    }
}

function load_env()
{
    if (!isset($_ENV['FL_PUBLIC_KEY'])) {
        $dotenv = new Dotenv\Dotenv(__DIR__.'/../');
        $dotenv->load();
    }
}
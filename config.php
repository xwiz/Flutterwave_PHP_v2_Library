<?php

function fl_get_config($key)
{
    switch($key) {
        case 'public_key':
            if (function_exists('config')) {
                return config('app.FL_PUBLIC_KEY');
            }
            return $_ENV['FL_PUBLIC_KEY'];
        case 'secret_key':
            if (function_exists('config')) {
                return config('app.FL_SECRET_KEY');
            }
            return $_ENV['FL_SECRET_KEY'];
        case 'environment':
            if (function_exists('config')) {
                return config('app.FL_MODE');
            }
            return $_ENV['FL_MODE'];
        default:
            return $_ENV[$key];
    }
}
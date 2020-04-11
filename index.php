<?php
session_start();
/*
https://github.com/GoogleCloudPlatform/php-docs-samples
index.php file is created to work php76
*/

if(isset($_SESSION["username"]))
{
    switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
        case '/':
            require 'login.html';
            break;
        case '/register.html':
            require 'register.html';
            break;
        case '/main.html':
            require 'main.html';
            break;
        case '/post.html':
            require 'post.html';
            break;
        case '/profile.html':
            require 'profile.html';
            break;
        case '/search.html':
            require 'search.html';
            break;
        case '/product.html':
            require 'product.html';
            break;
        case '/trade.html':
            require 'trade.html';
            break;
        default:
            http_response_code(404);
            exit('Not Found');
    }
}
else
{
    switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
        case '/':
            require 'login.html';
            break;
        case '/register.html':
            require 'register.html';
            break;
        default:
            http_response_code(404);
            exit('Not Found');
        }
        
}

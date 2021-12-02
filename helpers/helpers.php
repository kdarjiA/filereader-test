<?php

if (!function_exists('isFileExists')) {
    function isFileExists($path){
        if (!file_exists($path)) {
            return false;
        }
        return true;
    }
}

if (!function_exists('isExtensionAllowed')) {
    function isExtensionAllowed($extension){
        if(!in_array($extension, ALLOWED_EXTENSIONS)){
            return false;
        }
        return true;
    }
}

if (!function_exists('getClassName')) {
    function getClassName($name){
        return ucfirst($name);
    }
}
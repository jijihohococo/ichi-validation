<?php

if (!function_exists('convertToMb')) {
    function convertToMb($bytes)
    {
        return round($bytes / 1024 / 1024, 4);
    }
}

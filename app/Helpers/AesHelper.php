<?php 

use Thegiant\Algorithms\AES;

if (!function_exists('aes_decrypt')) {
    function aes_decrypt($encrypted_data) {
        return AES::decrypt($encrypted_data, $_ENV['AES_KEY'], $_ENV['AES_IV']);
    }
}

if (!function_exists('aes_encrypt')) {
    function aes_encrypt($encrypted_data) {
        return AES::encrypt($encrypted_data, $_ENV['AES_KEY'], $_ENV['AES_IV']);
    }
}
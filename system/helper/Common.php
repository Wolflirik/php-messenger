<?php

namespace system\helper;

class Common{

    /**
     * @return mixed
     */
    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return bool|string
     */
    public static function getPathUri()
    {
        $pathUri = $_SERVER['REQUEST_URI'];
        if($position = strpos($pathUri, '?'))
        {
            $pathUri = substr($pathUri, 0, $position);
        }

        return $pathUri;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }

    /**
     * @param $plain_text
     * @param $crypt_key
     * @return string
     */
    public static function encrypt ($plain_text, $crypt_key = '5V:W-*uTrwmu](eAGPD~') {

        $cipher   = 'aes-128-cbc';
      
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt(
                $plain_text, $cipher, $crypt_key, $options=OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $crypt_key, $as_binary=true);
            $encoded_text = base64_encode( $iv.$hmac.$ciphertext_raw );
        }
      
        return $encoded_text;
    }

    /**
     * @param $plain_text
     * @param $crypt_key
     * @return string
     */
    public static function decrypt ($encoded_text, $crypt_key = '5V:W-*uTrwmu](eAGPD~') {
      
        $c = base64_decode($encoded_text);
        $cipher   = 'aes-128-cbc';
      
        if (in_array($cipher, openssl_get_cipher_methods()))
        {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len=32);
            $ivlenSha2len = $ivlen+$sha2len;
            $ciphertext_raw = substr($c, $ivlen+$sha2len);
            $plain_text = openssl_decrypt(
                $ciphertext_raw, $cipher, $crypt_key, $options=OPENSSL_RAW_DATA, $iv);
        }
      
        return $plain_text;
    }
}
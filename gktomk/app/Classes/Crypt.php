<?php


namespace GKTOMK\Classes;


use Exception;

class Crypt
{
    private const PASSPHRASE = 'mu8VibzkUq58Z+hdqD6U/7gZhegGVZDY+1L7B+UmraA=';
    private const CIPHER = "aes-256-cbc-hmac-sha256";

    public static function encryptText($text)
    {
        if (in_array(self::CIPHER, openssl_get_cipher_methods())) {
            $encryption_key = base64_decode(self::PASSPHRASE);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc-hmac-sha256'));
            $encrypted = openssl_encrypt($text, self::CIPHER, $encryption_key, 0, $iv);

            return base64_encode($encrypted . '::' . $iv);
        }
        else
            throw new Exception('Cipher ' . self::CIPHER . 'not found');
    }

    public static function decryptText($text)
    {
        if (in_array(self::CIPHER, openssl_get_cipher_methods())){
            $encryption_key = base64_decode(self::PASSPHRASE);
            list($encrypted_data, $iv) = explode('::', base64_decode($text), 2);

            return openssl_decrypt($encrypted_data, self::CIPHER, $encryption_key, 0, $iv);
        }
        else
            throw new Exception('Cipher ' . self::CIPHER . 'not found');
    }
}

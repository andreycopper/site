<?php

namespace System;

/**
 * Class OldRSA
 * @package App\System
 */
class OldRSA
{
    const CIPHER = 'aes-256-cbc';
    const OPTION = OPENSSL_RAW_DATA;
    const SHA2LEN = 32;

    protected $public_key;
    protected $private_key;

    public function __construct(string $private_key)
    {
        $this->private_key = base64_decode($private_key);
        $this->public_key = base64_decode($_SESSION['public_key']);
    }

    /**
     * Шифрование данных
     * @param string $plaintext - данные для шифрования
     * @return string
     */
    public function encrypt(string $plaintext)
    {
        if (empty($plaintext)) return null;

        $ciphertext_raw = openssl_encrypt($plaintext, self::CIPHER, $this->private_key, self::OPTION, $this->public_key);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->private_key, true);

        return base64_encode($this->public_key . $hmac . $ciphertext_raw);
    }

    /**
     * Дешифрование данных
     * @param string $ciphertext - данные для дешифрования
     * @return false|string
     */
    public function decrypt(string $ciphertext)
    {
        if (empty($ciphertext)) return null;

        $c = base64_decode($ciphertext);
        $ivlen = self::getIvLength();
        $public_key = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, self::SHA2LEN);
        $ciphertext_raw = substr($c, $ivlen + self::SHA2LEN);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->private_key, true);

        return hash_equals($hmac, $calcmac) ?
            openssl_decrypt($ciphertext_raw, self::CIPHER, $this->private_key, self::OPTION, $public_key) :
            false;
    }

    /**
     * Генерирование строки случайных символов
     * @param int $count - длина строки (0 - длина определяется текущим методом шифрования)
     * @param bool $encode - необходимость кодирования в base64
     * @return false|string
     */
    public static function generateRandomBytes(int $count = 0, bool $encode = false)
    {
        return $encode ?
            base64_encode(openssl_random_pseudo_bytes($count ?: self::getIvLength())) :
            openssl_random_pseudo_bytes($count ?: self::getIvLength());
    }

    /**
     * Определяет длину публичного ключа шифрования
     * @return false|int
     */
    public static function getIvLength()
    {
        return openssl_cipher_iv_length(self::CIPHER);
    }
}

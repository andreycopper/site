<?php
namespace System;

use phpseclib3\Crypt\RSA;
use Exceptions\CryptException;

class Crypt
{
    private ?RSA\PublicKey $public = null;
    private ?RSA\PrivateKey $private = null;
    private ?string $publicKey;
    private ?string $privateKey;

    public function __construct(string $publicKey = null, string $privateKey = null)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Generate private-public pair
     * @return $this
     */
    public function generatePair(): Crypt
    {
        $this->private = RSA::createKey();
        $this->public = $this->private->getPublicKey();

        $this->privateKey = $this->private->toString(
            'PKCS8'/*,
            [
                'encryptionAlgorithm' => 'id-PBES2',
                'encryptionScheme'    => 'aes256-CBC-PAD',
                'PRF'                 => 'id-hmacWithSHA512-256',
                'iterationCount'      => 4096
            ]*/
        );
        $this->publicKey = $this->public->toString('PKCS8');

        return $this;
    }

    /**
     * Load keys from disk
     * @param int $user_id - user id
     * @throws CryptException
     */
    public function load(int $user_id): Crypt
    {
        if (!is_file(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR . "private.pem") ||
            !is_file(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR . "public.pem")
        ) throw new CryptException('Не найдены файлы с ключами', 523);

        $this->privateKey = file_get_contents(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR . "private.pem");
        $this->publicKey = file_get_contents(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR . "public.pem");
        return $this;
    }

    /**
     * Save current keys to disk
     * @param int $user_id - user id
     * @return $this
     */
    public function save(int $user_id): Crypt
    {
        if (!is_dir(DIR_CERTIFICATES)) mkdir(DIR_CERTIFICATES);
        if (!is_dir(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id)) mkdir(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id);
        file_put_contents(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR . "public.pem", $this->publicKey);
        file_put_contents(DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR . "private.pem", $this->privateKey);
        return $this;
    }

    /**
     * Encrypt text by public key
     * @param ?string $plaintext - text
     * @param bool $encodeBase64 - encode to base64
     * @return ?string
     */
    public function encryptByPublicKey(?string $plaintext = null, bool $encodeBase64 = true): ?string
    {
        if (empty($this->publicKey) || empty($plaintext)) return $plaintext;
        openssl_public_encrypt($plaintext, $encrypted, $this->publicKey);
        return $encodeBase64 ? base64_encode($encrypted) : $encrypted;
    }

    /**
     * Encrypt text by private key
     * @param ?string $plaintext - text to encode
     * @param bool $encodeBase64 - encode to base64
     * @return ?string
     */
    public function encryptByPrivateKey(?string $plaintext = null, bool $encodeBase64 = true): ?string
    {
        if (empty($this->privateKey) || empty($plaintext)) return $plaintext;
        openssl_private_encrypt($plaintext, $encrypted, $this->privateKey);
        return $encodeBase64 ? base64_encode($encrypted) : $encrypted;
    }

    /**
     * Decrypt text by public key
     * @param ?string $ciphertext - text to decode
     * @param bool $encodedBase64 - decode from base64
     * @return ?string
     */
    public function decryptByPublicKey(?string $ciphertext = null, bool $encodedBase64 = true): ?string
    {
        if (empty($this->publicKey) || empty($ciphertext)) return $ciphertext;
        openssl_public_decrypt($encodedBase64 ? base64_decode($ciphertext) : $ciphertext, $out, $this->publicKey);
        return $out;
    }

    /**
     * Decrypt text by private key
     * @param ?string $ciphertext - text to decode
     * @param bool $encodedBase64 - decode from base64
     * @return ?string
     */
    public function decryptByPrivateKey(?string $ciphertext = null, bool $encodedBase64 = true): ?string
    {
        if (empty($this->privateKey) || empty($ciphertext)) return $ciphertext;
        openssl_private_decrypt($encodedBase64 ? base64_decode($ciphertext) : $ciphertext, $out, $this->privateKey);
        return $out;
    }

    /**
     * @return ?string
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @param ?string $publicKey
     * @return Crypt
     */
    public function setPublicKey(?string $publicKey): Crypt
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * @param ?string $privateKey
     * @return Crypt
     */
    public function setPrivateKey(?string $privateKey): Crypt
    {
        $this->privateKey = $privateKey;
        return $this;
    }
}

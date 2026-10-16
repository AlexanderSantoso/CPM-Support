<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CryptoService
{
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function decrypt(string $value): ?string
    {
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return null;
        }
    }

    public function encryptId(int $id): string
    {
        return $this->encrypt((string) $id);
    }

    public function decryptId(string $value): int
    {
        $id = $this->decrypt($value);

        if (!$id || !ctype_digit($id)) {
            abort(404);
        }

        return (int) $id;
    }

    public function decryptIdOrNull(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        try {
            $id = $this->decrypt($value);

            if (!$id || !ctype_digit($id)) {
                return null;
            }

            return (int) $id;
        } catch (\Exception $e) {
            return null;
        }
    }
}

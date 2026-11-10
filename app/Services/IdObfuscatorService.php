<?php

namespace App\Services;

use Hashids\Hashids;

class IdObfuscatorService
{
    protected Hashids $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(
            config('hashids.salt'),
            config('hashids.length', 20)
        );
    }

    public function encode(int $id): string
    {
        return $this->hashids->encode($id);
    }

    public function decode(string $value): ?int
    {
        return $this->hashids->decode($value)[0] ?? null;
    }
}
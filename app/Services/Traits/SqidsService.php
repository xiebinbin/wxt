<?php

namespace App\Services\Traits;

use Sqids\Sqids;

trait SqidsService
{
    protected static function sqids()
    {
        return new Sqids(alphabet: self::$SQIDS_ALPHABET, minLength: self::$SQIDS_MIN_LENGTH);
    }
    public static function codeToId(string $code): ?int
    {
        $sqids = self::sqids();
        $ids = $sqids->decode($code);
        if (count($ids) == 1) {
            return $ids[0];
        }
        return null;
    }
    public static function idToCode(int $id): ?string
    {
        $sqids = self::sqids();
        return $sqids->encode([$id]);
    }
}

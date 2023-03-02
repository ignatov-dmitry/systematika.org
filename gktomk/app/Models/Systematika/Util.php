<?php


namespace GKTOMK\Models\Systematika;
use Closure as Closure;

final class Util
{
    private static $tplToken = '{%s}';

    public static function replaceTokens($subject, array $replacements) {
        return strtr($subject, self::prepareTokenReplacements($replacements));
    }

    public static function prepareTokenReplacements(array $replacements) {
        $prepared = array();

        foreach ($replacements as $key => $val) {
            $key = self::prepareToken($key);
            $prepared[$key] = $val;
        }

        return $prepared;
    }

    public static function prepareToken($token) {
        return sprintf(self::$tplToken, $token);
    }
}
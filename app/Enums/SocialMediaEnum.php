<?php

namespace App\Enums;

enum SocialMediaEnum: string {
    case facebook = 'Facebook';
    case twitter = 'Twitter';
    case instagram = 'Instagram';
    case youtube = 'Youtube';
    case linkedin = 'Linkedin';
    case pinterest = 'Pinterest';
    case reddit = 'Reddit';
    case github = 'Github';
    case other = 'Other';

    public static function toArray(): array
    {
        $array = [];

        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }

        return $array;
    }

    public static function values(): array
    {
        $array = [];

        foreach (self::cases() as $case) {
            array_push($array, $case->value);
        }

        return $array;
    }
}

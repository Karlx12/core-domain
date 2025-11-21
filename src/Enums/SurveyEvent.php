<?php

namespace IncadevUns\CoreDomain\Enums;

enum SurveyEvent: string
{
    case Satisfaction = 'satisfaction';
    case Teacher = 'teacher';
    case Impact = 'impact';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

namespace IncadevUns\CoreDomain\Enums;

enum ContentType: string
{
    case NEWS = 'news';
    case ANNOUNCEMENT = 'announcement';
    case ALERT = 'alert';
    case EVENT = 'event';
}

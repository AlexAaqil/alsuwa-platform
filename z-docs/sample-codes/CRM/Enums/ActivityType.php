<?php

namespace Modules\CRM\Enums;

enum ActivityType: string
{
    case CALL = 'call';
    case MEETING = 'meeting';
    case EMAIL = 'email';
    case NOTE = 'note';
    case TASK = 'task';
}
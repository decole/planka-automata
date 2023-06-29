<?php

declare(strict_types=1);

namespace App\Feature\DeadLineNotify\Enum;

enum NotifyTypeEnum: int
{
    case AT_WEEK = 1;
    case AT_THREE_DAYS = 2;
    case AT_TOMORROW = 3;
    case AT_TODAY = 4;
    case AT_DEAD_LINE = 5;
    case AFTER_DEADLINE_BY_EVERYDAY = 6;
    case AT_BUGFIX_CREATED = 7;
}

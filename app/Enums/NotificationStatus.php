<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Retrying = 'retrying';
    case Success = 'success';
    case Dead = 'dead';
}

<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

class TaskNotSelectedApiException extends ApiException
{
    public const string KEY = 'task_not_selected';
}

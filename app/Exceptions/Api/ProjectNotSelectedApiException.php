<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

class ProjectNotSelectedApiException extends ApiException
{
    public const string KEY = 'project_not_selected';
}

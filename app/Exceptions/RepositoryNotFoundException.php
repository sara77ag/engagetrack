<?php

namespace App\Exceptions;

// AC-S3-01.2
class RepositoryNotFoundException extends S3Exception
{
    protected int $status = 404;

    public function __construct()
    {
        parent::__construct('Repository not found or not public');
    }
}

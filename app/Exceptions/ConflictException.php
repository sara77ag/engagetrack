<?php

namespace App\Exceptions;

// AC-S3-01.4 (and any other "already exists" situation)
class ConflictException extends S3Exception
{
    protected int $status = 409;
}

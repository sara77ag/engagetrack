<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

// Base class: every S3 business error knows its own HTTP status
// and how to turn itself into a response (JSON for API/tests, redirect for the browser).
abstract class S3Exception extends RuntimeException
{
    protected int $status = 400;

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], $this->status);
        }

        return back()->withInput()->withErrors(['message' => $this->getMessage()]);
    }
}

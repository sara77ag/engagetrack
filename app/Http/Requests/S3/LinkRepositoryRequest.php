<?php

namespace App\Http\Requests\S3;

use App\Services\S3\RepositoryConnectionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LinkRepositoryRequest extends FormRequest
{
    // Runs BEFORE validation: a pending member gets 403 even with bad input (AC-S3-01.3)
    public function authorize(): bool
    {
        return Gate::allows('s3-member', (int) $this->route('teamId'));
    }

    public function rules(): array
    {
        return [
            'repository_url' => [
                'required',
                'string',
                'max:200',
                'regex:'.RepositoryConnectionService::URL_PATTERN,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'repository_url.regex' => 'Enter a link like https://github.com/owner/repo',
        ];
    }
}

<?php

namespace App\Http\Requests\S3;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SaveGitHubUsernameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('s3-member', (int) $this->route('teamId'));
    }

    public function rules(): array
    {
        return [
            // GitHub usernames: letters, numbers and single hyphens, max 39
            'github_login' => ['required', 'string', 'max:39', 'regex:/^[A-Za-z0-9](?:[A-Za-z0-9]|-(?=[A-Za-z0-9])){0,38}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'github_login.regex' => 'A GitHub username can only contain letters, numbers and hyphens (no spaces).',
        ];
    }
}

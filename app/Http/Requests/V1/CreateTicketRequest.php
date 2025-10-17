<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /**@var User $user */
        $user=Auth::user();
        if($user->tokenCan('bidder.access')||$user->tokenCan('entity.access')||$user->tokenCan('admin.access'))
        {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'issuegroup_id'=>['required'],
            'issuetype_id'=>['required'],
            'ticket'=>['required'],
            'regnumber'=>['required'],
            'name'=>['required'],
            'email'=>['required','email'],
            'phone'=>['required'],
            'title'=>['required'],
            'description'=>['required'],
            'user_id'=>['required'],
            'status'=>['required'],
            'issuestatus'=>['required'],
            'priority'=>['required'],
            'attachmenttype'=>['required'],
        ];
    }
    protected function prepareForValidation()
    {

    $input = $this->all();

    // replace camelCase keys with snake_case for DB
    if (array_key_exists('issuegroupId', $input)) {
        $input['issuegroup_id'] = $input['issuegroupId'];
        unset($input['issuegroupId']);
    }
    if (array_key_exists('issuetypeId', $input)) {
        $input['issuetype_id'] = $input['issuetypeId'];
        unset($input['issuetypeId']);
    }

    if (array_key_exists('ticketnumber', $input)) {
        $input['ticket'] = $input['ticketnumber'];
        unset($input['ticketnumber']);
    }

    if (array_key_exists('userId', $input)) {
        $input['user_id'] = $input['userId'];
        unset($input['userId']);
    }

    $this->replace($input);
    }
}

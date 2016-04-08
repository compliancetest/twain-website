<?php

namespace App\Http\Requests;

use App\CommunityMeta;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class CommunityRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'visibility_status' => 'required|string|in:public,private,hidden',
            'articles_status' => 'string|in:members,mods,admins',
            'image' => 'image',
        ];
        if($this->has('articles_enabled')){
            $rules['articles_status'] = 'string|in:member,mod,admin';
        }

        return $rules;
    }
}

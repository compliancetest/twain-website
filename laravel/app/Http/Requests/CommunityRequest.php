<?php

namespace App\Http\Requests;

use App\CommunityMeta;
use App\Http\Requests\Request;

class CommunityRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return is_super_admin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if($this->request->has('title')){
            $rules['title'] = 'required';
        }
        if($this->request->has('description')){
            $rules['description'] = 'required';
        }
        if($this->request->has('status')){
            $rules['status'] = 'required|string|in:public,private,hidden';
        }
        if($this->request->has('group-invite-status')){
            $rules['group-invite-status'] = 'required|string|in:members,mods,admins';
        }
        if($this->request->has('image')){
            $rules['image'] = 'mimes:png:jpeg:jpg:gif';
        }
        return $rules;
    }
}

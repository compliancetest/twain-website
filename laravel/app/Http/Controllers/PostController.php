<?php

namespace App\Http\Controllers;

use App\Post;
use App\WpOptions;
use Illuminate\Http\Request;

use App\Http\Requests;

class PostController extends Controller
{
    /**
     * Used to trigger PostOpserver from wordpress
     * @param $optionName
     */
    public function save($optionName)
    {
        $option = WpOptions::where('option_name', $optionName)->first();
        $post = Post::find($option->option_value);
        $post->timestamps = false;
        $post->save();
        $option->delete();
    }
}

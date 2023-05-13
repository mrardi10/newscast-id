<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * index
     *
     * @return void
     */
    public function search(Request $request)
    {
        $qs = $request->input('q');
        //User Search
        $user = User::where(function ($q) use ($qs) {
            if ($qs != '') {
                $q->where('name', 'like', '%' . $qs . '%')
                    ->orWhere('email', 'like', '%' . $qs . '%');
            }
        })->get();

        //return collection of posts as a resource
        return new UserResource(true, 'List Data User', $user);
    }
}
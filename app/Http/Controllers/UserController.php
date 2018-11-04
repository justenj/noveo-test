<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreRequest;
use App\User;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function store(StoreRequest $request)
    {
        $user = $this->user->create($request->all());

        $user->groups()->sync($request->groups);

        $responseData = [
            'result' => $user->fresh(['groups'])->toArray()
        ];

        return response($responseData, 201);
    }
}

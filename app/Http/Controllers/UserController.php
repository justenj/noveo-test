<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateRequest;
use App\Http\Requests\Users\StoreRequest;
use App\User;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Fetch users collection
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $responseData = [
            'result' => $this->user->paginate()
        ];
        return response($responseData);
    }

    /**
     * Create a new user
     *
     * @param StoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $user = $this->user->create($request->all());

        $user->groups()->sync($request->groups);

        $responseData = [
            'result' => $user->fresh(['groups'])->toArray()
        ];

        return response($responseData, 201);
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param UpdateRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(User $user, UpdateRequest $request)
    {
        $user->update($request->all());

        $user->groups()->sync($request->groups);

        $responseData = [
            'result' => $user->fresh('groups')->toArray()
        ];

        return response($responseData, 200);
    }
}

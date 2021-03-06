<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\CheckRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Http\Requests\Users\StoreRequest;
use App\User;
use Illuminate\Http\Response;

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
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        $responseData = [
            'result' => $this->user->paginate()
        ];
        return response($responseData);
    }

    /**
     * Fetch user model
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user): Response
    {
        $responseData = [
            'result' => $user->loadMissing(['groups'])
        ];
        return response($responseData);
    }

    /**
     * Create a new user
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request): Response
    {
        $user = $this->user->create($request->all());

        $user->groups()->sync($request->groups);

        $responseData = [
            'result' => $user->fresh('groups')
        ];

        return response($responseData, 201);
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param UpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UpdateRequest $request): Response
    {
        $user->update($request->all());

        $user->groups()->sync($request->groups);

        $responseData = [
            'result' => $user->fresh('groups')
        ];

        return response($responseData, 200);
    }

    /**
     * Check user existence
     *
     * @param CheckRequest $request
     * @return \Illuminate\Http\Response
     */
    public function check(CheckRequest $request): Response
    {
        $responseData =  [
            'result' => [
                'exists' => false,
            ]
        ];

        $user = $this->user->whereEmail($request->email)->first();
        if ($user) {
            $responseData['result'] = [
                'exists' => true,
                'state' => $user->state,
                'user_id' => $user->id,
            ];
        }

        return response($responseData, 200);
    }
}

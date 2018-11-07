<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\Groups\StoreRequest;
use App\Http\Requests\Groups\UpdateRequest;
use App\User;
use Illuminate\Http\Response;

class GroupController extends Controller
{
    private $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Fetch groups collection
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        $responseData = [
            'result' => $this->group->paginate()
        ];
        return response($responseData);
    }

    /**
     * Create a new group
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request): Response
    {
        $group = $this->group->create($request->all());

        $responseData = [
            'result' => $group
        ];

        return response($responseData, 201);
    }

    /**
     * Update an existing group
     *
     * @param Group $group
     * @param UpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Group $group, UpdateRequest $request): Response
    {
        $group->update($request->all());

        $responseData = [
            'result' => $group
        ];

        return response($responseData, 200);
    }

    /**
     * Add user into group
     *
     * @param Group $group
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function addUser(Group $group, User $user): Response
    {
        if (! $group->hasUser($user->id)) {
            $group->users()->save($user);
        }

        return response([], 204);
    }

    /**
     * Remove user from group
     *
     * @param Group $group
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Group $group, User $user): Response
    {
        $group->users()->delete($user);

        return response([], 204);
    }
}

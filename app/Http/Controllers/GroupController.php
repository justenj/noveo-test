<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\Groups\StoreRequest;
use App\Http\Requests\Groups\UpdateRequest;

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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $group = $this->group->create($request->all());

        $responseData = [
            'result' => $group->toArray()
        ];

        return response($responseData, 201);
    }

    /**
     * Update an existing group
     *
     * @param Group $group
     * @param UpdateRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Group $group, UpdateRequest $request)
    {
        $group->update($request->all());

        $responseData = [
            'result' => $group->toArray()
        ];

        return response($responseData, 200);
    }
}
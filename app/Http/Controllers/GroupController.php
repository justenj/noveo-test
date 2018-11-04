<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\Groups\StoreRequest;

class GroupController extends Controller
{
    private $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function store(StoreRequest $request)
    {
        $group = $this->group->create($request->all());

        $responseData = [
            'result' => $group->toArray()
        ];

        return response($responseData, 201);
    }
}

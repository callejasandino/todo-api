<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreTagsRequest;
use App\Services\TagService;

class TagController extends Controller
{
    protected $tagService;

    public function __construct()
    {
        $this->tagService = new TagService();
    }

    public function store(StoreTagsRequest $request)
    {
        return $this->tagService->store($request);
    }
}

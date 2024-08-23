<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreTaskFileUploadRequest;
use App\Services\TaskAttachementService;

class TaskAttachementController extends Controller
{
    protected $taskAttachementService;

    public function __construct()
    {
        $this->taskAttachementService = new TaskAttachementService();
    }

    public function upload(StoreTaskFileUploadRequest $request)
    {
        return $this->taskAttachementService->upload($request);
    }
}

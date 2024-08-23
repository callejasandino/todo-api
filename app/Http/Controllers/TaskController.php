<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreTaskRequest;
use App\Http\Requests\Update\ArchivedTaskRequest;
use App\Http\Requests\Update\MarkTaskRequest;
use App\Http\Requests\Update\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    public function index()
    {
        return $this->taskService->index();
    }

    public function show($taskId)
    {
        return $this->taskService->show($taskId);
    }

    public function sort(Request $request)
    {
        return $this->taskService->sort($request);
    }

    public function filter(Request $request)
    {
        return $this->taskService->filter($request);
    }

    public function store(StoreTaskRequest $request)
    {
        return $this->taskService->store($request);
    }

    public function update(UpdateTaskRequest $request)
    {
        return $this->taskService->update($request);
    }

    public function delete($taskId)
    {
        return $this->taskService->delete($taskId);
    }

    public function markTask(MarkTaskRequest $request)
    {
        return $this->taskService->markTask($request);
    }

    public function archiveTask(ArchivedTaskRequest $request)
    {
        return $this->taskService->archiveTask($request);
    }
}

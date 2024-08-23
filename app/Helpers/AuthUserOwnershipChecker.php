<?php

namespace App\Helpers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class AuthUserOwnershipChecker
{
    public function findAndAuthorizeTask($taskId)
    {
        $task = Task::find($taskId);

        $authUser = Auth::user();

        if (! $task) {
            return response()->json([
                'error' => 'Task does not exist.'
            ], 404);
        }

        if ($task->user_id !== $authUser->id) {
            return response()->json([
                'error' => 'Unauthorized.'
            ], 401);
        }

        return $task;
    }

}

<?php

namespace App\Services;

use App\Helpers\AuthUserOwnershipChecker;
use App\Http\Requests\Store\StoreTagsRequest;
use App\Models\TaskTag;
use Illuminate\Http\JsonResponse;

class TagService
{
    public function store(StoreTagsRequest $request)
    {
        $task = self::checkUserOwnership($request->input('task_id'));

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        TaskTag::updateOrCreate([
            'task_id' => $task->id,
        ], [

            'tags' => json_encode($request->input('tags')),
        ]);

        return response()->json([
            'tags' => $task->with('tag')
        ]);
    }

    public static function checkUserOwnership($taskId)
    {
        $userOwnership = new AuthUserOwnershipChecker();
        return $userOwnership->findAndAuthorizeTask($taskId);
    }
}

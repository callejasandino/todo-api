<?php

namespace App\Services;

use App\Helpers\AuthUserOwnershipChecker;
use App\Http\Requests\Store\StoreTaskFileUploadRequest;
use App\Models\TaskAttachment;
use Illuminate\Http\JsonResponse;

class TaskAttachementService
{
    public function upload(StoreTaskFileUploadRequest $request)
    {
        $task = self::checkUserOwnership($request->input('task_id'));

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        // Handle file uploads
        $uploadedFiles = [];
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('tasks/' . $task->id, 'public');

            // Save the attachment to the database
            $attachment = TaskAttachment::create([
                'task_id' => $task->id,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $file->getMimeType(),
            ]);

            $uploadedFiles[] = $attachment;
        }

        // You can store attachment metadata in the database here if needed.

        // Return success response with task and attached files
        return response()->json([
            'message' => 'Files uploaded successfully.',
            'task' => $task,
            'attachments' => $uploadedFiles,
        ], 200);
    }

    public static function checkUserOwnership($taskId)
    {
        $userOwnership = new AuthUserOwnershipChecker();
        return $userOwnership->findAndAuthorizeTask($taskId);
    }
}

<?php

namespace App\Services;

use App\Helpers\AuthUserOwnershipChecker;
use App\Http\Requests\Store\StoreTaskRequest;
use App\Http\Requests\Update\ArchivedTaskRequest;
use App\Http\Requests\Update\MarkTaskRequest;
use App\Http\Requests\Update\UpdateTaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class TaskService
{
    public static function index()
    {
        // Fetch tasks
        $tasks = Task::with(['tag', 'user', 'taskAttachments'])
        ->orderBy('created_at', 'DESC')
        ->paginate(10);

        $tasks->getCollection()->transform(function ($task) {
            $task->taskAttachments->transform(function ($attachment) {
                $attachment->url = URL::to(Storage::url($attachment->path));
                return $attachment;
            });
            return $task;
        });

        return response()->json([
            'tasks' => $tasks ?? []
        ], 200);
    }

    public static function show($taskId)
    {
        $task = self::checkUserOwnership($taskId);

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        return response()->json([
            'task' => $task
        ], 200);
    }

    public static function filter(Request $request)
    {
        $query = Task::with(['tag', 'user', 'taskAttachments']);

        $filter = $request->query('filter_by');
        $from = '';
        $to = '';

        // Check if the filter is based on dates
        if (in_array($filter, ['date_completed', 'archived_date', 'due_date'])) {
            $from = Carbon::parse($request->query('from'))->format('Y-m-d');
            $to = Carbon::parse($request->query('to'))->format('Y-m-d');
        }

        // Apply filters based on the type of filter selected
        if ($filter == 'date_completed') {
            $query->whereBetween('date_completed', [$from, $to]);
        } elseif ($filter == 'archived_date') {
            $query->whereBetween('archived_date', [$from, $to]);
        } elseif ($filter == 'due_date') {
            $query->whereBetween('due_date', [$from, $to]);
        } elseif ($filter == 'priority') {
            $priority = $request->query('priority');

            if($priority == 'none') {
                $query->where('task_priority', null);
            } else {
                $query->where('task_priority', $priority);
            }

        } else {
            $search = $request->query('search');
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Paginate the results
        $tasks = $query->paginate(10);

        // Return the tasks with a successful response
        return response()->json([
            'tasks' => $tasks ?? []
        ], 200);
    }

    public static function sort(Request $request)
    {
        // Define allowed sort columns and directions
        $allowedSortColumns = ['title', 'description', 'due_date', 'created_at', 'date_completed', 'task_priority'];
        $allowedSortDirections = ['asc', 'desc'];

        // Retrieve query parameters
        $sortColumn = $request->query('sort_column');
        $sortDirection = $request->query('sort_direction', 'asc'); // Default to 'asc' if not provided

        // Validate sort column
        if (!in_array($sortColumn, $allowedSortColumns)) {
            return response()->json(['message' => 'Invalid sort column'], 400);
        }

        // Validate sort direction
        if (!in_array($sortDirection, $allowedSortDirections)) {
            return response()->json(['message' => 'Invalid sort direction'], 400);
        }

        // Build the query with relations and sorting
        $query = Task::with(['tag', 'user', 'taskAttachments'])
            ->orderBy($sortColumn, $sortDirection);

        // Paginate the results and append sorting parameters to pagination links
        $tasks = $query->paginate(10)
            ->appends([
                'sort_column' => $sortColumn,
                'sort_direction' => $sortDirection
            ]);

        // Return paginated tasks with a 200 status
        return response()->json([
            'tasks' => $tasks
        ], 200);
    }


    public static function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date') ?? null,
            'task_priority' => $request->input('task_priority'),
            'user_id' => self::authUser()->id,
            'task_order' => Task::count() + 1, // Set task order
        ]);

        return response()->json([
            'task' => $task
        ], 200);
    }

    public static function update(UpdateTaskRequest $request)
    {
        $task = self::checkUserOwnership($request->input('task_id'));

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        $task->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'task_priority' => $request->input('task_priority'),
        ]);

        return response()->json([
            'task' => $task
        ], 200);
    }

    public static function delete($taskId)
    {
        $task = self::checkUserOwnership($taskId);

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted'
        ], 204);
    }

    public static function markTask(MarkTaskRequest $request)
    {
        $mark = $request->input('mark');

        $task = self::checkUserOwnership($request->input('task_id'));

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        $date = $mark == true ? Carbon::now()->format('Y-m-d H:i:s') : null;

        $task->update([
            'date_completed' => $date
        ]);

        return response()->json([
            'task' => $task
        ], 200);
    }

    public static function archiveTask(ArchivedTaskRequest $request)
    {
        $archived = $request->input('mark');

        $task = self::checkUserOwnership($request->input('task_id'));

        if (is_a($task, JsonResponse::class)) {
            return $task;
        }

        $date = $archived == true ? Carbon::now()->format('Y-m-d H:i:s') : null;

        $isArchived = !$task->is_archived;

        $task->update([
            'archived_date' => $date,
            'is_archived' => $isArchived
        ]);

        return response()->json([
            'task' => $task
        ], 200);
    }

    public static function authUser()
    {
        return Auth::user();
    }

    public static function checkUserOwnership($taskId)
    {
        $userOwnership = new AuthUserOwnershipChecker();
        return $userOwnership->findAndAuthorizeTask($taskId);
    }
}

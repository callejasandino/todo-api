<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskAttachementController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->controller(AuthController::class)->group(function ($auth) {
    $auth->post('login', 'login');
    $auth->post('register', 'register');
    $auth->post('logout', 'logout');
});

// Route::prefix('auth')->middleware('auth:sanctum')->group(function ($router) {
//     $router->get('validateToken', [AuthController::class, 'validateToken']);
// });

Route::middleware('auth:sanctum')->group(function ($route) {
    $route->prefix('task')->controller(TaskController::class)->group(function ($task) {
        $task->get('index', 'index');
        $task->get('sort', 'sort');
        $task->get('filter', 'filter');
        $task->get('show/{taskId}', 'show');
        $task->post('store', 'store');
        $task->put('update', 'update');
        $task->put('mark-task', 'markTask');
        $task->put('archive-task', 'archiveTask');
        $task->delete('delete/{taskId}', 'delete');
    });

    $route->prefix('tag')->controller(TagController::class)->group(function ($tag) {
        $tag->post('store', 'store');
    });

    $route->prefix('task-attachment')->controller(TaskAttachementController::class)->group(function ($taskAttachment) {
        $taskAttachment->post('upload', 'upload');
    });

    $route->prefix('auth')->controller(AuthController::class)->group(function ($auth) {
        $auth->get('validate-token', 'validateToken');
    });
});

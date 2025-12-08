<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    /**
     * Set task status to in progress
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $status = $request->get('status');

        if(Task::validate($status) === false){
            return redirect()->back()->withErrors('Hibás státusz!');
        }

        $task->setStatus($status);
        $task->save();

        return redirect()->back();
    }
}

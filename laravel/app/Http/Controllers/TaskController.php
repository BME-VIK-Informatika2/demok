<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    static array $status = [
        Task::NEW => ['title' => 'Új', 'class' => 'text-primary'],
        Task::IN_PROGRESS => ['title' => 'Folyamatban', 'class' => 'text-warning'],
        Task::DONE => ['title' => 'Kész', 'class' => 'text-secondary']
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        $status = self::$status;

        $tasks = Task::query()
            ->orderByRaw("FIELD(status, 'new', 'in_progress', 'done'), id")
            ->get();

        return view('pages.index', compact('tasks', 'status'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [], [
            'title' => 'cím',
            'description' => 'leírás'
        ]);

        $task = new Task($request->input());
        $task->save();

        return redirect()->back()->with('success', 'Feladat sikeresen létrehozva!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $status = self::$status;
        return view('pages.edit', compact('task', 'status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => ['required', Rule::in(array_keys(self::$status))]
        ], [], [
            'title' => 'cím',
            'description' => 'leírás'
        ]);

        $task->update($request->input());
        $task->save();

        return redirect()->route('task.index')->with('success', 'Feladat sikeresen módosítva lett!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->back()->with('success', 'Sikeres törlés!');
    }
}

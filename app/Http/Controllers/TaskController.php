<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function __construct()
    {
        // ✅ Manually authorize 'destroy' action since we use soft deletes/trash
        $this->authorizeResource(Task::class, 'task', [
            'except' => ['destroy'],
        ]);
    }

    public function index()
    {
        $q = request('q');

        $tasks = Task::query()
            ->visibleTo(auth()->user())
            ->when($q, function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%");
            })
            ->orderByRaw("CASE WHEN status='open' THEN 0 ELSE 1 END")
            ->orderBy('due_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tasks.index', compact('tasks', 'q'));
    }

    public function create()
    {
        $users = [];
        if (auth()->user()->hasRole('admin')) {
            $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);
        }

        $leads = \App\Models\Lead::query()
            ->visibleTo(auth()->user())
            ->latest()
            ->limit(50)
            ->get();

        $customers = \App\Models\Customer::query()
            ->visibleTo(auth()->user())
            ->latest()
            ->limit(50)
            ->get();

        return view('tasks.create', compact('users', 'leads', 'customers'));
    }

    /**
     * Store a newly created task in storage.
     * Uses StoreTaskRequest for validation and authorization.
     */
    public function store(\App\Http\Requests\StoreTaskRequest $request)
    {
        // 1. Get the validated and cleaned data from the Form Request
        $data = $request->validated();

        // 2. Mutate due_date and due_time into a single due_at timestamp
        if (!empty($data['due_date'])) {
            $time = $data['due_time'] ?? '09:00';
            $data['due_at'] = Carbon::parse($data['due_date'] . ' ' . $time);
        }
        unset($data['due_date'], $data['due_time']);

        // 3. Assign the task to the correct user (Admin can assign to others)
        $data['assigned_to'] = auth()->user()->hasRole('admin') 
            ? ($data['assigned_to'] ?? auth()->id()) 
            : auth()->id();

        // 4. Create the task in the database
        $task = Task::create($data);

        // 5. Log the activity 
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'task_created',
            'subject_type' => Task::class,
            'subject_id'   => $task->id,
            'meta'         => [
                'task' => $task->only([
                    'id', 'title', 'type', 'status', 'due_at',
                    'assigned_to', 'lead_id', 'customer_id'
                ]),
                // ✅ Added for activity-card rendering convenience
                'task_id' => $task->id,
            ],
        ]);

        // 6. Redirect back with a success message
        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    // ✅ Important: Correct model signature to prevent 403 errors
    public function show(Task $task)
    {
        // authorizeResource automatically handles authorize('view', $task)
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $users = [];
        if (auth()->user()->hasRole('admin')) {
            $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);
        }

        $leads = \App\Models\Lead::query()
            ->visibleTo(auth()->user())
            ->latest()
            ->limit(50)
            ->get();

        $customers = \App\Models\Customer::query()
            ->visibleTo(auth()->user())
            ->latest()
            ->limit(50)
            ->get();

        return view('tasks.edit', compact('task', 'users', 'leads', 'customers'));
    }

    /**
     * Update the specified task in storage.
     * Uses UpdateTaskRequest for validation and authorization.
     */
    public function update(\App\Http\Requests\UpdateTaskRequest $request, Task $task)
    {
        // 1. Get validated data (clean and safe)
        $data = $request->validated();

        // 2. Mutate due_date and due_time into a single due_at timestamp
        $dueAt = null;
        if (!empty($data['due_date'])) {
            $time = $data['due_time'] ?? '09:00';
            $dueAt = \Illuminate\Support\Carbon::parse($data['due_date'] . ' ' . $time);
        }
        unset($data['due_date'], $data['due_time']);
        $data['due_at'] = $dueAt;

        // 3. Admin logic: if admin didn't send 'assigned_to', keep the old one
        if (auth()->user()->hasRole('admin')) {
            $data['assigned_to'] = $data['assigned_to'] ?? $task->assigned_to;
        }

        // 4. Capture state BEFORE the update (for activity diff)
        $before = [
            'title'       => $task->title,
            'notes'       => $task->notes,
            'type'        => $task->type,
            'status'      => $task->status,
            'due_at'      => $task->due_at?->toDateTimeString(),
            'assigned_to' => (int) $task->assigned_to,
            'lead_id'     => $task->lead_id ? (int)$task->lead_id : null,
            'customer_id' => $task->customer_id ? (int)$task->customer_id : null,
        ];

        // 5. Perform the UPDATE in database
        $task->update($data);
        $task = $task->fresh();

        // 6. Capture state AFTER the update
        $after = [
            'title'       => $task->title,
            'notes'       => $task->notes,
            'type'        => $task->type,
            'status'      => $task->status,
            'due_at'      => $task->due_at?->toDateTimeString(),
            'assigned_to' => (int) $task->assigned_to,
            'lead_id'     => $task->lead_id ? (int)$task->lead_id : null,
            'customer_id' => $task->customer_id ? (int)$task->customer_id : null,
        ];

        // 7. Calculate the difference (diff) for the activity log
        $diff = [];
        foreach ($after as $k => $v) {
            if (($before[$k] ?? null) != $v) {
                $diff[$k] = ['from' => $before[$k] ?? null, 'to' => $v];
            }
        }

        // 8. Log the activity if anything actually changed
        if (!empty($diff)) {
            ActivityLog::create([
                'user_id'      => auth()->id(),
                'action'       => 'task_updated',
                'subject_type' => Task::class,
                'subject_id'   => $task->id,
                'meta'         => [
                    'task' => $task->only([
                        'id', 'title', 'type', 'status', 'due_at',
                        'assigned_to', 'lead_id', 'customer_id'
                    ]),
                    'task_id'  => $task->id,
                    'changes'  => $diff,
                ],
            ]);
        }

        // 9. Redirect back
        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }
    
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        // ✅ Log BEFORE delete
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'task_deleted',
            'subject_type' => Task::class,
            'subject_id'   => $task->id,
            'meta'         => [
                'task_id' => $task->id,
                'title'   => $task->title,
            ],
        ]);

        // ✅ Delete
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted.');
    }

    public function trash()
    {
        $this->authorize('viewTrash', Task::class);

        $tasks = Task::onlyTrashed()
            ->trashVisibleTo(auth()->user())
            ->latest()
            ->paginate(10);

        return view('tasks.trash', compact('tasks'));
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $task);

        $task->restore();

        return redirect()->route('tasks.trash')->with('success', 'Task restored.');
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $task);

        $task->forceDelete();

        return redirect()->route('tasks.trash')->with('success', 'Task permanently deleted.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'status' => ['required', 'in:open,done,canceled'],
        ]);

        $before = $task->status;

        $task->update([
            'status' => $data['status'],
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'task_status_updated',
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'meta' => [
                'task' => $task->only([
                    'id', 'title', 'type', 'status', 'due_at',
                    'assigned_to', 'lead_id', 'customer_id'
                ]),
                'task_id' => $task->id,
                'changes' => [
                    'status' => ['from' => $before, 'to' => $task->status],
                ],
            ],
        ]);


        return back()->with('success', 'Task status updated.');
    }
}
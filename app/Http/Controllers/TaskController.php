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
        // ✅ destroy را خودمان دستی authorize می‌کنیم
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'type' => ['required', 'in:call,meeting,follow_up,email'],
            'status' => ['required', 'in:open,done,canceled'],

            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],

            'lead_id' => ['nullable', 'exists:leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $dueAt = null;
        if (!empty($data['due_date'])) {
            $time = $data['due_time'] ?? '09:00';
            $dueAt = Carbon::parse($data['due_date'] . ' ' . $time);
        }

        unset($data['due_date'], $data['due_time']);
        $data['due_at'] = $dueAt;

        if (auth()->user()->hasRole('admin')) {
            $data['assigned_to'] = $data['assigned_to'] ?? auth()->id();
        } else {
            $data['assigned_to'] = auth()->id();
        }

        $user = auth()->user();

        // ✅ اگر هر دو انتخاب شده بود، customer را ترجیح می‌دهیم و lead را null می‌کنیم
        if (!empty($data['lead_id']) && !empty($data['customer_id'])) {
            $data['lead_id'] = null;
        }

        // ✅ غیر admin اجازه ندارد lead/customer دیگران را به task وصل کند
        if (!$user->hasRole('admin')) {

            if (!empty($data['lead_id'])) {
                $ok = \App\Models\Lead::query()
                    ->visibleTo($user)
                    ->whereKey($data['lead_id'])
                    ->exists();

                if (!$ok) abort(403, 'You cannot attach this lead to the task.');
            }

            if (!empty($data['customer_id'])) {
                $ok = \App\Models\Customer::query()
                    ->visibleTo($user)
                    ->whereKey($data['customer_id'])
                    ->exists();

                if (!$ok) abort(403, 'You cannot attach this customer to the task.');
            }
        }


        $task = Task::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'task_created',
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'meta' => [
                'task' => $task->only([
                    'id', 'title', 'type', 'status', 'due_at',
                    'assigned_to', 'lead_id', 'customer_id'
                ]),
                // ✅ برای راحتی activity-card
                'task_id' => $task->id,
            ],
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    // ✅ مهم: امضای صحیح برای جلوگیری از 403
    public function show(Task $task)
    {
        // authorizeResource خودش authorize(view) را انجام می‌دهد
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



            public function update(Request $request, Task $task)
    {
        $user = auth()->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'type'  => ['required', 'in:call,meeting,follow_up,email'],
            'status'=> ['required', 'in:open,done,canceled'],

            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],

            'lead_id'     => ['nullable', 'exists:leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],

            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        // ✅ اگر هر دو انتخاب شده بود، customer را ترجیح می‌دهیم و lead را null می‌کنیم
        if (!empty($data['lead_id']) && !empty($data['customer_id'])) {
            $data['lead_id'] = null;
        }

        // ✅ غیر admin حق ندارد task را به کس دیگری assign کند
        if ($user->hasRole('admin')) {
            $data['assigned_to'] = $data['assigned_to'] ?? $task->assigned_to;
        } else {
            unset($data['assigned_to']);
        }

        // ✅ کنترل دسترسی به lead/customer انتخاب‌شده (sales فقط چیزهای خودش)
        if (!$user->hasRole('admin')) {
            if (!empty($data['lead_id'])) {
                $ok = \App\Models\Lead::query()
                    ->visibleTo($user)
                    ->whereKey($data['lead_id'])
                    ->exists();

                if (!$ok) abort(403, 'You cannot attach this lead to the task.');
            }

            if (!empty($data['customer_id'])) {
                $ok = \App\Models\Customer::query()
                    ->visibleTo($user)
                    ->whereKey($data['customer_id'])
                    ->exists();

                if (!$ok) abort(403, 'You cannot attach this customer to the task.');
            }
        }

        // ✅ ساخت due_at از due_date + due_time (اگر due_date خالی شد => due_at null)
        $dueAt = null;
        if (!empty($data['due_date'])) {
            $time = $data['due_time'] ?? '09:00';
            $dueAt = Carbon::parse($data['due_date'] . ' ' . $time);
        }
        unset($data['due_date'], $data['due_time']);
        $data['due_at'] = $dueAt;

        // ✅ BEFORE برای diff
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

        // ✅ UPDATE
        $task->update($data);
        $task = $task->fresh();

        // ✅ AFTER برای diff
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

        // ✅ diff به فرم activity-card: changes[field] = {from,to}
        $diff = [];
        foreach ($after as $k => $v) {
            if (($before[$k] ?? null) != $v) {
                $diff[$k] = ['from' => $before[$k] ?? null, 'to' => $v];
            }
        }

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

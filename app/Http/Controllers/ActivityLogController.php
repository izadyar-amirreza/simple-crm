<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLogController extends Controller
{
    
        public function __construct()
    {
        $this->middleware('permission:activity.view');
    }

        public function index(Request $request)
    {
        $q = ActivityLog::query()->with('user');

        // action filter
        if ($request->filled('action')) {
            $q->where('action', $request->string('action')->toString());
        }

        // type filter
        if ($request->filled('type')) {
            $type = $request->string('type')->toString();

            if ($type === 'customer') $q->where('subject_type', Customer::class);
            if ($type === 'lead')     $q->where('subject_type', Lead::class);
            if ($type === 'task')     $q->where('subject_type', Task::class);
        }

        // user filter
        if ($request->filled('user_id')) {
            $q->where('user_id', (int) $request->input('user_id'));
        }

        // search
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $q->where(function ($qq) use ($search) {
                $qq->where('action', 'like', "%{$search}%")
                   ->orWhere('meta', 'like', "%{$search}%")
                   ->orWhere('subject_type', 'like', "%{$search}%")
                   ->orWhere('subject_id', 'like', "%{$search}%");
            });
        }

        $logs = $q->latest()->paginate(20)->withQueryString();

        // ✅ dropdown ها
        $actions = ActivityLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');
        $users   = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        // ✅ collect ids from subject/meta
        $customerIds = [];
        $leadIds     = [];
        $taskIds     = [];

        foreach ($logs->items() as $log) {
            $meta = is_array($log->meta)
                ? $log->meta
                : (json_decode((string) $log->meta, true) ?? []);

            $subjectType = $log->subject_type ?? null;
            $subjectId   = $log->subject_id ?? null;

            $cid = $meta['customer_id'] ?? ($subjectType === Customer::class ? $subjectId : null);
            $lid = $meta['lead_id']     ?? ($subjectType === Lead::class ? $subjectId : null);

            // task_id ممکن است در meta یا subject باشد
            $tid = $meta['task_id']
                ?? ($meta['task']['id'] ?? null)
                ?? ($subjectType === Task::class ? $subjectId : null);

            if ($cid) $customerIds[] = (int) $cid;
            if ($lid) $leadIds[]     = (int) $lid;
            if ($tid) $taskIds[]     = (int) $tid;
        }

        $customerIds = array_values(array_unique($customerIds));
        $leadIds     = array_values(array_unique($leadIds));
        $taskIds     = array_values(array_unique($taskIds));

        // ✅ fetch maps (no extra queries in blade)
        $customersMap = Customer::withTrashed()
            ->whereIn('id', $customerIds)
            ->get()
            ->keyBy('id');

        $leadsMap = Lead::withTrashed()
            ->whereIn('id', $leadIds)
            ->get()
            ->keyBy('id');

        // ✅ Task: اگر SoftDeletes دارد → withTrashed
        $taskQuery = Task::query();
        if (in_array(SoftDeletes::class, class_uses_recursive(Task::class), true)) {
            $taskQuery->withTrashed();
        }

        $tasksMap = $taskQuery
            ->whereIn('id', $taskIds)
            ->get()
            ->keyBy('id');

        // ✅ این قسمت دقیقا همان چیزی است که در فایل شما حذف/جا افتاده بود
        // ✅ Grouped Timeline (day → entity → user)
        $grouped = $logs->getCollection()
            ->groupBy(fn($log) => $log->created_at->toDateString())
            ->map(function ($dayLogs) {
                return $dayLogs
                    ->groupBy(function ($log) {
                        $type = $log->subject_type ?? 'unknown';
                        $id   = $log->subject_id ?? '0';
                        return $type . '|' . $id;
                    })
                    ->map(fn($entityLogs) => $entityLogs->groupBy(fn($log) => $log->user_id ?? 0));
            });

        return view('activity.index', compact(
            'logs',
            'grouped',
            'customersMap',
            'leadsMap',
            'tasksMap',
            'actions',
            'users'
        ));
    }
}

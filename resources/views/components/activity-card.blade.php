@props(['log', 'customersMap' => [], 'leadsMap' => [], 'tasksMap' => []])

@php
    // badge color
    $badge = match($log->badgeColor()) {
        'emerald' => 'bg-emerald-500/15 text-emerald-200 border-emerald-500/25',
        'blue'    => 'bg-sky-500/15 text-sky-200 border-sky-500/25',
        'red'     => 'bg-red-500/15 text-red-200 border-red-500/25',
        'amber'   => 'bg-amber-500/15 text-amber-200 border-amber-500/25',
        'purple'  => 'bg-violet-500/15 text-violet-200 border-violet-500/25',
        default   => 'bg-slate-500/15 text-slate-200 border-slate-500/25',
    };

    $icon = $log->iconName();

    $subjectType = $log->subject_type ?? $log->loggable_type ?? null;
    $subjectId   = $log->subject_id   ?? $log->loggable_id   ?? null;

    $meta = is_array($log->meta) ? $log->meta : (json_decode($log->meta, true) ?? []);

    // Resolve IDs
    $customerId = $meta['customer_id'] ?? ($subjectType === \App\Models\Customer::class ? $subjectId : null);
    $leadId     = $meta['lead_id']     ?? ($subjectType === \App\Models\Lead::class ? $subjectId : null);
    $taskId     = $meta['task_id']     ?? ($subjectType === \App\Models\Task::class ? $subjectId : null);

    // Resolve Models (passed from controller maps)
    $customer = $customerId ? ($customersMap[$customerId] ?? null) : null;
    $lead     = $leadId     ? ($leadsMap[$leadId] ?? null) : null;
    $task     = $taskId     ? ($tasksMap[$taskId] ?? null) : null;

    $leadIsTrashed     = $lead && method_exists($lead, 'trashed') ? $lead->trashed() : false;
    $customerIsTrashed = $customer && method_exists($customer, 'trashed') ? $customer->trashed() : false;
    $taskIsTrashed     = $task && method_exists($task, 'trashed') ? $task->trashed() : false;

    // Subject deep-link (used for the generic Subject ID display)
    $subjectUrl = null;
    if ($subjectType === \App\Models\Customer::class && $subjectId && !$customerIsTrashed) {
        $subjectUrl = route('customers.show', $subjectId);
    } elseif ($subjectType === \App\Models\Lead::class && $subjectId && !$leadIsTrashed) {
        $subjectUrl = route('leads.show', $subjectId);
    } elseif ($subjectType === \App\Models\Task::class && $subjectId && !$taskIsTrashed) {
        $subjectUrl = route('tasks.show', $subjectId);
    }

    $changes = $meta['changes'] ?? null;

    $sentence = method_exists($log,'sentence')
        ? $log->sentence()
        : (method_exists($log,'label') ? $log->label() : ($log->action ?? 'Activity'));

    $customerName = $customer?->name ?? null;
    $leadName     = $lead?->name ?? null;
    $taskTitle    = $task?->title ?? null;

    $createdAt = $log->created_at?->diffForHumans() ?? '';
@endphp

<div class="bg-slate-900/50 border border-white/10 rounded-xl p-4 flex items-start justify-between gap-4">
    <div class="flex items-start gap-3">
        <div class="mt-0.5">
            <div class="w-9 h-9 rounded-lg border border-white/10 bg-white/5 flex items-center justify-center">
                @if($icon === 'plus')
                    <span class="text-white/80">＋</span>
                @elseif($icon === 'pencil')
                    <span class="text-white/80">✎</span>
                @elseif($icon === 'trash')
                    <span class="text-white/80">🗑</span>
                @elseif($icon === 'restore')
                    <span class="text-white/80">↩</span>
                @elseif($icon === 'arrow')
                    <span class="text-white/80">➜</span>
                @else
                    <span class="text-white/80">•</span>
                @endif
            </div>
        </div>

        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-xs {{ $badge }}">
                    {{ $log->label() ?? $log->action }}
                </span>

                <span class="text-white/90 text-sm font-medium">
                    {{ $sentence }}
                </span>

                <div class="flex flex-wrap items-center gap-2 text-xs">
                    {{-- Customer --}}
                    @if($customer)
                        @can('view', $customer)
                            <span class="text-white/40 text-xs">•</span>
                            <a href="{{ route('customers.activity', $customer->id) }}"
                               class="text-xs underline underline-offset-2 hover:text-white text-white/80">
                                {{ $customerName ?? ('Customer #'.$customer->id) }}
                            </a>
                        @endcan
                    @elseif($customerId)
                        <span class="text-white/40 text-xs">•</span>
                        <span class="text-xs text-white/50">Customer #{{ $customerId }}</span>
                    @endif

                    {{-- Lead --}}
                    @if($lead && !$leadIsTrashed)
                        @if(auth()->user()?->can('view', $lead))
                            <span class="text-white/40 text-xs">•</span>
                            <a href="{{ route('leads.show', $lead) }}"
                               class="text-xs underline underline-offset-2 hover:text-white text-white/80">
                                {{ $leadName ?? ('Lead #'.$lead->id) }}
                            </a>
                        @endif
                    @elseif($leadId)
                        <span class="text-white/40 text-xs">•</span>
                        <s class="text-xs text-white/50">Lead #{{ $leadId }} (deleted)</s>
                    @endif

                    {{-- Task --}}
                    @if($task && !$taskIsTrashed)
                        @can('view', $task)
                            <span class="text-white/40 text-xs">•</span>
                            <a href="{{ route('tasks.show', $task) }}"
                               class="text-xs underline underline-offset-2 hover:text-white text-white/80">
                                {{ $taskTitle ?? ('Task #'.$task->id) }}
                            </a>
                        @else
                            <span class="text-white/40 text-xs">•</span>
                            <span class="text-xs text-white/50" title="No access to this task">
                                Task #{{ $task->id }}
                            </span>
                        @endcan
                    @elseif($taskId)
                        <span class="text-white/40 text-xs">•</span>
                        <span class="text-xs text-white/50">Task #{{ $taskId }} (deleted)</span>
                    @endif

                    @if($subjectType && $subjectId)
                        <span class="text-xs text-white/40">(ID:
                            @if($subjectUrl)
                                <a href="{{ $subjectUrl }}"
                                   class="text-xs underline underline-offset-2 hover:text-white text-white/80">
                                    {{ $subjectId }}
                                </a>
                            @else
                                {{ $subjectId }}
                            @endif
                        )</span>
                    @endif
                </div>

                <div class="text-xs text-white/50 mt-1">
                    By:
                    <span class="text-white/70">{{ optional($log->user)->name ?? 'N/A' }}</span>
                    <span class="text-white/40">({{ optional($log->user)->email ?? '' }})</span>
                </div>
            </div>
        </div>
    </div>

    <div class="text-xs text-white/50 text-right">
        {{ $createdAt }}
    </div>
</div>

@if($changes && is_array($changes))
    <div class="mt-3 bg-slate-900/30 border border-white/10 rounded-xl p-4">
        <div class="text-xs font-semibold text-white/70 mb-2">Changes</div>

        <div class="space-y-2">
            @foreach($changes as $field => $change)
                <div class="text-xs text-white/70">
                    <span class="text-white/90 font-medium">{{ $field }}</span>
                    <span class="text-white/40">:</span>

                    <span class="text-white/50">from</span>
                    <span class="text-white/80">
                        {{ is_array($change) ? ($change['from'] ?? '') : '' }}
                    </span>

                    <span class="text-white/50">to</span>
                    <span class="text-white/80">
                        {{ is_array($change) ? ($change['to'] ?? '') : '' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
@endif

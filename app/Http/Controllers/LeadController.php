<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct()
    {
        // Policy resource methods
        $this->authorizeResource(Lead::class, 'lead');
    }

    public function index()
    {
        $leads = Lead::query()
            ->visibleTo(auth()->user())
            ->latest()
            ->paginate(10);

        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        $owners = [];

        if (auth()->user()->hasRole('admin')) {
            $owners = User::orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('leads.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['nullable', 'email', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:new,contacted,qualified,lost'],
        ];

        if ($request->user()->hasRole('admin')) {
            $rules['owner_id'] = ['nullable', 'exists:users,id'];
        }

        $data = $request->validate($rules);

        $ownerId = $request->user()->id;
        if ($request->user()->hasRole('admin')) {
            $ownerId = $request->input('owner_id') ?: $request->user()->id;
        }

        Lead::create([
            'name'     => $data['name'],
            'email'    => $data['email']  ?? null,
            'phone'    => $data['phone']  ?? null,
            'source'   => $data['source'] ?? null,
            'status'   => $data['status'] ?? 'new',
            'owner_id' => $ownerId,
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);

        $owners = [];

        if (auth()->user()->hasRole('admin')) {
            $owners = User::orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('leads.edit', compact('lead', 'owners'));
    }

    public function update(Request $request, Lead $lead)
    {
        if ($lead->status === 'converted') {
            return back()->with('error', 'Converted leads cannot be edited.');
        }

        $rules = [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['nullable', 'email', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:new,contacted,qualified,lost'],
        ];

        if ($request->user()->hasRole('admin')) {
            $rules['owner_id'] = ['nullable', 'exists:users,id'];
        }

        $data = $request->validate($rules);

        if ($request->user()->hasRole('admin')) {
            $data['owner_id'] = $request->input('owner_id') ?: $lead->owner_id;
        } else {
            unset($data['owner_id']);
        }

        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }

        public function trash()
    {
        $this->authorize('viewTrash', Lead::class);

        $leads = Lead::onlyTrashed()
            ->trashVisibleTo(auth()->user())
            ->latest()
            ->paginate(10);

        return view('leads.trash', compact('leads'));
    }

    public function restore($id)
    {
        $lead = Lead::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $lead);

        $lead->restore();

        return redirect()->route('leads.trash')->with('success', 'Lead restored.');
    }

    public function forceDelete($id)
    {
        $lead = Lead::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $lead);

        $lead->forceDelete();

        return redirect()->route('leads.trash')->with('success', 'Lead permanently deleted.');
    }

    /**
     * Convert lead to customer
     */
        public function convert(Lead $lead)
    {
        $this->authorize('convert', $lead);

        if ($lead->status === 'converted' || $lead->customer_id) {
            return back()->with('error', 'This lead is already converted.');
        }

        $customer = null;

        if (!empty($lead->email)) {
            $customer = Customer::where('email', $lead->email)->first();
        }

        if (!$customer) {
            $ownerId = auth()->id();

            // اگر admin دارد convert می‌کند، مالک customer را مالک lead قرار بده
            if (auth()->user()->hasRole('admin') && $lead->owner_id) {
                $ownerId = $lead->owner_id;
            }

            $customer = Customer::create([
                'name'     => $lead->name,
                'email'    => $lead->email,
                'phone'    => $lead->phone,
                'owner_id' => $ownerId,
                'notes'    => $lead->notes,
            ]);
        }

        $lead->update([
            'status'       => 'converted',
            'customer_id'  => $customer->id,
            'converted_at' => now(),
        ]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'lead_converted',
            'subject_type' => Lead::class,
            'subject_id'   => $lead->id,
            'meta'         => [
                'lead_id'     => $lead->id,
                'customer_id' => $customer->id,
            ],
        ]);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Lead converted to customer successfully.');
    }


    public function show(Lead $lead)
    {
        $tasks = \App\Models\Task::query()
            ->visibleTo(auth()->user())
            ->where('lead_id', $lead->id)
            ->latest()
            ->paginate(5);

        return view('leads.show', compact('lead', 'tasks'));
    }
}

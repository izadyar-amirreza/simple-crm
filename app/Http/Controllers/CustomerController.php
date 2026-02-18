<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Models\ActivityLog;


class CustomerController extends Controller
{
    
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Customer::class, 'customer');
    }
    
        public function index()
    {
        $q = request('q');

        $customers = Customer::query()->visibleTo(auth()->user());

        $customers = $customers
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'q'));
    }


            public function trash()
    {
        
        $this->authorize('viewTrash', Customer::class);
        
        $query = Customer::onlyTrashed()->trashVisibleTo(auth()->user());

        $customers = $query->latest()->paginate(10);

        return view('customers.trash', compact('customers'));
    }



        public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $customer);

        $customer->restore();

        return redirect()->route('customers.trash')->with('success', 'Customer restored.');
    }


        public function forceDelete($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $customer);

        $customer->forceDelete();

        return redirect()
            ->route('customers.trash')
            ->with('success', 'Customer permanently deleted.');
    }




        public function create()
    {
        $owners = [];

        if (auth()->user()->hasRole('admin')) {
            $owners = User::orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('customers.create', compact('owners'));
    }

   

        public function show(Customer $customer)
    {
        $tasks = \App\Models\Task::query()
        ->visibleTo(auth()->user())
        ->where('customer_id', $customer->id)
        ->latest()
        ->paginate(5);

        return view('customers.show', compact('customer', 'tasks'));
    }


        public function activity($id)
    {
        $customer = \App\Models\Customer::withTrashed()->findOrFail($id);

        $this->authorize('view', $customer);

         $logs = \App\Models\ActivityLog::query()
        ->where('subject_type', \App\Models\Customer::class)
        ->where('subject_id', $customer->id)
        ->latest()
        ->paginate(10);
        $grouped = $logs->getCollection()
        ->groupBy(fn($log) => $log->created_at->toDateString())
        ->map(fn($dayLogs) => $dayLogs->groupBy(fn($log) => $log->user_id ?? 0));

        return view('customers.activity', compact('customer', 'logs', 'grouped'));

    }




        public function edit(Customer $customer)
    {
        $owners = [];

        if (auth()->user()->hasRole('admin')) {
            $owners = User::orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('customers.edit', compact('customer', 'owners'));
    }


        public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->safe()->only(['name', 'email', 'phone', 'notes']);

        if ($request->user()->hasRole('admin')) {
            $data['owner_id'] = $request->validated('owner_id') ?? $customer->owner_id;
        }

        $customer->update($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }







        public function destroy(Customer $customer)
    {

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');

    }


   

            public function store(StoreCustomerRequest $request)
    {
        $data = $request->safe()->only(['name','email','phone','notes']);

        $ownerId = auth()->id();

        if (auth()->user()->hasRole('admin')) {
            $ownerId = $request->input('owner_id') ?: auth()->id();
        }

        $customer = Customer::create([
            ...$data,
            'owner_id' => $ownerId,
        ]);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }



        

}

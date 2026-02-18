{{-- Name --}}
<div>
    <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-gray-200">Name</label>
    <input
        type="text"
        name="name"
        value="{{ old('name', $customer?->name) }}"
        class="w-full rounded-lg border border-slate-300 dark:border-gray-700 px-4 py-2.5
               bg-white dark:bg-gray-900 text-slate-900 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        required
    />
    @error('name') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

{{-- Email --}}
<div>
    <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-gray-200">Email</label>
    <input
        type="email"
        name="email"
        value="{{ old('email', $customer?->email) }}"
        class="w-full rounded-lg border border-slate-300 dark:border-gray-700 px-4 py-2.5
               bg-white dark:bg-gray-900 text-slate-900 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
    />
    @error('email') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

{{-- Phone --}}
<div>
    <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-gray-200">Phone</label>
    <input
        type="text"
        name="phone"
        value="{{ old('phone', $customer?->phone) }}"
        class="w-full rounded-lg border border-slate-300 dark:border-gray-700 px-4 py-2.5
               bg-white dark:bg-gray-900 text-slate-900 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
    />
    @error('phone') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

{{-- Notes --}}
<div>
    <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-gray-200">Notes</label>
    <textarea
        name="notes"
        rows="4"
        class="w-full rounded-lg border border-slate-300 dark:border-gray-700 px-4 py-2.5
               bg-white dark:bg-gray-900 text-slate-900 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
    >{{ old('notes', $customer?->notes) }}</textarea>
    @error('notes') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

{{-- Owner (admin فقط) --}}
@if(auth()->user()->hasRole('admin'))
    <div>
        <label class="block mb-1 text-sm font-medium text-slate-700 dark:text-gray-200">Owner</label>

        {{-- نکته مهم: حتماً "border" دارد تا select دیده شود --}}
        <select
            name="owner_id"
            class="w-full rounded-lg border border-slate-300 dark:border-gray-700 px-4 py-2.5
                   bg-white dark:bg-gray-900 text-slate-900 dark:text-gray-100
                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        >
            <option value="">-- Me --</option>
            @foreach($owners as $o)
                <option value="{{ $o->id }}" @selected(old('owner_id', $customer?->owner_id) == $o->id)>
                    {{ $o->name }} ({{ $o->email }})
                </option>
            @endforeach
        </select>

        @error('owner_id') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
@endif

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Lead;
use App\Models\Customer;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * (Check permissions before processing the update)
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // 1. Admin has full access
        if ($user->hasRole('admin')) {
            return true;
        }

        // 2. Check if the user owns the selected Lead
        if ($this->filled('lead_id')) {
            $hasLead = Lead::query()
                ->visibleTo($user)
                ->whereKey($this->input('lead_id'))
                ->exists();
                
            if (!$hasLead) {
                return false; // Automatically returns 403 Forbidden
            }
        }

        // 3. Check if the user owns the selected Customer
        if ($this->filled('customer_id')) {
            $hasCustomer = Customer::query()
                ->visibleTo($user)
                ->whereKey($this->input('customer_id'))
                ->exists();
                
            if (!$hasCustomer) {
                return false; 
            }
        }

        return true;
    }

    /**
     * Prepare the data for validation.
     * (Clean and mutate request data before validation)
     */
    protected function prepareForValidation(): void
    {
        // Prioritize customer if both are sent
        if ($this->filled('lead_id') && $this->filled('customer_id')) {
            $this->merge([
                'lead_id' => null,
            ]);
        }

        // 🚨 IMPORTANT SECURITY FIX: 
        // Non-admins cannot assign tasks to someone else.
        // We completely remove 'assigned_to' from their request so they can't hack it.
        if (!$this->user()->hasRole('admin') && $this->has('assigned_to')) {
            $this->request->remove('assigned_to');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'notes'       => ['nullable', 'string'],
            'type'        => ['required', 'in:call,meeting,follow_up,email'],
            'status'      => ['required', 'in:open,done,canceled'],
            'due_date'    => ['nullable', 'date'],
            'due_time'    => ['nullable', 'date_format:H:i'],
            'lead_id'     => ['nullable', 'exists:leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
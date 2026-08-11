<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Lead;
use App\Models\Customer;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * (Check permissions before processing the request)
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // 1. Admin has full access to create tasks for anyone
        if ($user->hasRole('admin')) {
            return true;
        }

        // 2. Check if the user owns the selected Lead (if provided)
        if ($this->filled('lead_id')) {
            $hasLead = Lead::query()
                ->visibleTo($user)
                ->whereKey($this->input('lead_id'))
                ->exists();
                
            if (!$hasLead) {
                return false; // Automatically triggers a 403 Forbidden response
            }
        }

        // 3. Check if the user owns the selected Customer (if provided)
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
     * (Mutate or clean request data before validation rules are applied)
     */
    protected function prepareForValidation(): void
    {
        // If both lead_id and customer_id are provided, prioritize customer and nullify lead
        if ($this->filled('lead_id') && $this->filled('customer_id')) {
            $this->merge([
                'lead_id' => null,
            ]);
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
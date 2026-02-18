<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use App\Models\Customer;
use App\Models\Lead;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic subject
    public function subject()
    {
        return $this->morphTo(null, 'subject_type', 'subject_id');
    }

    /**
     * مرکز کنترل UI اکشن‌ها (label/badge/icon)
     */
    public function ui(): array
    {
        $map = [
            // Customers
            'customer_created' => ['label' => 'Customer created', 'badge' => 'emerald', 'icon' => 'plus'],
            'customer_updated' => ['label' => 'Customer updated', 'badge' => 'blue',    'icon' => 'pencil'],
            'customer_deleted' => ['label' => 'Customer deleted', 'badge' => 'red',     'icon' => 'trash'],
            'customer_restored'=> ['label' => 'Customer restored','badge' => 'amber',   'icon' => 'restore'],
            'customer_created_from_lead' => ['label' => 'Customer created from lead', 'badge' => 'emerald', 'icon' => 'spark'],

            // Leads
            'lead_created' => ['label' => 'Lead created', 'badge' => 'emerald', 'icon' => 'plus'],
            'lead_updated' => ['label' => 'Lead updated', 'badge' => 'blue',    'icon' => 'pencil'],
            'lead_deleted' => ['label' => 'Lead deleted', 'badge' => 'red',     'icon' => 'trash'],
            'lead_converted' => ['label' => 'Lead converted', 'badge' => 'purple', 'icon' => 'arrow'],

            // optional
            'customer_linked_from_lead' => ['label' => 'Lead linked to existing customer', 'badge' => 'purple', 'icon' => 'link'],
            'lead_converted_to_existing_customer' => ['label' => 'Lead converted to existing customer', 'badge' => 'purple', 'icon' => 'link'],

            // Tasks
            'task_created'        => ['label' => 'Task created',        'badge' => 'emerald', 'icon' => 'plus'],
            'task_updated'        => ['label' => 'Task updated',        'badge' => 'blue',    'icon' => 'pencil'],
            'task_deleted'        => ['label' => 'Task deleted',        'badge' => 'red',     'icon' => 'trash'],
            'task_status_updated' => ['label' => 'Task status updated', 'badge' => 'purple',  'icon' => 'arrow'],
            
            'customer_force_deleted' => ['label' => 'Customer permanently deleted', 'badge' => 'red', 'icon' => 'trash'],

            'lead_restored'      => ['label' => 'Lead restored', 'badge' => 'amber', 'icon' => 'restore'],
            'lead_force_deleted' => ['label' => 'Lead permanently deleted', 'badge' => 'red', 'icon' => 'trash'],

        ];

        return $map[$this->action] ?? ['label' => $this->action, 'badge' => 'slate', 'icon' => 'dot'];
    }

    public function label(): string
    {
        return $this->ui()['label'];
    }

    public function badgeColor(): string
    {
        return $this->ui()['badge'];
    }

    public function iconName(): string
    {
        return $this->ui()['icon'];
    }

    /**
     * متن ساده (بدون لینک) برای جاهایی که HTML لازم نیست
     */
    public function sentence(): string
    {
        return $this->label();
    }

    /**
     * ✅ Sentence لینک‌دار مثل GitHub
     * خروجی HTML امن (escaped) + لینک‌ها فقط وقتی که موجود و مجاز باشند.
     * نکته: اینجا دیگه اسم کاربر (actor) رو نمی‌ذاریم چون در کارت جدا نمایش میدی.
     */
    public function sentenceHtml(): HtmlString
    {
        $meta = is_array($this->meta) ? $this->meta : (json_decode((string)$this->meta, true) ?? []);

        // ids
        $customerId = $meta['customer_id'] ?? ($this->subject_type === Customer::class ? $this->subject_id : null);
        $leadId     = $meta['lead_id']     ?? ($this->subject_type === Lead::class ? $this->subject_id : null);

        // names (fallback)
        $customerName = data_get($meta, 'customer.name') ?? data_get($meta, 'customer_name');
        $leadName     = data_get($meta, 'lead.name')     ?? data_get($meta, 'lead_name');

        // load models (withTrashed safe)
        $customer = null;
        $lead = null;

        if ($customerId) {
            try {
                $customer = Customer::withTrashed()->find($customerId);
            } catch (\Throwable $e) {
                $customer = null;
            }
        }

        if ($leadId) {
            try {
                $lead = Lead::withTrashed()->find($leadId);
            } catch (\Throwable $e) {
                $lead = null;
            }
        }

        // final texts
        $customerText = $customerName ?: ($customer ? $customer->name : null);
        $leadText     = $leadName     ?: ($lead ? $lead->name : null);

        $customerChunk = $this->linkedCustomerChunk($customer, $customerId, $customerText);
        $leadChunk     = $this->linkedLeadChunk($lead, $leadId, $leadText);

        $html = match ($this->action) {
            'customer_created' => "Customer created {$customerChunk}",
            'customer_updated' => "Customer updated {$customerChunk}",
            'customer_deleted' => "Customer deleted {$customerChunk}",
            'customer_restored'=> "Customer restored {$customerChunk}",

            'lead_created'     => "Lead created {$leadChunk}",
            'lead_updated'     => "Lead updated {$leadChunk}",
            'lead_deleted'     => "Lead deleted {$leadChunk}",

            'lead_converted'   => "Lead converted {$leadChunk} to customer {$customerChunk}",
            'customer_created_from_lead' => "Customer created {$customerChunk} from lead {$leadChunk}",
            'customer_linked_from_lead' => "Lead {$leadChunk} linked to customer {$customerChunk}",
            'lead_converted_to_existing_customer' => "Lead {$leadChunk} converted to existing customer {$customerChunk}",

            default => e($this->label()),
        };

        return new HtmlString($html);
    }

    private function linkedCustomerChunk($customer, $customerId, $text): string
    {
        $label = e($text ?: ($customerId ? "Customer #{$customerId}" : 'Customer'));

        // اگر مدل هست و مجاز → لینک به customers.activity
        if ($customer && Gate::allows('view', $customer)) {
            $url = route('customers.activity', $customer->id);
            return $this->a($url, $label);
        }

        // اگر فقط id داریم ولی دسترسی/وجود نداریم → متن ساده
        return "<span class=\"text-white/80\">{$label}</span>";
    }

    private function linkedLeadChunk($lead, $leadId, $text): string
    {
        $label = e($text ?: ($leadId ? "Lead #{$leadId}" : 'Lead'));

        // soft-deleted؟ لینک نده
        if ($lead && method_exists($lead, 'trashed') && $lead->trashed()) {
            return "<span class=\"text-white/60\">{$label} (deleted)</span>";
        }

        // اول view → leads.show
        if ($lead && Gate::allows('view', $lead)) {
            $url = route('leads.show', $lead);
            return $this->a($url, $label);
        }

        // اگر view نداره ولی update داره → leads.edit
        if ($lead && Gate::allows('update', $lead)) {
            $url = route('leads.edit', $lead);
            return $this->a($url, $label);
        }

        // fallback
        return "<span class=\"text-white/80\">{$label}</span>";
    }

    private function a(string $url, string $label): string
    {
        $url = e($url);

        return "<a href=\"{$url}\" class=\"text-indigo-200 hover:text-indigo-100 underline underline-offset-4\">
                    {$label}
                </a>";
    }
}

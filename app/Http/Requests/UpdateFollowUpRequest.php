<?php

namespace App\Http\Requests;

use App\Models\FollowUp;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFollowUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'scheduled_date' => 'required|date',
            'contact_method' => 'required|in:phone,whatsapp,email,in_person',
            'type' => 'required|in:initial_contact,course_inquiry,payment_reminder,enrollment_follow_up,customer_service,other',
            'purpose' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'required|in:high,medium,low',
            'course_id' => 'nullable|exists:courses,id',
            // Legacy fields for backward compatibility
            'action_note' => 'nullable|string|max:1000',
            'outcome' => 'nullable|in:' . implode(',', FollowUp::getOutcomes()),
            'status' => 'nullable|in:' . implode(',', array_keys(FollowUp::getStatuses())),
            'next_follow_up_date' => 'nullable|date|after_or_equal:today',
            'cancellation_reason' => 'nullable|in:' . implode(',', FollowUp::getCancellationReasons()),
            'cancellation_details' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'scheduled_date.required' => __('follow_ups.scheduled_date_required'),
            'scheduled_date.date' => __('follow_ups.scheduled_date_invalid'),
            'contact_method.required' => __('follow_ups.contact_method_required'),
            'contact_method.in' => __('follow_ups.contact_method_invalid'),
            'type.required' => __('follow_ups.type_required'),
            'type.in' => __('follow_ups.type_invalid'),
            'purpose.required' => __('follow_ups.purpose_required'),
            'purpose.max' => __('follow_ups.purpose_max'),
            'notes.max' => __('follow_ups.notes_max'),
            'priority.required' => __('follow_ups.priority_required'),
            'priority.in' => __('follow_ups.priority_invalid'),
            'course_id.exists' => __('follow_ups.course_invalid'),
            'action_note.max' => __('follow_ups.action_note_max'),
            'outcome.in' => __('follow_ups.outcome_invalid'),
            'status.in' => __('follow_ups.status_invalid'),
            'next_follow_up_date.date' => __('follow_ups.next_follow_up_date_invalid'),
            'next_follow_up_date.after_or_equal' => __('follow_ups.next_follow_up_date_future'),
            'cancellation_reason.in' => __('follow_ups.cancellation_reason_invalid'),
            'cancellation_details.max' => __('follow_ups.cancellation_details_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'scheduled_date' => __('follow_ups.scheduled_date'),
            'contact_method' => __('follow_ups.contact_method'),
            'type' => __('follow_ups.type'),
            'purpose' => __('follow_ups.purpose'),
            'notes' => __('follow_ups.notes'),
            'priority' => __('follow_ups.priority'),
            'course_id' => __('follow_ups.course'),
            'action_note' => __('follow_ups.action_note'),
            'outcome' => __('follow_ups.outcome'),
            'status' => __('follow_ups.status'),
            'next_follow_up_date' => __('follow_ups.next_follow_up_date'),
            'cancellation_reason' => __('follow_ups.cancellation_reason'),
            'cancellation_details' => __('follow_ups.cancellation_details'),
        ];
    }
}

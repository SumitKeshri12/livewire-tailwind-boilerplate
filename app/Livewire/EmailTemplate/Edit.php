<?php

namespace App\Livewire\EmailTemplate;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class Edit extends Component
{
    public $emailTemplate;

    public $id;

    public $type;

    public $label;

    public $subject;

    public $body;

    public $status = 'Y';

    public $statusLabels = ['Inactive', 'Active'];

    public $legendsArray = [];

    public function mount($id)
    {
        if (! Gate::allows('edit-emailtemplates')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->id = $id;
        $this->emailTemplate = EmailTemplate::find($id);

        if ($this->emailTemplate) {
            foreach ($this->emailTemplate->getAttributes() as $key => $value) {
                $this->{$key} = $value;
            }
        }

        // Load legends from Helper
        $this->legendsArray = Helper::getAllLegends();

        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.email_template.breadcrumb.title'),
            'item_1' => __('messages.email_template.breadcrumb.sub_title'),
            'item_2' => __('messages.email_template.breadcrumb.create'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */
    }

    public function rules()
    {
        return [
            'subject' => 'required|string',
            'body' => 'required|string',
            'status' => 'required|in:Y,N',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'The subject is required.',
            'subject.string' => 'The subject must be a string.',
            'body.required' => 'The body is required.',
            'body.string' => 'The body must be a string.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either Y (Active) or N (Inactive).',
        ];
    }

    public function store()
    {
        $this->validate();

        $data = [
            'subject' => $this->subject,
            'body' => $this->body,
            'status' => $this->status,
        ];

        EmailTemplate::where('id', $this->id)->update($data);

        // $this->emailTemplate->update($data);

        session()->flash('success', __('messages.email_template.messages.update_success'));

        return $this->redirect('/email-templates', navigate: true);
    }

    public function render()
    {
        $types = EmailTemplate::types();

        return view('livewire.email-template.edit', compact('types'));
    }
}

<?php

namespace App\Livewire\EmailTemplate;

use App\Livewire\Breadcrumb;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    public $title;

    public function mount()
    {
        if (! Gate::allows('view-emailtemplates')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        /* Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.email_template.breadcrumb.title'),
            'item_1' => __('messages.email_template.breadcrumb.sub_title'),
            'item_2' => __('messages.email_template.breadcrumb.create'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return view('livewire.email-template.index');
    }
}

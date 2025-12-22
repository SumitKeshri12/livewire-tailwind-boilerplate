<?php

namespace App\Livewire\Role;

use App\Livewire\Breadcrumb;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    public $title;

    public function mount()
    {
        /* Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.role.breadcrumb.title'),
            'item_1' => __('messages.role.breadcrumb.role'),
            'item_1_href' => route('role.index'),
            'item_2' => __('messages.role.breadcrumb.list'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $baseTitle = __('messages.meta_title.index_role');
        $this->title = $baseTitle;

        return view('livewire.role.index')->title(__('messages.meta_title.index_role'));
    }
}

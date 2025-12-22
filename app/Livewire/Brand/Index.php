<?php

namespace App\Livewire\Brand;

use App\Livewire\Breadcrumb;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class Index extends Component
{
    public $title;

    public function mount()
    {
        if (! Gate::allows('view-brand')) {
            abort(Response::HTTP_FORBIDDEN);
        }
        /* Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.brand.breadcrumb.title'),
            'item_1' => __('messages.brand.breadcrumb.brand'),
            'item_1_href' => route('brand.index'),
            'item_2' => __('messages.brand.breadcrumb.list'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
    }

    public function addBrand()
    {
        $this->redirect(route('brand.create'), navigate: true);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $baseTitle = __('messages.meta_title.index_brand');
        $this->title = $baseTitle;

        return view('livewire.brand.index')->title(__('messages.meta_title.index_brand'));
    }
}

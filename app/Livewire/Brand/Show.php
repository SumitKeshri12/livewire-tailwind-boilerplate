<?php

namespace App\Livewire\Brand;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public $id;

    public $brand;

    public $event = 'showbrandInfoModal';

    #[On('show-brand-info')]
    public function show($id)
    {
        $this->brand = null;

        $this->brand = Brand::select(
            'brands.id',
            'countries.name as country_name',
            DB::raw(
                '(CASE
                                        WHEN brands.status = "' . config('constants.brand.status.key.active') . '" THEN  "' . config('constants.brand.status.value.active') . '"
                                        WHEN brands.status = "' . config('constants.brand.status.key.inactive') . '" THEN  "' . config('constants.brand.status.value.inactive') . '"
                                ELSE " "
                                END) AS status'
            ),
            'brands.bob',
            'brands.start_date',
            'brands.start_time'
        )
            ->leftJoin('countries', 'countries.id', '=', 'brands.country_id')
            ->where('brands.id', $id)
            ->groupBy('brands.id')
            ->first();
        if (! is_null($this->brand)) {
            $this->dispatch('show-modal', id: '#' . $this->event);
        } else {
            session()->flash('error', __('messages.brand.messages.record_not_found'));

        }
    }

    public function render()
    {
        return view('livewire.brand.show');
    }
}

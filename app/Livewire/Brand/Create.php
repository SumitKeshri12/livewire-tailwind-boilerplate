<?php

namespace App\Livewire\Brand;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\Brand;
use App\Models\BrandDetail;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class Create extends Component
{
    use WithFileUploads;

    public $id;

    public $country_id;

    public $countries = [];

    public $status = 'N';

    public $bob;

    public $start_date;

    public $start_time;

    public $adds = [];

    public $newAdd = [
        'description' => '',
        'status' => '',
        'brand_image' => '',
        'show_brand_image' => '',
        'id' => 0,
    ];

    public $isEdit = false;

    public function mount()
    {
        if (! Gate::allows('add-brand')) {
            abort(Response::HTTP_FORBIDDEN);
        }
        /* begin::Set breadcrumb */
        $segmentsData = [
            'item_1' => __('messages.brand.breadcrumb.title'),
            'item_2' => __('messages.brand.breadcrumb.create'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->countries = Helper::getAllCountry();
        $this->adds[] = $this->newAdd;
    }

    public function rules()
    {
        $rules = [
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'status' => 'required|in:Y,N',
            'bob' => 'required',
            'start_date' => 'required',
        ];
        foreach ($this->adds as $index => $add) {
            $rules["adds.$index.description"] = 'required';
            $rules["adds.$index.status"] = 'required|in:Y,N';
            $rules["adds.$index.brand_image"] = 'image|mimes:jpeg,png,jpg,gif,svg|max:4096';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'country_id.required' => __('messages.brand.validation.messsage.country_id.required'),
            'adds.*.description.required' => __('messages.brand.validation.messsage.description.required'),
            'adds.*.status.required' => __('messages.brand.validation.messsage.status.required'),
            'adds.*.status.in' => __('messages.brand.validation.messsage.status.in'),
            'bob.required' => __('messages.brand.validation.messsage.bob.required'),
            'bob.date_format' => __('messages.brand.validation.messsage.bob.date_format'),
            'start_date.required' => __('messages.brand.validation.messsage.start_date.required'),
            'start_date.date_format' => __('messages.brand.validation.messsage.start_date.date_format'),
        ];
    }

    public function updatedStatus($value)
    {
        $this->status = $value ? 'Y' : 'N';
    }

    public function store()
    {
        $this->validate();
        $data = [
            'country_id' => $this->country_id,
            'status' => $this->status,
            'bob' => $this->bob,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time ?? null,
        ];
        $brand = Brand::create($data);

        foreach ($this->adds as $add) {
            $BrandDetailId = $add['id'] ?? 0;
            $BrandDetailInfo = BrandDetail::find($BrandDetailId);
            $BrandDetailData = [
                'description' => $add['description'],
                'status' => $add['status'],
                'brand_id' => $brand->id,
            ];
            if ($BrandDetailInfo) {
                BrandDetail::where('id', $BrandDetailId)->update($BrandDetailData);
            } else {
                $BrandDetailInfo = BrandDetail::create($BrandDetailData);
            }

            if (! empty($add['brand_image'])) {
                $realPath = 'branddetail/' . $BrandDetailInfo->id . '/';
                $resizedImage = BrandDetail::resizeImages($add['brand_image'], $realPath, true);
                $imagePath = $realPath . pathinfo($resizedImage['image'], PATHINFO_BASENAME);
                $BrandDetailInfo->update(['brand_image' => $imagePath]);
            }
        }

        session()->flash('success', __('messages.brand.messages.success'));

        return $this->redirect('/brand', navigate: true); // redirect to brand listing page
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        return view('livewire.brand.create')->title(__('messages.meta_title.create_brand'));
    }

    public function add()
    {
        if (count($this->adds) < 5) {
            $this->adds[] = $this->newAdd;
        } else {
            $this->dispatch('alert', type: 'error', message: __('messages.maximum_record_limit_error'));
        }
    }

    public function remove($index, $id)
    {
        if ($id != 0) {
            BrandDetail::where('id', $id)->forceDelete();
        }
        unset($this->adds[$index]);
        $this->adds = array_values($this->adds);
    }
}

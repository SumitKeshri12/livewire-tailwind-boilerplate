<?php

namespace App\Livewire\Brand;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\Brand;
use App\Models\BrandDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class Edit extends Component
{
    use WithFileUploads;

    public $brand;

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

    public $isEdit = true;

    /**
     * @param $brand
     */
    public function mount($id)
    {
        if (! Gate::allows('edit-brand')) {
            abort(Response::HTTP_FORBIDDEN);
        }
        /* begin::Set breadcrumb */
        $segmentsData = [
            'item_1' => __('messages.brand.breadcrumb.title'),
            'item_2' => __('messages.brand.breadcrumb.edit'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->brand = Brand::find($id);

        if ($this->brand) {
            foreach ($this->brand->getAttributes() as $key => $value) {
                $this->{$key} = $value; // Dynamically assign the attributes to the class
            }
        }

        $this->countries = Helper::getAllCountry();
        $brandDetailInfo = BrandDetail::select('description', 'status', 'brand_image', 'id')->where('brand_id', $id)->get();
        if ($brandDetailInfo->isNotEmpty()) {
            foreach ($brandDetailInfo as $index => $addInfo) {
                $this->adds[] = [
                    'description' => $addInfo->description,
                    'status' => $addInfo->status,
                    'brand_image' => $addInfo->brand_image,
                    'id' => $addInfo->id,
                ];
            }
        } else {
            $this->adds = [$this->newAdd];
        }
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
            // $rules["adds.$index.brand_image"] = "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096";
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
            'start_time' => $this->start_time,
        ];
        $this->brand->update($data); // Update data into the DB

        foreach ($this->adds as $add) {
            $BrandDetailId = $add['id'] ?? 0;
            $brandDetailInfo = BrandDetail::find($BrandDetailId);
            $BrandDetailData = [
                'description' => $add['description'],
                'status' => $add['status'],
                'brand_id' => $this->brand->id,
            ];
            if ($brandDetailInfo) {
                BrandDetail::where('id', $BrandDetailId)->update($BrandDetailData);
            } else {
                $brandDetailInfo = BrandDetail::create($BrandDetailData);
            }

            if (! empty($add['brand_image'])) {
                $realPath = 'branddetail/' . $brandDetailInfo->id . '/';
                $resizedImage = BrandDetail::resizeImages($add['brand_image'], $realPath, true);
                $imagePath = $realPath . pathinfo($resizedImage['image'], PATHINFO_BASENAME);
                $brandDetailInfo->update(['brand_image' => $imagePath]);
            }
        }

        session()->flash('success', __('messages.brand.messages.update'));

        return $this->redirect('/brand', navigate: true); // redirect to brand listing page
    }

    public function render()
    {
        return view('livewire.brand.edit')->title(__('messages.meta_title.edit_brand'));
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

<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DropzoneComponent extends Component
{
    public $importData;

    public $userID;

    /**
     * mount
     *
     * @param mixed $importData
     */
    public function mount($importData)
    {
        $user = Auth::user();
        $this->importData = $importData;
        $this->userID = $user->id;
    }

    public function downloadSampleCsv()
    {
        if ($this->importData['modelName'] == config('constants.import_csv_log.models.role')) {
            $filePath = public_path('samples/import_sample_role.csv');
        } elseif ($this->importData['modelName'] == config('constants.import_csv_log.models.user')) {
            $filePath = public_path('samples/import_sample_user.csv');
        } elseif ($this->importData['modelName'] == config('constants.import_csv_log.models.brand')) {
            $filePath = public_path('samples/import_sample_brand.csv');
        } elseif ($this->importData['modelName'] == config('constants.import_csv_log.models.smstemplate')) {
            $filePath = public_path('samples/import_sample_smstemplate.csv');
        } elseif ($this->importData['modelName'] == config('constants.import_csv_log.models.pushnotificationtemplate')) {
            $filePath = public_path('samples/import_sample_pushnotificationtemplate.csv');
        } elseif ($this->importData['modelName'] == config('constants.import_csv_log.models.whatsapptemplate')) {
            $filePath = public_path('samples/import_sample_whatsapptemplate.csv');
        } else {
            $filePath = ''; // Default file path
        }

        if ($filePath != '') {
            return response()->download($filePath);
        } else {
            session()->flash('error', __('messages.something_went_wrong'));
        }
    }

    public function render()
    {
        return view('livewire.dropzone-component');
    }
}

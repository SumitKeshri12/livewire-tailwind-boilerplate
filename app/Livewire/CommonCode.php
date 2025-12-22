<?php

namespace App\Livewire;

use App\Helper;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class CommonCode extends Component
{
    #[On('showExportProgressEvent')]
    public function showExportProgress($functionParams)
    {
        try {
            // Skip Livewire rendering for this method
            $this->skipRender();

            // Process export progress using Helper method
            $result = Helper::processProgressOfExport($functionParams);

            if ($result['status'] == 0) {
                // Dispatch error alert if status is 0
                session()->flash('error', $result['message']);
                $this->dispatch('stopExportProgressEvent');

                return false;
            } elseif ($result['status'] == 1) {
                // Dispatch event to update export progress if status is 1
                $this->dispatch('updateExportProgress', $result['data']);
            } elseif ($result['status'] == 2) {
                // Dispatch success alert & hide export progress div if status is 2
                session()->flash('success', $result['message']);
                $this->dispatch('stopExportProgressEvent');
                // $this->dispatch('downloadExportFileEvent', $result['data']);

                return $result['data'];
            } else {
                // Log error if status is not recognized
                logger()->error('App\Livewire\CommonCode: showExportProgress: Status not found in processProgressOfExport', ['functionParams' => $functionParams, 'result' => $result]);

                return false;
            }
        } catch (Throwable $e) {
            // Log and dispatch error alert if exception occurs
            logger()->error('App\Livewire\CommonCode: showExportProgress: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString(), 'functionParams' => $functionParams]);
            session()->flash('error', __('messages.common_error_message'));

            return false;
        }
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
        </div>
        HTML;
    }
}

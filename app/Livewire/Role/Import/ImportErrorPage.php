<?php

namespace App\Livewire\Role\Import;

use Livewire\Component;

class ImportErrorPage extends Component
{
    public $errorLogs;

    #[\Livewire\Attributes\On('viewImportErrors')]
    /**
     * show
     *
     * @param mixed $opusPcId
     */
    public function updateBlockStatus($errorLogs)
    {
        $this->errorLogs = json_decode($errorLogs);
        $this->dispatch('show-modal', id: '#importErrorShowModal');
    }

    /**
     * closeModal
     */
    public function closeModal()
    {
        $this->dispatch('hide-modal', id: '#importErrorShowModal');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        return view('livewire.role.import.import-error-page');
    }
}

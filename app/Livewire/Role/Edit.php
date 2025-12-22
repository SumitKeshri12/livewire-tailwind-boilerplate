<?php

namespace App\Livewire\Role;

use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $role;
    public $id;
    public $name;
    public $status = 'N';
    public $statusedit = false;

    /**
     * @param $role
     */
    public function mount($id)
    {
        $this->role = Role::find($id);

        if ($this->role) {
            foreach ($this->role->getAttributes() as $key => $value) {
                $this->{$key} = $value; // Dynamically assign the attributes to the class
                if ($key == 'status') {
                    $this->statusedit = $value == 'Y' ? true : false;
                }
            }
        }
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|max:50|unique:roles,name,' . $this->role->id,
            'status' => 'required|in:Y,N',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.role.validation.messsage.name.required'),
            'name.in' => __('messages.role.validation.messsage.name.in'),
            'name.max' => __('messages.role.validation.messsage.name.max'),
        ];
    }

    public function updatedStatusedit($value)
    {
        $this->status = $value ? 'Y' : 'N';
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'status' => $this->status,
        ];
        $this->role->update($data); // Update data into the DB
        Cache::forget('getAllRole');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => __('messages.role.messages.update'),
        ]);

        return $this->redirect('/role', navigate: true); // redirect to role listing page
    }

    public function render()
    {
        return view('livewire.role.edit')->title(__('messages.meta_title.edit_role'));
    }
}

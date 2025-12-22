<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public ?User $user = null;

    public $title;

    public $event = 'showUserInfoModal';

    #[On('show-user-info')]
    public function show($id)
    {
        $this->user = null;

        if (! Gate::allows('view-user')) {
            session()->flash('error', __('messages.user.messages.access_denied'));

            return;
        }

        // Using join query with userHobbies, userDocuments, and userLanguages relationships
        $this->user = User::query()
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
            ->leftJoin('states', 'users.state_id', '=', 'states.id')
            ->leftJoin('cities', 'users.city_id', '=', 'cities.id')
            ->leftJoin('user_hobbies', function ($join) {
                $join->on('users.id', '=', 'user_hobbies.user_id')
                    ->whereNull('user_hobbies.deleted_at');
            })
            ->leftJoin('user_documents', function ($join) {
                $join->on('users.id', '=', 'user_documents.user_id')
                    ->whereNull('user_documents.deleted_at');
            })
            ->leftJoin('user_languages', function ($join) {
                $join->on('users.id', '=', 'user_languages.user_id')
                    ->whereNull('user_languages.deleted_at');
            })
            ->select([
                'users.*',
                'roles.name as role_name',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
                DB::raw('GROUP_CONCAT(DISTINCT user_hobbies.hobby) as hobbies_list'),
                DB::raw('GROUP_CONCAT(DISTINCT user_documents.document_path) as documents_list'),
                DB::raw('GROUP_CONCAT(DISTINCT user_languages.language) as languages_list'),
            ])
            ->where('users.id', $id)
            ->groupBy('users.id')
            ->first();

        if (! is_null($this->user)) {
            $this->dispatch('show-modal', id: '#' . $this->event);
        } else {
            session()->flash('error', __('messages.user.messages.record_not_found'));
        }
    }

    public function render()
    {
        return view('livewire.user.show');
    }
}

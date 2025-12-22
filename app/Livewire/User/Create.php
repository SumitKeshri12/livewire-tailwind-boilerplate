<?php

namespace App\Livewire\User;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserHobby;
use App\Models\UserLanguage;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class Create extends Component
{
    use WithFileUploads;

    public $id;

    public $name;

    public $email;

    public $password;

    public $role_id;

    public $roles = [];

    public $dob;

    public $profile_image;

    public $country_id;

    public $countries = [];

    public $state_id;

    public $states = [];

    public $city_id;

    public $cities = [];

    public $gender;

    public $status = 'N';

    public $description;

    public $comments;

    public $hobbies;

    public $skills;

    public $languages = [];

    public $bg_color;

    public $timezone;

    public $event_date;

    public $event_datetime;

    public $event_time;

    public $document_image = [];

    public $previous_document_image = [];

    public $terms_accepted = false;

    public $privacy_policy_accepted = false;

    public $data_processing_consent = false;

    public $marketing_consent = false;

    public $title;

    public function mount()
    {
        if (! Gate::allows('add-user')) {
            abort(Response::HTTP_FORBIDDEN);
        }
        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.user.breadcrumb.title'),
            'item_1' => __('messages.user.breadcrumb.user'),
            'item_1_href' => route('users.index'),
            'item_2' => __('messages.user.breadcrumb.create'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->roles = Helper::getAllRoles();
        $this->countries = Helper::getAllCountry();
        $this->states = []; // Start with empty states
        $this->cities = []; // Start with empty cities
        $this->skills = []; // Start with empty skills
        $this->previous_document_image = []; // Initialize previous document image tracker
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|max:200|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|min:12|max:191|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/',
            'role_id' => 'required|exists:roles,id,deleted_at,NULL',
            'dob' => 'required|date_format:' . config('constants.validation_date_format'),
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'state_id' => 'required|exists:states,id,deleted_at,NULL',
            'city_id' => 'required|exists:cities,id,deleted_at,NULL',
            'gender' => 'required|in:F,M',
            'status' => 'required|in:Y,N',
            'description' => 'required|string',
            'comments' => 'nullable|string|max:1000',
            'hobbies' => 'nullable|array',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'bg_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'timezone' => 'required|string|max:100',
            'event_date' => 'nullable|date',
            'event_datetime' => 'nullable|date_format:Y-m-d\TH:i',
            'event_time' => 'nullable|date_format:' . config('constants.validation_time_format'),
            'document_image' => 'required|array|max:' . config('constants.user.file.document.max_files'),
            'document_image.*' => 'file|mimes:pdf,doc,docx|max:' . config('constants.user.file.document.max_size'),
            'terms_accepted' => 'required|accepted',
            'privacy_policy_accepted' => 'required|accepted',
            'data_processing_consent' => 'required|accepted',
            'marketing_consent' => 'nullable',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name field must not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.max' => 'The email field must not be greater than 255 characters.',
            'email.email' => 'The email field must be a valid email address.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 12 characters and include uppercase, lowercase, numbers, and special characters.',
            'password.max' => 'The password field must not be greater than 191 characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).',
            'role_id.required' => 'The role field is required.',
            'dob.required' => 'The date of birth field is required.',
            'dob.date_format' => 'The date of birth field must be in the format Y-m-d.',
            'profile_image.required' => 'The profile image field is required.',
            'profile_image.max' => 'The profile image field must not be greater than 2048 kilobytes.',
            'country_id.required' => 'The country field is required.',
            'state_id.required' => 'The state field is required.',
            'city_id.required' => 'The city field is required.',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The selected gender is invalid.',
            'status.required' => 'The status field is required.',
            'description.required' => 'The description field is required.',
            'comments.max' => 'The comments field must not be greater than 1000 characters.',
            'timezone.in' => 'The selected timezone is invalid.',
            'timezone.max' => 'The timezone field must not be greater than 255 characters.',
            'event_time.date_format' => 'The event time field must be in the format Y-m-d H:i:s.',
            'document_image.max' => 'You can upload maximum ' . config('constants.user.file.document.max_files') . ' documents.',
            'document_image.*.max' => 'The file size is too large. Maximum allowed size is ' . config('constants.user.file.document.max_size') . ' KB (approximately ' . round(config('constants.user.file.document.max_size') / 1024, 1) . ' MB). Please select a smaller file.',
            'document_image.*.file' => 'Invalid file format. Please upload only PDF, DOC, or DOCX files.',
            'document_image.*.mimes' => 'Invalid file format. Please upload only PDF, DOC, or DOCX files.',
            'terms_accepted.required' => 'You must accept the terms and conditions.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
            'privacy_policy_accepted.required' => 'You must accept the privacy policy.',
            'privacy_policy_accepted.accepted' => 'You must accept the privacy policy.',
            'data_processing_consent.required' => 'You must consent to data processing.',
            'data_processing_consent.accepted' => 'You must consent to data processing.',
        ];
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Dispatch custom event for error scrolling
            $this->dispatch(config('constants.user.events.validation_failed'));

            // Re-throw the exception so Livewire can display validation errors properly
            throw $e;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role_id' => $this->role_id,
            'dob' => $this->dob,
            'profile' => $this->profile_image,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'status' => $this->status,
            'description' => $this->description,
            'comments' => $this->comments,
            'skills' => json_encode($this->skills ?? []),
            'bg_color' => $this->bg_color,
            'timezone' => $this->timezone,
            'event_date' => $this->event_date,
            'event_datetime' => $this->event_datetime,
            'event_time' => $this->event_time,
            'consent_data' => [
                'terms_accepted' => $this->terms_accepted,
                'privacy_policy_accepted' => $this->privacy_policy_accepted,
                'data_processing_consent' => $this->data_processing_consent,
                'marketing_consent' => $this->marketing_consent ? true : false,
            ],
        ];

        $user = User::create($data);

        // Save hobbies to user_hobbies table using bulk insert
        if (! empty($this->hobbies) && is_array($this->hobbies)) {
            $hobbiesData = array_filter($this->hobbies);

            if (! empty($hobbiesData)) {
                $insertData = array_map(fn($hobby) => [
                    'user_id' => $user->id,
                    'hobby' => $hobby,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $hobbiesData);

                UserHobby::insert($insertData);
            }
        }

        // Save languages to user_languages table using bulk insert
        if (! empty($this->languages) && is_array($this->languages)) {
            $languagesData = array_filter($this->languages);

            if (! empty($languagesData)) {
                $insertData = array_map(fn($language) => [
                    'user_id' => $user->id,
                    'language' => $language,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $languagesData);

                UserLanguage::insert($insertData);
            }
        }

        if ($this->profile_image) {
            $realPath = config('constants.user.file.profile.directory') . $user->id . '/';
            $resizeImages = $user->resizeImages($this->profile_image, $realPath, true, false, 'public');
            $imagePath = $realPath . pathinfo($resizeImages['image'], PATHINFO_BASENAME);
            $user->update(['profile' => $imagePath]);
        }

        if ($this->document_image && count($this->document_image) > 0) {
            $realPath = config('constants.user.file.document.directory') . $user->id . '/';

            // Prepare bulk insert data
            $insertData = [];
            foreach ($this->document_image as $document) {
                $documentPath = \App\Traits\UploadTrait::uploadOne($document, $realPath, 'public');
                $insertData[] = [
                    'user_id' => $user->id,
                    'document_path' => $documentPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert documents
            if (! empty($insertData)) {
                UserDocument::insert($insertData);
            }
        }

        // Set session flash message to show on users listing page
        session()->flash('success', __('messages.user.messages.success'));

        // Redirect immediately to users listing page
        return $this->redirect('/users', navigate: true);
    }

    public function render()
    {
        // Dynamic title based on form state
        $baseTitle = __('messages.meta_title.create_user');
        $this->title = $baseTitle;

        return view('livewire.user.create');
    }

    public function updatedStateId()
    {
        // When state_id is updated, load the dependent options
        $this->cities = \App\Models\City::where('state_id', $this->state_id)->get();

        // Reset dependent dropdown
        $this->city_id = null;

        // Dispatch event to reinitialize Quill editor
        $this->dispatch(config('constants.user.events.quill_reinit'));
    }

    public function removeFile($property, $index)
    {
        if (is_array($this->$property) && isset($this->$property[$index])) {
            unset($this->$property[$index]);
            $this->$property = array_values($this->$property); // Re-index array

            // Update previous_document_image if this is document_image property
            if ($property === 'document_image') {
                $this->previous_document_image = $this->document_image;
            }
        }
    }

    public function updatedDocumentImage($value)
    {
        // Ensure properties are arrays
        $this->document_image = is_array($this->document_image) ? $this->document_image : [];
        $this->previous_document_image = is_array($this->previous_document_image) ? $this->previous_document_image : [];

        // Early return if value is invalid or empty
        if (! is_array($value) || empty($value)) {
            $this->previous_document_image = [];

            return;
        }

        // Get counts and max limit
        $previousCount = count($this->previous_document_image);
        $newCount = count($value);
        $maxFiles = config('constants.user.file.document.max_files');
        $totalAfterAdd = $previousCount + $newCount;

        // Validate max files limit - reject if exceeds
        if ($totalAfterAdd > $maxFiles) {
            $this->document_image = $this->previous_document_image; // Revert to previous state
            $remainingSlots = $maxFiles - $previousCount;
            $message = $remainingSlots > 0
                ? "You can only upload {$remainingSlots} more file(s). Currently you have {$previousCount} file(s). Please select only {$remainingSlots} file(s)."
                : "You have reached the maximum limit of {$maxFiles} files. Please remove some files before uploading more.";
            session()->flash('error', $message);

            return;
        }

        // Append new files to existing ones (Livewire replaces array, so we merge)
        $this->document_image = $previousCount > 0
            ? array_merge($this->previous_document_image, $value)
            : $value; // Initial selection

        // Update previous files for next iteration
        $this->previous_document_image = $this->document_image;
    }

    /**
     * Update selected items for searchable dropdown
     *
     * @param array $selectedValues Array of selected values
     */
    public function updateSelected($selectedValues)
    {
        $this->hobbies = $selectedValues;
    }

    /**
     * Update country selection for searchable dropdown
     *
     * @param mixed $countryId Selected country ID
     */
    public function updatedCountryId($countryId)
    {
        // When country_id is updated, load the dependent options
        if ($countryId) {
            $this->states = \App\Models\State::where('country_id', $countryId)->get();
        } else {
            $this->states = [];
        }

        // Reset dependent dropdowns
        $this->state_id = null;
        $this->city_id = null;
        $this->cities = [];

        // Dispatch event to reinitialize Quill editor
        $this->dispatch(config('constants.user.events.quill_reinit'));
    }

    /**
     * Toggle array item for multiselect dropdowns
     *
     * @param string $property The property name (e.g., 'languages')
     * @param mixed $value The value to toggle
     */
    public function toggleArrayItem($property, $value)
    {
        if (!property_exists($this, $property)) {
            return;
        }

        // Initialize as array if not set
        if (!is_array($this->$property)) {
            $this->$property = [];
        }

        // Toggle the value
        if (in_array($value, $this->$property)) {
            // Remove the value
            $this->$property = array_values(array_filter($this->$property, function ($item) use ($value) {
                return $item !== $value;
            }));
        } else {
            // Add the value
            $this->$property[] = $value;
        }
    }
}

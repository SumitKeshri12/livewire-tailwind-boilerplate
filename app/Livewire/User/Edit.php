<?php

namespace App\Livewire\User;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserHobby;
use App\Models\UserLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class Edit extends Component
{
    use WithFileUploads;

    public $id;

    public $name;

    public $email;

    public $password;

    public $role_id;

    public $roles = [];

    public $dob;

    public $profile;

    public $profile_image;

    public $existing_profile_image;

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

    public $languages;

    public $skills;

    public $bg_color;

    public $timezone;

    public $event_date;

    public $event_datetime;

    public $event_time;

    public $document_image = [];

    public $previous_document_image = [];

    public $existing_documents = [];

    public $title;

    public $terms_accepted = false;

    public $privacy_policy_accepted = false;

    public $data_processing_consent = false;

    public $marketing_consent = false;

    public $user;

    public function mount($id)
    {
        // Authorization Check (Privilege Escalation Protection)
        if (! Gate::allows('edit-user')) {
            abort(Response::HTTP_FORBIDDEN);
        }
        
        // Initialize arrays to prevent count() errors
        $this->hobbies = [];
        $this->languages = [];
        $this->skills = [];
        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.user.breadcrumb.title'),
            'item_1' => __('messages.user.breadcrumb.user'),
            'item_1_href' => route('users.index'),
            'item_2' => __('messages.user.breadcrumb.edit'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->id = $id;
        $this->user = User::query()
            ->leftJoin('user_hobbies', function ($join) {
                $join->on('users.id', '=', 'user_hobbies.user_id')
                    ->whereNull('user_hobbies.deleted_at');
            })
            ->leftJoin('user_languages', function ($join) {
                $join->on('users.id', '=', 'user_languages.user_id')
                    ->whereNull('user_languages.deleted_at');
            })
            ->select([
                'users.*',
                DB::raw('GROUP_CONCAT(DISTINCT user_hobbies.hobby) as hobbies_list'),
                DB::raw('GROUP_CONCAT(DISTINCT user_languages.language) as languages_list'),
            ])
            ->where('users.id', $id)
            ->groupBy('users.id')
            ->first();

        // Load existing user data
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->role_id = $this->user->role_id;
        $this->dob = $this->user->dob ? $this->user->dob->format(config('constants.validation_date_format')) : null;
        // Handle profile image - get raw value to avoid accessor issues
        $this->existing_profile_image = $this->user->getRawOriginal('profile');
        $this->country_id = $this->user->country_id;
        $this->state_id = $this->user->state_id;
        $this->city_id = $this->user->city_id;
        $this->gender = $this->user->gender;
        $this->status = $this->user->status;
        $this->description = $this->user->description;
        $this->comments = $this->user->comments;

        // Load hobbies from user_hobbies table (new structure)
        if ($this->user->hobbies_list) {
            // Split the comma-separated hobbies from GROUP_CONCAT
            $this->hobbies = array_filter(explode(',', $this->user->hobbies_list));
        } else {
            // Fallback: Load from old hobbies column (for backward compatibility)
            $rawHobbies = $this->user->getRawOriginal('hobbies');

            if ($rawHobbies) {
                if (is_string($rawHobbies)) {
                    $cleaned = trim($rawHobbies, '"');
                    $decoded = json_decode($cleaned, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $this->hobbies = $this->convertToConstantsFormat($decoded, 'hobbies');
                    }
                } elseif (is_array($rawHobbies)) {
                    $this->hobbies = $this->convertToConstantsFormat($rawHobbies, 'hobbies');
                }
            }
        }

        // Load languages from user_languages table
        if ($this->user->languages_list) {
            // Split the comma-separated languages from GROUP_CONCAT
            $this->languages = array_filter(explode(',', $this->user->languages_list));
        } else {
            $this->languages = [];
        }

        // Load skills - handle both old and new data formats
        $rawSkills = $this->user->getRawOriginal('skills');
        $userSkills = $this->user->skills; // This uses the cast

        $skillsData = [];

        // Try raw original first
        if ($rawSkills) {
            if (is_string($rawSkills)) {
                // Remove any extra quotes and decode
                $cleaned = trim($rawSkills, '"');
                $decoded = json_decode($cleaned, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $skillsData = $decoded;
                }
            } else {
                $skillsData = is_array($rawSkills) ? $rawSkills : [];
            }
        }

        // Fallback to model cast if raw didn't work
        if (empty($skillsData) && $userSkills) {
            if (is_array($userSkills)) {
                $skillsData = $userSkills;
            } elseif (is_string($userSkills)) {
                // Handle single-escaped JSON like "[\"python\",\"react\"]"
                $decoded = json_decode($userSkills, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $skillsData = $decoded;
                }
            }
        }

        // For chips component, we need the actual values, not converted keys
        $this->skills = $skillsData;

        // If still empty, try direct database query
        if (empty($this->skills)) {
            $directSkills = DB::table('users')->where('id', $this->user->id)->value('skills');

            if ($directSkills) {
                // Handle the specific format: "[\"python\",\"react\"]"
                $cleaned = trim($directSkills, '"');
                $decoded = json_decode($cleaned, true);
                if (is_array($decoded)) {
                    $this->skills = $decoded;
                }
            }
        }
        $this->bg_color = $this->user->bg_color;
        $this->timezone = $this->user->timezone;
        $this->event_date = $this->user->event_date ? $this->user->event_date->format('Y-m-d') : null;
        $this->event_datetime = $this->user->event_datetime ? $this->user->event_datetime->format('Y-m-d\TH:i') : null;

        // Handle event_time - ensure it's in H:i format for HTML time input
        if ($this->user->event_time) {
            if (is_string($this->user->event_time)) {
                // If it's a string, try to parse it and format it
                try {
                    $time = \Carbon\Carbon::parse($this->user->event_time);
                    $this->event_time = $time->format('H:i');
                } catch (\Exception $e) {
                    // If parsing fails, try to extract time from string
                    if (preg_match('/(\d{2}:\d{2})/', $this->user->event_time, $matches)) {
                        $this->event_time = $matches[1];
                    } else {
                        $this->event_time = null;
                    }
                }
            } else {
                // If it's already a Carbon instance, format it
                $this->event_time = $this->user->event_time->format('H:i');
            }
        } else {
            $this->event_time = null;
        }
        // Load documents from user_documents table
        $userDocuments = $this->user->userDocuments()->get();
        $this->existing_documents = [];

        if ($userDocuments->isNotEmpty()) {
            foreach ($userDocuments as $userDoc) {
                $cleanPath = html_entity_decode($userDoc->document_path, ENT_QUOTES, 'UTF-8');
                $this->existing_documents[] = Storage::url($cleanPath);
            }
        }

        // Load consent data from user
        if ($this->user->consent_data && is_array($this->user->consent_data)) {
            $this->terms_accepted = $this->user->consent_data['terms_accepted'] ?? false;
            $this->privacy_policy_accepted = $this->user->consent_data['privacy_policy_accepted'] ?? false;
            $this->data_processing_consent = $this->user->consent_data['data_processing_consent'] ?? false;
            $this->marketing_consent = $this->user->consent_data['marketing_consent'] ?? false;
        }

        // Load dropdown data
        $this->roles = Helper::getAllRoles();
        $this->countries = Helper::getAllCountry();

        // Load dependent dropdowns based on existing data
        if ($this->country_id) {
            $this->states = \App\Models\State::where('country_id', $this->country_id)->get();
        } else {
            $this->states = [];
        }

        if ($this->state_id) {
            $this->cities = \App\Models\City::where('state_id', $this->state_id)->get();
        } else {
            $this->cities = [];
        }
    }

    public function rules()
    {
        // Count existing documents to determine if document_image is required
        $existingDocumentCount = $this->user ? $this->user->userDocuments()->count() : 0;
        $hasExistingDocuments = $existingDocumentCount > 0;

        // If there are existing documents, document_image is optional
        // If there are no existing documents, document_image is required
        $documentImageRule = $hasExistingDocuments
            ? 'nullable|array|max:' . config('constants.user.file.document.max_files')
            : 'required|array|min:1|max:' . config('constants.user.file.document.max_files');

        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|max:200|email|unique:users,email,' . $this->id . ',id,deleted_at,NULL',
            'password' => 'nullable|min:12|max:191|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'role_id' => 'required|exists:roles,id,deleted_at,NULL',
            'dob' => 'required|date_format:' . config('constants.validation_date_format'),
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'state_id' => 'required|exists:states,id,deleted_at,NULL',
            'city_id' => 'required|exists:cities,id,deleted_at,NULL',
            'gender' => 'required|in:F,M',
            'status' => 'required|in:Y,N',
            'description' => 'required|string',
            'comments' => 'nullable|string|max:1000',
            'hobbies' => 'nullable|array',
            'languages' => 'nullable|array',
            'skills' => 'nullable|array',
            'bg_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'timezone' => 'required|string|max:100',
            'event_date' => 'nullable|date',
            'event_datetime' => 'nullable|date',
            'event_time' => 'nullable|date_format:' . config('constants.validation_time_format'),
            'document_image' => $documentImageRule,
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
            'password.min' => 'The password must be at least 12 characters and include uppercase, lowercase, numbers, and special characters.',
            'password.max' => 'The password field must not be greater than 191 characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).',
            'role_id.required' => 'The role field is required.',
            'dob.required' => 'The date of birth field is required.',
            'dob.date_format' => 'The date of birth field must be in the format Y-m-d.',
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

    public function update()
    {
        $this->validate();

        // Ensure at least one document exists (either existing or new)
        $existingDocumentCount = $this->user->userDocuments()->count();
        $newDocumentCount = is_array($this->document_image) ? count($this->document_image) : 0;
        $totalDocuments = $existingDocumentCount + $newDocumentCount;

        if ($totalDocuments < 1) {
            $this->addError('document_image', 'At least one document is required. Please upload a document or keep existing documents.');

            return;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'dob' => $this->dob,
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
            'event_time' => $this->event_time ?: null,
            'consent_data' => [
                'terms_accepted' => $this->terms_accepted,
                'privacy_policy_accepted' => $this->privacy_policy_accepted,
                'data_processing_consent' => $this->data_processing_consent,
                'marketing_consent' => $this->marketing_consent ? true : false,
            ],
        ];

        // Only update password if provided
        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        // $this->user->forceFill($data)->save();
        $this->user->update($data);

        // Sync hobbies to user_hobbies table efficiently
        $this->syncUserHobbies();

        // Sync languages to user_languages table efficiently
        $this->syncUserLanguages();

        // Handle profile image upload
        if ($this->profile_image) {
            // Delete old profile image if exists
            $this->deleteOldProfileImage();

            $realPath = config('constants.user.file.profile.directory') . $this->user->id . '/';
            $resizeImages = $this->user->resizeImages($this->profile_image, $realPath, true, false, 'public');
            $imagePath = $realPath . pathinfo($resizeImages['image'], PATHINFO_BASENAME);
            $this->user->update(['profile' => $imagePath]);
        }

        // Handle document upload - sync to user_documents table
        if ($this->document_image && count($this->document_image) > 0) {
            $realPath = config('constants.user.file.document.directory') . $this->user->id . '/';

            // Prepare bulk insert data for new documents
            $insertData = [];
            foreach ($this->document_image as $document) {
                $documentPath = \App\Traits\UploadTrait::uploadOne($document, $realPath, 'public');
                $insertData[] = [
                    'user_id' => $this->user->id,
                    'document_path' => $documentPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert new documents
            if (! empty($insertData)) {
                UserDocument::insert($insertData);
            }
        }

        session()->flash('success', __('messages.user.messages.updated'));

        // Redirect immediately to users listing page
        return $this->redirect('/users', navigate: true);
    }

    public function render()
    {
        // Dynamic title based on form state
        $baseTitle = __('messages.meta_title.edit_user');
        $this->title = $baseTitle;

        return view('livewire.user.edit');
    }

    public function updatedCountryId()
    {
        // When country_id is updated, load the dependent options
        if ($this->country_id) {
            $this->states = \App\Models\State::where('country_id', $this->country_id)->get();
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

    public function updatedStateId()
    {
        // When state_id is updated, load the dependent options
        $this->cities = \App\Models\City::where('state_id', $this->state_id)->get();

        // Reset dependent dropdown
        $this->city_id = null;

        // Dispatch event to reinitialize Quill editor
        $this->dispatch(config('constants.user.events.quill_reinit'));
    }

    /**
     * Delete old profile image from storage
     */
    private function deleteOldProfileImage()
    {
        if ($this->existing_profile_image) {
            // Get the raw profile path (without URL) before deferring
            $profilePath = $this->user->getRawOriginal('profile');

            // Defer file deletion and logging to run after response
            defer(function () use ($profilePath) {
                try {
                    if ($profilePath && Storage::disk('public')->exists($profilePath)) {
                        // Delete the main image file
                        Storage::disk('public')->delete($profilePath);
                    }
                } catch (\Exception $e) {
                    // Log error but don't stop the update process
                    Log::warning('Failed to delete old profile image: ' . $e->getMessage());
                }
            });
        }
    }

    /**
     * Convert data to constants format (keys instead of values)
     * Handles both old format (display names) and new format (keys)
     */
    private function convertToConstantsFormat($data, $type)
    {
        if (! is_array($data) || empty($data)) {
            return [];
        }

        $constants = config("constants.user.{$type}");
        $result = [];

        foreach ($data as $item) {
            // If it's already a key, keep it
            if (isset($constants[$item])) {
                $result[] = $item;
            } else {
                // If it's a display name, find the corresponding key
                $key = array_search($item, $constants);
                if ($key !== false) {
                    $result[] = $key;
                } else {
                    // If no match found, convert to lowercase key
                    $key = strtolower(str_replace(' ', '_', $item));
                    if (isset($constants[$key])) {
                        $result[] = $key;
                    }
                }
            }
        }

        return array_unique($result);
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
     * Sync user hobbies efficiently
     * Only performs database operations for actual changes
     */
    private function syncUserHobbies(): void
    {
        if (! isset($this->hobbies) || ! is_array($this->hobbies)) {
            return;
        }

        // Clean and normalize input
        $newHobbies = array_values(array_filter(array_unique($this->hobbies)));

        // Get existing hobbies (from hobbies_list or fallback to query)
        if ($this->user->hobbies_list) {
            $existingHobbies = array_filter(explode(',', $this->user->hobbies_list));
        } else {
            $existingHobbies = $this->user->userHobbies()->pluck('hobby')->toArray();
        }

        // Early return if nothing changed
        sort($existingHobbies);
        sort($newHobbies);
        if ($existingHobbies === $newHobbies) {
            return;
        }

        // Calculate differences
        $hobbiesToAdd = array_diff($newHobbies, $existingHobbies);
        $hobbiesToDelete = array_diff($existingHobbies, $newHobbies);

        // Bulk delete if needed
        if (! empty($hobbiesToDelete)) {
            $this->user->userHobbies()
                ->whereIn('hobby', $hobbiesToDelete)
                ->delete();
        }

        // Bulk insert if needed (more efficient than individual creates)
        if (! empty($hobbiesToAdd)) {
            $insertData = array_map(fn($hobby) => [
                'user_id' => $this->user->id,
                'hobby' => $hobby,
                'created_at' => now(),
                'updated_at' => now(),
            ], $hobbiesToAdd);

            UserHobby::insert($insertData);
        }
    }

    /**
     * Sync user languages efficiently
     * Only performs database operations for actual changes
     */
    private function syncUserLanguages(): void
    {
        if (! isset($this->languages) || ! is_array($this->languages)) {
            return;
        }

        // Clean and normalize input
        $newLanguages = array_values(array_filter(array_unique($this->languages)));

        // Get existing languages (from languages_list or fallback to query)
        if ($this->user->languages_list) {
            $existingLanguages = array_filter(explode(',', $this->user->languages_list));
        } else {
            $existingLanguages = $this->user->userLanguages()->pluck('language')->toArray();
        }

        // Early return if nothing changed
        sort($existingLanguages);
        sort($newLanguages);
        if ($existingLanguages === $newLanguages) {
            return;
        }

        // Calculate differences
        $languagesToAdd = array_diff($newLanguages, $existingLanguages);
        $languagesToDelete = array_diff($existingLanguages, $newLanguages);

        // Bulk delete if needed
        if (! empty($languagesToDelete)) {
            $this->user->userLanguages()
                ->whereIn('language', $languagesToDelete)
                ->delete();
        }

        // Bulk insert if needed (more efficient than individual creates)
        if (! empty($languagesToAdd)) {
            $insertData = array_map(fn($language) => [
                'user_id' => $this->user->id,
                'language' => $language,
                'created_at' => now(),
                'updated_at' => now(),
            ], $languagesToAdd);

            UserLanguage::insert($insertData);
        }
    }

    /**
     * Toggle array item for multiselect dropdowns
     *
     * @param string $property The property name (e.g., 'languages')
     * @param mixed $value The value to toggle
     */
    public function toggleArrayItem($property, $value)
    {
        if (! property_exists($this, $property)) {
            return;
        }

        // Initialize as array if not set
        if (! is_array($this->$property)) {
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

    public function removeExistingDocument($index)
    {
        if (isset($this->existing_documents[$index])) {
            // Get the document URL to find the document path
            $documentUrl = $this->existing_documents[$index];

            // Extract the document path from the URL
            // Storage::url() returns something like "/storage/user/1/file.pdf"
            // We need to extract "user/1/file.pdf"
            $storageBaseUrl = Storage::url('');
            $documentPath = str_replace($storageBaseUrl, '', parse_url($documentUrl, PHP_URL_PATH));
            $documentPath = ltrim($documentPath, '/');

            // Find and delete the document from database
            // Try exact match first, then partial match
            $userDocument = $this->user->userDocuments()
                ->where(function ($query) use ($documentPath) {
                    $query->where('document_path', $documentPath)
                        ->orWhere('document_path', 'like', '%' . basename($documentPath));
                })
                ->first();

            if ($userDocument) {
                // Use the actual document path from database
                $actualPath = $userDocument->document_path;

                // Delete the record from database first (needed for UI update)
                $userDocument->delete();

                // Defer file deletion to run after response
                defer(function () use ($actualPath) {
                    // Delete the file from storage
                    if ($actualPath && Storage::disk('public')->exists($actualPath)) {
                        Storage::disk('public')->delete($actualPath);
                    }
                });
            }

            // Reload documents from database to ensure UI updates correctly
            $userDocuments = $this->user->userDocuments()->get();
            $this->existing_documents = [];

            if ($userDocuments->isNotEmpty()) {
                foreach ($userDocuments as $userDoc) {
                    $cleanPath = html_entity_decode($userDoc->document_path, ENT_QUOTES, 'UTF-8');
                    $this->existing_documents[] = Storage::url($cleanPath);
                }
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

        // Count existing documents from user_documents table
        $existingCount = $this->user->userDocuments()->count();

        // Get counts and max limit
        $previousCount = count($this->previous_document_image);
        $newCount = count($value);
        $maxFiles = config('constants.user.file.document.max_files');

        // Total includes existing documents from DB + previous uploads + new uploads
        $totalAfterAdd = $existingCount + $previousCount + $newCount;

        // Validate max files limit - reject if exceeds
        if ($totalAfterAdd > $maxFiles) {
            $this->document_image = $this->previous_document_image; // Revert to previous state
            $currentTotal = $existingCount + $previousCount;
            $remainingSlots = $maxFiles - $currentTotal;
            $message = $remainingSlots > 0
                ? "You can only upload {$remainingSlots} more file(s). Currently you have {$currentTotal} file(s). Please select only {$remainingSlots} file(s)."
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
}

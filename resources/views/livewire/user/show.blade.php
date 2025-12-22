<div>
    <x-show-info-modal modalTitle="{{ __('messages.user.show.label_user') }}" :eventName="$event" :showSaveButton="false"
        :showCancelButton="false">
        <div class="space-y-6">
            @if (!$user)
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <div class="flex items-center">
                        <flux:icon icon="exclamation-triangle" class="w-5 h-5 text-red-600 mr-3" />
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-red-800">User Not Found</h3>
                            <p class="text-sm text-red-600 mt-1">The requested user could not be found.</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Header Section with Profile -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-6">
                        <!-- Profile Avatar -->
                        <div class="flex-shrink-0">
                            @php
                                $profileData = $user->getAttributes()['profile'] ?? null;
                            @endphp

                            @if ($profileData && !empty($profileData))
                                @php
                                    $profileUrl = asset('storage/' . $profileData);
                                @endphp
                                <flux:avatar size="lg" src="{{ $profileUrl }}" alt="Profile Image">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                                </flux:avatar>
                            @else
                                <flux:avatar size="lg" class="bg-gradient-to-br from-blue-500 to-purple-600">
                                    {{ $user->initials() }}
                                </flux:avatar>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <flux:heading size="xl" class="text-gray-900 dark:text-white">{{ $user->name }}
                            </flux:heading>
                            <flux:description class="mt-1">{{ $user->email }}</flux:description>
                            <div class="flex items-center space-x-3 mt-3">
                                <flux:badge color="blue">
                                    {{ $user->role?->name ?? config('constants.user.table.default_values.no_role') }}
                                </flux:badge>
                                <flux:badge
                                    color="{{ \App\Services\CommonService::getStatusFormatted($user->status) === config('constants.user.status.value.active') ? 'green' : 'red' }}">
                                    {{ \App\Services\CommonService::getStatusFormatted($user->status) }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.basic_information.title') }}</flux:heading>
                        <flux:description>Basic user information and contact details</flux:description>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('messages.user.create.basic_information.full_name') }}</flux:label>
                            <flux:description>
                                {{ $user->name ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.basic_information.email_address') }}</flux:label>
                            <flux:description>
                                {{ $user->email ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.basic_information.date_of_birth') }}</flux:label>
                            <flux:description>
                                {{ $user->dob?->format(config('constants.date_formats.table_date')) ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.personal_details.gender') }}</flux:label>
                            <flux:description>{{ \App\Services\CommonService::getGenderFormatted($user->gender) }}
                            </flux:description>
                        </flux:field>
                    </div>
                </div>

                <!-- Location Information Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.location_information.title') }}</flux:heading>
                        <flux:description>Geographic location and timezone information</flux:description>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('messages.user.create.location_information.country') }}</flux:label>
                            <flux:description>
                                {{ $user->country?->name ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.location_information.state') }}</flux:label>
                            <flux:description>
                                {{ $user->state?->name ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.location_information.city') }}</flux:label>
                            <flux:description>
                                {{ $user->city?->name ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.additional_information.timezone') }}</flux:label>
                            <flux:description>
                                {{ config('constants.timezones')[$user->timezone] ?? ($user->timezone ?? config('constants.user.table.default_values.not_set')) }}
                            </flux:description>
                        </flux:field>
                    </div>
                </div>

                <!-- Personal Details Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.personal_details.title') }}</flux:heading>
                        <flux:description>Personal information and preferences</flux:description>
                    </div>

                    <div class="space-y-6">
                        <flux:field>
                            <flux:label>{{ __('messages.user.create.personal_details.description') }}</flux:label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if ($user->description)
                                    <div class="prose prose-sm max-w-none dark:prose-invert">
                                        {{ strip_tags($user->description) }}
                                    </div>
                                @else
                                    <p class="text-gray-500 italic">
                                        {{ config('constants.user.table.default_values.not_set') }}</p>
                                @endif
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.personal_details.comments') }}</flux:label>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if ($user->comments)
                                    <div class="prose prose-sm max-w-none dark:prose-invert">
                                        {{ strip_tags($user->comments) }}
                                    </div>
                                @else
                                    <p class="text-gray-500 italic">
                                        {{ config('constants.user.table.default_values.not_set') }}</p>
                                @endif
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.additional_information.background_color') }}
                            </flux:label>
                            <div class="flex items-center space-x-3">
                                @if ($user->bg_color)
                                    <div class="w-8 h-8 rounded border-2 border-gray-300"
                                        data-bg-color="{{ $user->bg_color }}"
                                        style="background-color: {{ $user->bg_color }};"></div>
                                    <span
                                        class="text-sm font-mono text-gray-900 dark:text-white">{{ $user->bg_color }}</span>
                                @else
                                    <p class="text-sm text-gray-500 italic">
                                        {{ config('constants.user.table.default_values.not_set') }}</p>
                                @endif
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.personal_details.document') }}</flux:label>
                            <div class="space-y-3">
                                @php
                                    // Get documents from documents_list (from user_documents table)
                                    $documents = [];
                                    if (!empty($user->documents_list)) {
                                        // Split comma-separated document paths
                                        $documents = array_filter(explode(',', $user->documents_list));
                                    }
                                @endphp

                                @if (!empty($documents))
                                    <div class="space-y-2">
                                        @foreach ($documents as $index => $document)
                                            @php
                                                // Decode escaped slashes from JSON storage
                                                $decodedPath = str_replace('\/', '/', $document);

                                                // Clean the document path (remove any extra slashes and normalize)
                                                $cleanPath = trim($decodedPath, '/');
                                                $cleanPath = preg_replace('/\/+/', '/', $cleanPath); // Replace multiple slashes with single slash

                                                // Generate the correct URL for storage/app/public files
                                                $documentUrl = asset('storage/' . $cleanPath);

                                                // Check if file exists in storage
                                                $fileExists = \Storage::disk('public')->exists($cleanPath);

                                                // Extract filename from the path
                                                $fileName = basename($cleanPath);

                                                // Get file extension
                                                $extension = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
                                            @endphp

                                            <div
                                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                    <!-- Document Icon -->
                                                    <div class="flex-shrink-0">
                                                        @if (in_array($extension, ['pdf']))
                                                            <div
                                                                class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-red-600 dark:text-red-400"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                        @elseif(in_array($extension, ['doc', 'docx']))
                                                            <div
                                                                class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                        @else
                                                            <div
                                                                class="w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                    </path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Document Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                            title="{{ $fileName }}">
                                                            {{ $fileName }}
                                                            @if (!$fileExists)
                                                                <span class="text-red-500 text-xs ml-1">(File not
                                                                    found)</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                                            {{ $extension }} Document
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Document Actions -->
                                                <div class="flex items-center space-x-2 flex-shrink-0">
                                                    <a href="{{ $documentUrl }}" target="_blank"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 rounded-md transition-colors"
                                                        title="View {{ $fileName }}">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                        View
                                                    </a>
                                                    <a href="{{ $documentUrl }}" download="{{ $fileName }}"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/30 rounded-md transition-colors"
                                                        title="Download {{ $fileName }}">
                                                        <svg class="w-3 h-3 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">
                                        {{ config('constants.user.table.default_values.no_document') }}</p>
                                @endif
                            </div>
                        </flux:field>
                    </div>
                </div>

                <!-- Languages Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.personal_details.languages') }}</flux:heading>
                        <flux:description>{{ __('messages.user.create.personal_details.languages_description') }}
                        </flux:description>
                    </div>
                    <div class="space-y-4">
                        @php
                            $languages = !empty($user->languages_list) ? explode(',', $user->languages_list) : [];
                            $languageLabels = config('constants.user.languages', []);
                        @endphp

                        @if (count($languages) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($languages as $languageId)
                                    @if (isset($languageLabels[$languageId]))
                                        <flux:badge color="blue">{{ $languageLabels[$languageId] }}</flux:badge>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">
                                {{ config('constants.user.table.default_values.no_languages') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.additional_information.title') }}</flux:heading>
                        <flux:description>Hobbies, skills, and other additional information</flux:description>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label>{{ __('messages.user.create.additional_information.hobbies') }}</flux:label>
                            <flux:description>
                                {{ \App\Services\CommonService::getHobbiesFormattedFromList($user->hobbies_list) }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.additional_information.skills') }}</flux:label>
                            <flux:description>{{ \App\Services\CommonService::getSkillsFormatted($user->skills) }}
                            </flux:description>
                        </flux:field>
                    </div>
                </div>

                <!-- Event Information Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.event_information.title') }}</flux:heading>
                        <flux:description>Event-related dates and times</flux:description>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:field>
                            <flux:label>{{ __('messages.user.create.event_information.event_date') }}</flux:label>
                            <flux:description>
                                {{ $user->event_date?->format(config('constants.date_formats.table_date')) ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.event_information.event_time') }}</flux:label>
                            <flux:description>
                                {{ \App\Services\CommonService::getEventTimeFormatted($user->event_time) }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.create.event_information.event_datetime') }}</flux:label>
                            <flux:description>
                                {{ $user->event_datetime?->format(config('constants.date_formats.table_datetime')) ?? config('constants.user.table.default_values.not_set') }}
                            </flux:description>
                        </flux:field>
                    </div>
                </div>

                <!-- Consent Information Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">
                            {{ __('messages.user.create.terms_and_conditions.title') }}</flux:heading>
                        <flux:description>User consent and agreement information</flux:description>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $consentData = $user->consent_data ?? [];
                        @endphp

                        <!-- Terms and Conditions -->
                        <flux:field>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if ($consentData['terms_accepted'] ?? false)
                                        <flux:badge color="green" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="check-circle" class="w-4 h-4" />
                                            <span>Accepted</span>
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="x-circle" class="w-4 h-4" />
                                            <span>Not Accepted</span>
                                        </flux:badge>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <flux:label>
                                        {{ __('messages.user.create.terms_and_conditions.terms_and_conditions') }}
                                    </flux:label>
                                </div>
                            </div>
                        </flux:field>

                        <!-- Privacy Policy -->
                        <flux:field>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if ($consentData['privacy_policy_accepted'] ?? false)
                                        <flux:badge color="green" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="check-circle" class="w-4 h-4" />
                                            <span>Accepted</span>
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="x-circle" class="w-4 h-4" />
                                            <span>Not Accepted</span>
                                        </flux:badge>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <flux:label>{{ __('messages.user.create.terms_and_conditions.privacy_policy') }}
                                    </flux:label>
                                </div>
                            </div>
                        </flux:field>

                        <!-- Data Processing Consent -->
                        <flux:field>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if ($consentData['data_processing_consent'] ?? false)
                                        <flux:badge color="green" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="check-circle" class="w-4 h-4" />
                                            <span>Consented</span>
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="x-circle" class="w-4 h-4" />
                                            <span>Not Consented</span>
                                        </flux:badge>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <flux:label>{{ __('messages.user.create.terms_and_conditions.data_processing') }}
                                    </flux:label>
                                </div>
                            </div>
                        </flux:field>

                        <!-- Marketing Consent -->
                        <flux:field>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if ($consentData['marketing_consent'] ?? false)
                                        <flux:badge color="green" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="check-circle" class="w-4 h-4" />
                                            <span>Agreed</span>
                                        </flux:badge>
                                    @else
                                        <flux:badge variant="outline" size="sm" class="flex items-center gap-1">
                                            <flux:icon name="x-circle" class="w-4 h-4 text-gray-500" />
                                            <span>Not Agreed</span>
                                        </flux:badge>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <flux:label>
                                        {{ __('messages.user.create.terms_and_conditions.marketing_communications') }}
                                    </flux:label>
                                </div>
                            </div>
                        </flux:field>
                    </div>
                </div>

                <!-- System Information Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="mb-6">
                        <flux:heading size="lg" class="text-gray-900 dark:text-white">System Information
                        </flux:heading>
                        <flux:description>Technical details and timestamps</flux:description>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <flux:field>
                            <flux:label>User ID</flux:label>
                            <flux:description class="font-mono">{{ $user->id }}</flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.table.columns.created_at') }}</flux:label>
                            <flux:description>
                                {{ $user->created_at->format(config('constants.date_formats.table_datetime')) }}
                            </flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('messages.user.table.columns.updated_at') }}</flux:label>
                            <flux:description>
                                {{ $user->updated_at->format(config('constants.date_formats.table_datetime')) }}
                            </flux:description>
                        </flux:field>
                    </div>
                </div>
            @endif
        </div>
    </x-show-info-modal>
</div>

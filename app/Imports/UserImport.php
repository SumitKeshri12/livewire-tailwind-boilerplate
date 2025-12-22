<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserHobby;
use App\Traits\CommonTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class UserImport
{
    use CommonTrait;

    private $errors = [];

    private $rows = 0;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getErrors()
    {
        return $this->errors; // return all errors
    }

    public function rules(): array
    {
        return [
            '0' => 'required|max:100', // name
            '1' => 'required|max:200|email|unique:users,email,NULL,id,deleted_at,NULL', // email
            '2' => 'required|min:6|max:191', // password
            '3' => 'required|exists:roles,id,deleted_at,NULL', // role_id
            '4' => 'required|date_format:' . config('constants.validation_date_format'), // dob
            '5' => 'required|exists:countries,id,deleted_at,NULL', // country_id
            '6' => 'required|exists:states,id,deleted_at,NULL', // state_id
            '7' => 'required|exists:cities,id,deleted_at,NULL', // city_id
            '8' => 'required|in:F,M', // gender
            '9' => 'required|in:Y,N', // status
            '10' => 'required|string', // description
            '11' => 'nullable|string', // hobbies (will be converted to array)
            '12' => 'nullable|string', // skills (will be converted to array)
            '13' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/', // bg_color
            '14' => 'required|string|max:100', // timezone
            '15' => 'nullable|date', // event_date
            '16' => 'nullable|date_format:Y-m-d\TH:i', // event_datetime
            '17' => 'nullable|date_format:' . config('constants.validation_time_format'), // event_time
            '18' => 'nullable|integer|min:0|max:200', // age
            '19' => 'required|accepted', // terms_accepted
            '20' => 'required|accepted', // privacy_policy_accepted
            '21' => 'required|accepted', // data_processing_consent
            '22' => 'nullable', // marketing_consent
        ];
    }

    public function validationMessages()
    {
        return [
            '0.required' => 'The name field is required.',
            '0.max' => 'The name field must not be greater than 100 characters.',
            '1.required' => 'The email field is required.',
            '1.max' => 'The email field must not be greater than 200 characters.',
            '1.email' => 'The email field must be a valid email address.',
            '1.unique' => 'The email has already been taken.',
            '2.required' => 'The password field is required.',
            '2.min' => 'The password field must be at least 6 characters.',
            '2.max' => 'The password field must not be greater than 191 characters.',
            '3.required' => 'The role field is required.',
            '3.exists' => 'The selected role is invalid.',
            '4.required' => 'The date of birth field is required.',
            '4.date_format' => 'The date of birth field must be in the format ' . config('constants.validation_date_format') . '.',
            '5.required' => 'The country field is required.',
            '5.exists' => 'The selected country is invalid.',
            '6.required' => 'The state field is required.',
            '6.exists' => 'The selected state is invalid.',
            '7.required' => 'The city field is required.',
            '7.exists' => 'The selected city is invalid.',
            '8.required' => 'The gender field is required.',
            '8.in' => 'The selected gender is invalid.',
            '9.required' => 'The status field is required.',
            '9.in' => 'The selected status is invalid.',
            '10.required' => 'The description field is required.',
            '10.string' => 'The description field must be a string.',
            '11.string' => 'The hobbies field must be a string (comma-separated values).',
            '12.string' => 'The skills field must be a string (comma-separated values).',
            '13.regex' => 'The background color field must be a valid hex color (e.g., #FF5733).',
            '14.required' => 'The timezone field is required.',
            '14.string' => 'The timezone field must be a string.',
            '14.max' => 'The timezone field must not be greater than 100 characters.',
            '15.date' => 'The event date field must be a valid date.',
            '16.date_format' => 'The event datetime field must be in the format Y-m-d\TH:i.',
            '17.date_format' => 'The event time field must be in the format ' . config('constants.validation_time_format') . '.',
            '18.integer' => 'The age field must be an integer.',
            '18.min' => 'The age field must be at least 0.',
            '18.max' => 'The age field must not be greater than 200.',
            '19.required' => 'You must accept the terms and conditions.',
            '19.accepted' => 'You must accept the terms and conditions.',
            '20.required' => 'You must accept the privacy policy.',
            '20.accepted' => 'You must accept the privacy policy.',
            '21.required' => 'You must consent to data processing.',
            '21.accepted' => 'You must consent to data processing.',
        ];
    }

    public function validateBulk($collection)
    {
        $i = 1;
        foreach ($collection as $col) {
            $i++;
            $errors[$i] = ['row' => $i];

            $colArray = $col->toArray();

            // Custom validation for hobbies (index 11)
            if (isset($colArray[11]) && ! empty($colArray[11])) {
                $hobbies = explode(',', $colArray[11]);
                $hobbies = array_map('trim', $hobbies);
                $validHobbies = config('constants.user.hobbies');
                $invalidHobbies = [];
                foreach ($hobbies as $hobby) {
                    if (! empty($hobby) && ! array_key_exists(strtolower($hobby), $validHobbies)) {
                        $invalidHobbies[] = $hobby;
                    }
                }
                if (! empty($invalidHobbies)) {
                    $errors[$i]['error'][] = 'Invalid hobby: ' . implode(', ', $invalidHobbies);
                }
            }

            // Custom validation for timezone (index 14)
            if (isset($colArray[14]) && ! empty($colArray[14])) {
                $timezones = config('constants.timezones');
                if (! array_key_exists($colArray[14], $timezones)) {
                    $errors[$i]['error'][] = 'Invalid timezone: "' . $colArray[14] . '"';
                }
            }

            $validator = Validator::make($colArray, $this->rules(), $this->validationMessages());
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $error) {
                        $errors[$i]['error'][] = $error;
                    }
                }
            }

            // Only add error if there are actual errors
            if (! empty($errors[$i]['error'])) {
                $this->errors[] = (object) $errors[$i];
            }
        }

        return $this->getErrors();
    }

    public function collection(Collection $collection)
    {
        $error = $this->validateBulk($collection);

        if ($error) {
            return;
        } else {
            foreach ($collection as $col) {
                // Convert hobbies and skills from string to array
                $hobbies = $col[11] ?? [];
                if (is_string($hobbies)) {
                    $hobbies = explode(',', $hobbies);
                }
                $hobbies = array_map('trim', $hobbies);
                $hobbies = array_filter($hobbies);

                $skills = $col[12] ?? [];
                if (is_string($skills)) {
                    $skills = explode(',', $skills);
                }
                $skills = array_map('trim', $skills);
                $skills = array_filter($skills);

                $user = User::create([
                    'name' => $col[0],
                    'email' => $col[1],
                    'password' => bcrypt($col[2]),
                    'role_id' => $col[3],
                    'dob' => $col[4],
                    'profile' => null, // Profile image not handled in CSV import
                    'country_id' => $col[5],
                    'state_id' => $col[6],
                    'city_id' => $col[7],
                    'gender' => $col[8],
                    'status' => $col[9],
                    'description' => $col[10],
                    'skills' => json_encode($skills),
                    'bg_color' => $col[13],
                    'timezone' => $col[14],
                    'event_date' => $col[15],
                    'event_datetime' => $col[16],
                    'event_time' => $col[17],
                    'age' => $col[18],
                    'document' => null, // Document images not handled in CSV import
                    'consent_data' => [
                        'terms_accepted' => $col[19] ?? true,
                        'privacy_policy_accepted' => $col[20] ?? true,
                        'data_processing_consent' => $col[21] ?? true,
                        'marketing_consent' => $col[22] ?? false,
                    ],
                ]);

                // Save hobbies to user_hobbies table using bulk insert
                if (! empty($hobbies)) {
                    $insertData = array_map(fn($hobby) => [
                        'user_id' => $user->id,
                        'hobby' => mb_strtolower($hobby),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $hobbies);

                    UserHobby::insert($insertData);
                }

                $this->rows++;
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * Get expected CSV headers for this import
     */
    public function getExpectedHeaders(): array
    {
        return [
            'name',
            'email',
            'password',
            'role_id',
            'dob',
            'country_id',
            'state_id',
            'city_id',
            'gender',
            'status',
            'description',
            'hobbies',
            'skills',
            'bg_color',
            'timezone',
            'event_date',
            'event_datetime',
            'event_time',
            'age',
            'terms_accepted',
            'privacy_policy_accepted',
            'data_processing_consent',
            'marketing_consent',
        ];
    }
}

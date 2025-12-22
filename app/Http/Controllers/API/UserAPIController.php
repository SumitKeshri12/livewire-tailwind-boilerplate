<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use App\Models\User;
use App\Traits\UploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserAPIController extends Controller
{
    use UploadTrait;

    /**
     * @return JsonResponse
     */
    public function uploadFile(Request $request)
    {
        try {
            if ($request->get('modelName') == config('constants.import_csv_log.models.cash_token_point')) {
                $validation = 'required|max:10240'; // 10 MB
            } else {
                $validation = 'required|mimes:csv,txt|max:10240'; // 10 MB
            }

            $request->validate([
                'file' => $validation,
            ]);

            if ($request->hasFile('file')) {
                // SECURITY FIX: Hardcode folder to prevent traversal & use Auth ID to prevent IDOR
                $userId = Auth::user()->id;
                if (!$userId) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                $folderName = 'uploads/users/' . $userId;

                $uploadFilePath = User::uploadOne($request->file('file'), $folderName, 'public');

                if ($uploadFilePath != false) {
                    /* Insert data into the import_logs table */
                    ImportLog::create(
                        [
                            'file_name' => basename($uploadFilePath),
                            'file_path' => $uploadFilePath,
                            'model_name' => $request->get('modelName'),
                            'user_id' => $userId, // Use authenticated user ID
                            'status' => config('constants.import_csv_log.status.key.pending'),
                            'import_flag' => config('constants.import_csv_log.import_flag.key.pending'),
                        ]
                    );

                    return response()->json(['success' => __('messages.import_history.messages.success')]);
                } else {
                    return response()->json(['error' => __('messages.something_went_wrong')], config('constants.validation_codes.unprocessable_entity'));
                }
            } else {
                return response()->json(['error' => __('messages.import_history.messages.validate_error')], config('constants.validation_codes.unprocessable_entity'));
            }
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);

            return response()->json(['error' => $th->getMessage()], config('constants.validation_codes.unassigned'));
        }
    }
}

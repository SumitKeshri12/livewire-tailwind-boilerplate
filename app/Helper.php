<?php

namespace App;

use App\Models\Country;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Helper
{
    /**
     * @return string
     */
    public static function formatDate($dateTime, $format = null)
    {
        $format = is_null($format) ? config('constants.date_formats.default') : $format;

        return Carbon::parse($dateTime)->format($format);
    }

    public static function getIp()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return request()->ip(); // it will return server ip when no client ip found
    }

    /**
     * Log validation errors.
     * Delegates to LoggerService.
     */
    public static function logValidationError(string $controller_name, string $function_name, Validator $validator, $user = null, string $channel = 'validation'): void
    {
        (new \App\Services\LoggerService())->logValidationError($controller_name, $function_name, $validator, $user, $channel);
    }

    /**
     * Log exceptions or errors with stack trace and additional details.
     * Delegates to LoggerService.
     */
    public static function logCatchError(Throwable $th, string $controller_name, string $function_name, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        (new \App\Services\LoggerService())->logCatchError($th, $controller_name, $function_name, $extra_param, $user, $channel);
    }

    public static function logSingleError($controller_name, $function_name, $message, $extra_param = [], $user = null, $channel = null): void
    {
        // Deprecated or mapped to logError? Keeping equivalent logic via new service if possible or just wrapping.
        // The original logSingleError was very similar to logError but without some request data in the generic Log::error catch
        // For simplicity and consistency, let's map it to logError but clearly it was intended as a lighter version.
        // However, to keep strict back-compat, we will use logError from service.
        (new \App\Services\LoggerService())->logError($controller_name, $function_name, $message, $extra_param, $user, $channel);
    }

    /**
     * Log general error messages with additional context.
     * Delegates to LoggerService.
     */
    public static function logError(string $controller_name, string $function_name, string $message, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        (new \App\Services\LoggerService())->logError($controller_name, $function_name, $message, $extra_param, $user, $channel);
    }

    /**
     * Log a single informational message with optional parameters.
     */
    public static function logSingleInfo(string $controller_name, string $function_name, string $message, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        (new \App\Services\LoggerService())->logInfo($controller_name, $function_name, $message, $extra_param, $user, $channel);
    }

    /**
     * Log general informational messages with additional context.
     */
    public static function logInfo(string $controller_name, string $function_name, string $message, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        (new \App\Services\LoggerService())->logInfo($controller_name, $function_name, $message, $extra_param, $user, $channel);
    }

    public static function getAllLegends()
    {
        try {
            $userModel = new User();
            $legendsArray['Users'] = $userModel->legend;

            return $legendsArray;
        } catch (Throwable $th) {
            self::logCatchError($th, static::class, __FUNCTION__);
        }
    }

    /**
     * Get all roles
     */
    public static function getAllRole()
    {
        return Cache::rememberForever('getAllRole', function () {
            return Role::select('id', 'name')->get()->toArray();
        });
    }

    /**
     * Get all countries
     */
    public static function getAllCountry()
    {
        return Country::where('deleted_at', null)->get();
    }

    /**
     * Get all states
     */
    public static function getAllState()
    {
        return State::where('deleted_at', null)->get();
    }

    public static function getAllRoles()
    {
        return Role::pluck('name', 'id')->toArray();
    }

    public static function getAllPermissions()
    {
        return Cache::rememberForever('getAllPermissions', function () {
            return Permission::select('id', 'name', 'guard_name', 'label')->get()->toArray();
        });
    }

    public static function getCachedPermissionsByRole($roleId)
    {
        return Cache::rememberForever("getCachedPermissionsByRole:$roleId", function () use ($roleId) {
            return PermissionRole::leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
                ->where('permission_role.role_id', $roleId)->pluck('permissions.name')->toArray();
        });
    }

    /**
     * @param $importStatus
     * @return string
     */
    public static function getImportStatusText($status)
    {
        if ($status == config('constants.import_csv_log.status.key.success')) {
            $statusText = '<flux:badge color="green">' . config('constants.import_csv_log.status.value.success') . '</flux:badge>';
        } elseif ($status == config('constants.import_csv_log.status.key.fail')) {
            $statusText = '<flux:badge color="red">' . config('constants.import_csv_log.status.value.fail') . '</flux:badge>';
        } elseif ($status == config('constants.import_csv_log.status.key.pending')) {
            $statusText = '<flux:badge color="blue">' . config('constants.import_csv_log.status.value.pending') . '</flux:badge>';
        } elseif ($status == config('constants.import_csv_log.status.key.processing')) {
            $statusText = '<flux:badge color="yellow">' . config('constants.import_csv_log.status.value.processing') . '</flux:badge>';
        } elseif ($status == config('constants.import_csv_log.status.key.convert_decrypted')) {
            $statusText = '<flux:badge color="yellow">' . config('constants.import_csv_log.status.value.convert_decrypted') . '</flux:badge>';
        } else {
            $statusText = '-';
        }

        return $statusText;
    }

    public static function processProgressOfExport($functionParams)
    {
        try {
            // Decode function parameters
            $params = json_decode($functionParams, true);

            // Check if batch ID is provided
            if (! $params['batchId']) {
                logger()->error('app/Helper.php: downloadExportFile: batchId not found', ['functionParams' => $functionParams, 'params' => $params]);

                return ['status' => 0, 'message' => __('messages.common_error_message')];
            }

            // Find batch and check for failed jobs
            $batch = Bus::findBatch($params['batchId']);
            if ($batch->failedJobs) {
                logger()->error('app/Helper.php: downloadExportFile: failedJobs', ['functionParams' => $functionParams, 'params' => $params, 'failedJobs' => $batch->failedJobs]);

                return ['status' => 0, 'message' => __('messages.common_error_message')];
            }

            // Check if file is downloadable and batch is finished
            if (isset($params['isFileDownloadable']) && $params['isFileDownloadable'] && $batch->finished()) {
                $downloadableResponse = Helper::downloadExportFile($functionParams);

                return ['status' => 2, 'message' => 'Exporting Successfully.', 'data' => $downloadableResponse];

                /*$downloadableResponse = Helper::mergeExportFile($functionParams);
            if ($downloadableResponse['status']) {

            return ['status' => 2, 'message' => 'Exporting Successfully.', 'data' => $downloadableResponse['data']];
            } else {

            return ['status' => 0, 'message' => $downloadableResponse['message']];
            }*/
            }

            // Get export progress and update parameters
            $exportProgress = $batch->progress();
            if (isset($params['exportProgress']) && ($params['exportProgress'] != $exportProgress)) {
                // Messages are only change when percentage will change
                $params['waitingMessage'] = Helper::getRandomExportWaitingMessage();
            }

            $params['exportProgress'] = $exportProgress;
            // We are displaying 100% first, after we will process download file. For that we have added isFileDownloadable condition.
            $params['isFileDownloadable'] = $exportProgress == 100 ? 1 : 0;

            return ['status' => 1, 'data' => json_encode($params)];
        } catch (Throwable $e) {
            // Log any exceptions during export progress processing
            logger()->error('app/Helper.php: downloadExportFile: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString(), 'functionParams' => $functionParams]);

            return ['status' => 0, 'message' => __('messages.common_error_message')];
        }
    }

    public static function getRandomExportWaitingMessage()
    {
        try {
            // Get random export waiting message
            $exportWaitingMessageArray = __('messages.export.export_waiting_message');

            return $exportWaitingMessageArray[array_rand($exportWaitingMessageArray)];
        } catch (Throwable $e) {
            // Log any exceptions during random message fetching
            logger()->error('app/Helper.php: downloadExportFile: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);

            return 'Your export is in progress. Thank you for your patience!';
        }
    }

    public static function downloadExportFile($functionParams)
    {
        try {
            // Decode function parameters
            $params = json_decode($functionParams, true);

            // Get parameters for download file
            $headingColumn = $params['headingColumn'];
            $newFileArray = $params['newFileArray'];
            $folder = $params['folder'];
            $downloadFileName = $params['downloadPrefixFileName'] . date('dmY') . '.' . config('constants.export_csv_file_type');

            // Stream download of export file
            return response()->streamDownload(function () use ($headingColumn, $newFileArray, $folder) {
                echo $headingColumn . "\n";
                foreach ($newFileArray as $file) {
                    echo Storage::get($file);
                }
                // We removed directory from storage after downloaded
                Storage::deleteDirectory($folder);
            }, $downloadFileName);
        } catch (Throwable $e) {
            // Log any exceptions during export file download
            logger()->error('app/Helper.php: downloadExportFile: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);
        }
    }

    public static function runExportJob($totalRecord, $filters, $checkboxValues, $search, $headingColumn, $downloadPrefixFileName, $exportClass, $batchName, $extraParam = []): array
    {
        try {
            // Check if there are any records to export
            if (! $totalRecord) {
                return ['status' => false, 'message' => 'We can\'t find any record.'];
            }

            // Calculate batch parameters
            $newFileArray = $jobArray = [];

            // We are chunked as 25000 per process & it's configurable via .env.
            $itemCountBatching = config('constants.export_pagination');
            $count = ($checkboxValues) ? count($checkboxValues) : $totalRecord;
            $jobCount = ceil($count / $itemCountBatching);

            // Export file are stored in "custom_exports" folder in storage.
            $folder = config('constants.export_file_path') . uniqid() . '/';
            $file = $folder . time();

            // Generate new file names and create export job instances
            for ($index = 1; $index <= $jobCount; $index++) {
                // Exporting file types are CSV
                $newFileArray[] = $new_file = $file . '_' . $index . '.' . config('constants.export_txt_file_type');
                $jobArray[] = new $exportClass($index, $itemCountBatching, $new_file, $filters, $checkboxValues, $search, $extraParam);
            }

            // Check if job array is empty
            if (! $jobArray) {
                return ['status' => false, 'message' => __('messages.common_error_message')];
            }

            // Dispatch batch job and return status and data
            $batchId = Bus::batch($jobArray)->name($batchName)->dispatch()->id;

            return ['status' => true, 'data' => [
                'batchId' => $batchId,
                'folder' => $folder,
                'newFileArray' => $newFileArray,
                'isFileDownloadable' => 0,
                'exportProgress' => 0,
                'waitingMessage' => Helper::getRandomExportWaitingMessage(),
                'downloadPrefixFileName' => $downloadPrefixFileName,
                'headingColumn' => $headingColumn,
            ]];
        } catch (Throwable $e) {
            // Log any exceptions during export job
            logger()->error('app/Helper.php: runExportJob: Throwable', [
                'Message' => $e->getMessage(),
                'TraceAsString' => $e->getTraceAsString(),
                'totalRecord' => $totalRecord,
                'filters' => $filters,
                'checkboxValues' => $checkboxValues,
                'search' => $search,
                'headingColumn' => $headingColumn,
                'downloadPrefixFileName' => $downloadPrefixFileName,
                'exportClass' => $exportClass,
                'batchName' => $batchName,
                'extraParam' => $extraParam,
            ]);

            return ['status' => false, 'message' => __('messages.common_error_message')];
        }
    }

    public static function putExportData($className, $final_data, $file, $sr_no): bool
    {
        try {
            // Check if final data is empty
            if (empty($final_data)) {
                logger()->error('app/Helper.php: appendExportData: There is no data to append', ['className' => $className, 'final_data' => $final_data, 'file' => $file, 'sr_no' => $sr_no]);

                return false;
            }

            // Generate CSV content from final data and store in storage
            $final_string = null;
            foreach ($final_data as $i => $data) {
                // Convert array values to string
                $row = array_map(function ($value) {
                    if (is_array($value)) {
                        return is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
                    }

                    return $value;
                }, $data);

                // Escape double quotes and wrap in quotes
                $row = array_map(function ($value) {
                    return '"' . str_replace('"', '""', (string) $value) . '"';
                }, $row);

                $final_string .= implode(',', $row) . "\n";
            }

            // Store CSV content in storage
            if ($final_string) {
                // Storage::put($file, trim($final_string, "\n"));
                Storage::put($file, $final_string);

                return true;
            } else {
                return false;
            }
        } catch (Throwable $e) {
            // Log any exceptions during data export
            logger()->error('app/Helper.php: appendExportData: Throwable', [
                'Message' => $e->getMessage(),
                'TraceAsString' => $e->getTraceAsString(),
                'className' => $className,
                'final_data' => $final_data,
                'file' => $file,
                'sr_no' => $sr_no,
            ]);

            return false;
        }
    }
}

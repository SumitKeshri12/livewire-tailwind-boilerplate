<?php

namespace App\Traits;

use App\Models\ImportLog;
use Illuminate\Support\Facades\Storage;

trait ImportTrait
{
    use Mailable;

    /**
     * Common Import Method - Process CSV files with header validation first, then data validation
     */
    public static function commonImport($model, $path, $modelType, $filename, $redirectLink, $subject, $importLog = null)
    {
        try {
            // Read and process the CSV file
            $csvData = self::readCsvFile($path);

            if (empty($csvData)) {
                throw new \Exception(__('messages.import.csv_empty_or_invalid'));
            }

            // Get header row
            $header = array_shift($csvData);

            // Validate headers first
            $headerValidation = self::validateHeaders($header, $model);
            if (! $headerValidation['valid']) {
                throw new \Exception(__('messages.import.header_invalid', ['expected' => $headerValidation['errors'][0] ?? '']));
            }

            // Convert CSV data to Collection for validation
            $collection = collect($csvData)->map(function ($row) {
                return collect($row);
            });

            // Process the data using the model
            $model->collection($collection);

            // Check for validation errors
            $errors = $model->getErrors();
            $rowCount = $model->getRowCount();

            if (! empty($errors)) {
                // Has validation errors - mark as failed
                $newPath = str_replace('new', 'fail', $path);
                $newPath = str_replace('.csv', '_' . config('constants.calender.import_format') . '_fail.csv', $newPath);
                Storage::disk('public')->move($path, $newPath);

                self::commonForImportLogAndEmail($filename, $newPath, $modelType, config('constants.import_csv_log.status.key.fail'), $rowCount, json_encode($errors), $importLog);

                self::sendImportFail($model, ['{{model_type}}' => $modelType, '{{file_name}}' => $filename, '{{subject}}' => $subject], $redirectLink);
            } else {
                // No errors - mark as success
                $newPath = str_replace('new', 'success', $path);
                $newPath = str_replace('.csv', '_' . config('constants.calender.import_format') . '_success.csv', $newPath);
                Storage::disk('public')->move($path, $newPath);

                self::commonForImportLogAndEmail($filename, $newPath, $modelType, config('constants.import_csv_log.status.key.success'), $rowCount, null, $importLog);

                self::sendImportSuccess($model, ['{{row_count}}' => $rowCount, '{{model_type}}' => $modelType, '{{file_name}}' => $filename, '{{subject}}' => $subject], $redirectLink);
            }
        } catch (\Exception $e) {
            // Handle any exceptions during processing
            $newPath = str_replace('new', 'fail', $path);
            $newPath = str_replace('.csv', '_' . config('constants.calender.import_format') . '_fail.csv', $newPath);
            Storage::disk('public')->move($path, $newPath);

            self::commonForImportLogAndEmail($filename, $newPath, $modelType, config('constants.import_csv_log.status.key.fail'), 0, json_encode(['error' => $e->getMessage()]), $importLog);
        }
    }

    /**
     * Validate CSV headers against expected format
     */
    private static function validateHeaders($header, $model)
    {
        $expectedHeaders = self::getExpectedHeaders($model);

        // Simple check - if headers don't match, return the expected sequence
        if (count($header) !== count($expectedHeaders)) {
            return [
                'valid' => false,
                'errors' => ['Header sequence is incorrect. Expected headers: ' . implode(', ', $expectedHeaders)],
            ];
        }

        // Check if headers match (case insensitive)
        foreach ($expectedHeaders as $index => $expectedHeader) {
            if (! isset($header[$index]) || strtolower(trim($header[$index])) !== strtolower($expectedHeader)) {
                return [
                    'valid' => false,
                    'errors' => ['Header sequence is incorrect. Expected headers: ' . implode(', ', $expectedHeaders)],
                ];
            }
        }

        return [
            'valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Get expected headers from the model
     */
    private static function getExpectedHeaders($model)
    {
        // Check if the model has getExpectedHeaders method
        if (method_exists($model, 'getExpectedHeaders')) {
            return $model->getExpectedHeaders();
        }

        // Fallback for models without this method
        return [];
    }

    /**
     * Read CSV file and return data as array with memory optimization
     */
    private static function readCsvFile($path)
    {
        $fullPath = Storage::disk('public')->path($path);

        if (! file_exists($fullPath)) {
            throw new \Exception(__('messages.import.csv_not_found', ['path' => $path]));
        }

        $data = [];
        $handle = fopen($fullPath, 'r');
        $rowCount = 0;
        $maxRows = 10000; // Limit to prevent memory issues

        if ($handle !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false && $rowCount < $maxRows) {
                $data[] = $row;
                $rowCount++;

                // Clear memory every 1000 rows
                if ($rowCount % 1000 === 0) {
                    gc_collect_cycles();
                }
            }
            fclose($handle);

            // If we hit the limit, throw an error
            if ($rowCount >= $maxRows) {
                throw new \Exception(__('messages.import.file_too_large', ['max' => $maxRows]));
            }
        }

        return $data;
    }

    /**
     * Common method for import log stored and send email of success or failure
     */
    public static function commonForImportLogAndEmail($filename, $path, $modelType, $status, $no_of_rows, $error_log, $importLog)
    {
        if (! is_null($importLog)) {
            $importLog->import_flag = config('constants.import_csv_log.import_flag.key.success');
            $importLog->status = $status; // Update status to Success in import_csv_logs table
            $importLog->no_of_rows = $no_of_rows; // Update insert number of rows in import_csv_logs table
            $importLog->error_log = $error_log;
            $importLog->update();
        } else {
            ImportLog::create([
                'file_name' => $filename,
                'file_path' => $path,
                'model_name' => $modelType,
                'status' => $status,
                'import_flag' => config('constants.import_csv_log.import_flag.key.success'),
                'no_of_rows' => $no_of_rows,
                'error_log' => $error_log,
            ]);
        }
    }
}

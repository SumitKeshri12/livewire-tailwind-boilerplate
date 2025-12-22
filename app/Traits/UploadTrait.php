<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadTrait
{
    public static function uploadOne(UploadedFile $uploadedFile, $folder, $disk = 'local')
    {
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'zip'];

        if (!in_array($ext, $allowed)) {
            throw new \Exception('Invalid file extension.');
        }

        // Use a secure random string for filename
        $file_name = Str::random(40) . '.' . $ext;

        return $uploadedFile->storeAs($folder, $file_name, $disk);
    }

    public static function setTinify($tinifyKey = false)
    {
        \Tinify\Tinify::setKey(config('services.tinify.key'));
    }

    public function deleteOne($path)
    { // $path = path with image name
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    /**
     * Upload In Local
     */
    public static function uploadInLocal(UploadedFile $uploadedFile, $folder, $disk = 'local')
    {
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'zip', 'bmp', 'svg']; // Added bmp/svg as this method supports them based on usage

        if (!in_array($ext, $allowed)) {
            throw new \Exception('Invalid file extension.');
        }

        return $uploadedFile->store($folder, $disk);
    }

    public static function uploadSvgInLocal($image, $folder, $disk = 'local')
    {
        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        $imageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $ext;
        $imagePath = $folder . '/' . $imageName;
        $image_dt = Storage::get($image->getRealPath());
        $image_dt = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $image_dt);
        $image_dt = str_replace('<?php', '<php_tag>', $image_dt);
        $image_dt = str_replace(' ?>', '</php_tag>', $image_dt);
        $image_dt = preg_replace('#<php_tag(.*?)>(.*?)</php_tag>#is', '', $image_dt);
        Storage::disk($disk)->put($imagePath, $image_dt);

        return $imagePath;
    }

    public static function resizeImages($image, $realPath, $fromApp = false, $isScanImage = false, $disk = 'local')
    {
        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        self::setTinify();

        if ($fromApp) {
            $filePath = $image->getRealPath();
        } else {
            $filePath = $image->temporaryUrl();
        }
        $realPath = $realPath;
        if ($ext == 'svg') {
            $destinationPath = self::uploadSvgInLocal($image, $realPath, $disk);
        } elseif ($ext == 'bmp') {
            $destinationPath = self::uploadInLocal($image, $realPath, $disk);
        } else {
            $filename = urldecode(pathinfo($filePath, PATHINFO_FILENAME));
            $destinationPath = $realPath . $filename . '.' . $ext;
            if ($isScanImage) {
                $destinationPath = $realPath . date('ymdhis') . '_' . Str::random(6) . '.' . $ext;
            }
            $sourceData = file_get_contents($filePath);
            $resultData = \Tinify\fromBuffer($sourceData)->toBuffer();
            Storage::disk($disk)->put($destinationPath, $resultData);
        }

        $image_array['image'] = $destinationPath;

        return $image_array;
    }

    public static function is_file_exists($path)
    {
        if (! is_null($path) && Storage::exists($path)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Download Image Path
     */
    public static function getImagePath($path)
    {
        return config('app.url') . '/api/v1/download-image?file=' . Crypt::encryptString($path);
    }

    /**
     * Get File Path
     */
    public static function getFilePathByStorage($filePath)
    {
        if ($filePath) {
            if (Storage::providesTemporaryUrls()) {
                $downloadUrl = Storage::temporaryUrl($filePath, now()->addMinutes(config('constants.expiration_time_of_temp_url')));
            } else {
                $downloadUrl = Storage::url($filePath);
            }

            return $downloadUrl;
        } else {
            return '';
        }
    }

    public static function compressAndUploadToS3($image, $realPath, $isPublic = false)
    {
        $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
        self::setTinify();

        // Step 1: Define the local temporary path
        $localTmpDir = storage_path('app/tmp');

        // Step 2: Ensure the directory exists, if not, create it
        if (! file_exists($localTmpDir)) {
            mkdir($localTmpDir, 0755, true); // Creates the directory if it doesn't exist
        }

        // Step 3: Store the image in the temporary path
        $path = $image->store('tmp', 'local'); // This will store the file in storage/app/tmp
        $fullTmpPath = storage_path('app/' . $path); // Full path to the stored file
        // $filename = urldecode(pathinfo($path, PATHINFO_FILENAME));

        $timestamp = Carbon::now('Asia/Kolkata')->format('d_m_Y') . '_' . Carbon::now('Asia/Kolkata')->format('g_i_a');
        // Get the original filename with extension
        $originalFilename = $image->getClientOriginalName();
        // Use pathinfo to get file information
        $fileInfo = pathinfo($originalFilename);
        // Get the filename without extension
        $filenameWithoutExtension = $fileInfo['filename'];
        $fileName = $filenameWithoutExtension . '_' . $timestamp . '.' . $ext; // generate filename with auto generate name and original file extension

        // Step 4: Define the real path for the image
        $destinationPath = $realPath . $fileName;

        // Step 5: Handle different file types
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            // For images, use Tinify compression
            self::setTinify();
            $sourceData = file_get_contents($fullTmpPath);
            $resultData = \Tinify\fromBuffer($sourceData)->toBuffer();
        } elseif (in_array($ext, ['pdf', 'zip'])) {
            // For PDFs, just read the file content directly
            $resultData = file_get_contents($fullTmpPath);
        } else {
            // Clean up and throw error for unsupported file types
            unlink($fullTmpPath);
            throw new \Exception('Unsupported file type. Supported types are: jpg, jpeg, png, webp, pdf');
        }

        // Step 6: Upload the compressed image to S3 with public visibility
        if (! App::environment('local')) {
            if ($isPublic) {
                Storage::disk('s3')->put($destinationPath, $resultData, 'public');
            } else {
                Storage::disk('s3')->put($destinationPath, $resultData);
            }
        } else {
            Storage::disk('public')->put($destinationPath, $resultData, 'public');
        }

        $image_array['image'] = $destinationPath;

        // Step 7: Clean up by deleting the local temporary file
        unlink($fullTmpPath);

        return $image_array;
    }
}

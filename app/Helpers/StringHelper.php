<?php

namespace App\Helpers;

use App\Enums\Types\IsHasThumbnail;
use App\Enums\Types\IsHasWaterMark;
use DB;
use Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Enums\Types\IsResizeImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class StringHelper
{
    const KEY = 'Xna4VoFQ7Ayk8szAJwc0qNmwFlw3gAJ=';
    const IV = 'G9cRYFH2gVJv8ono';
    const METHOD = 'AES-256-CBC';

    public static function decrypt($text)
    {
        return openssl_decrypt($text, self::METHOD, self::KEY, 0, self::IV);
    }

    public static function encrypt($text)
    {
        return openssl_encrypt($text, self::METHOD, self::KEY, 0, self::IV);
    }

    const DATE_FILTER_TYPE = [
        'TODAY' => 1,
        'YESTERDAY' => 2,
        'THIS_WEEK' => 3,
        'LAST_WEEK' => 4,
        'THIS_MONTH' => 5,
        'LAST_MONTH' => 6,
        'THIS_YEAR' => 7,
        'LAST_YEAR' => 8,
        'CUSTOM' => 9
    ];

    /**
     * clear phone format
     *
     * @param [type] $phone
     * @return void
     */
    public static function clearPhoneFormat($phone)
    {
        return $phone = str_replace("-", "", $phone);
    }

    /**
     * Phone Format
     */
    public static function phoneNumber($data, $format = ' ')
    {
        return substr($data, 0, 3) . $format . substr($data, 3, 3) . $format . substr($data, 6);
    }

    public static function imageToBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public static function audioToBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:audio/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public static function getNameByLocal($name, $local = 'local_name')
    {
        try {
            $data = json_decode($name);
            if ($local == 'local_name') {
                return $data->local_name;
            } else if ($local == 'latin_name') {
                return $data->latin_name;
            } else {
                return $data->latin_name;
            }
        } catch (Exception $e) {
            return $name;
        }
    }

    public static function currency($data, $decimals = 0)
    {

        $decimal_part = explode('.', $data);
        if (count($decimal_part) > 1) {
            if (intval($decimal_part[1]) > 0) {
                $decimals = strlen(intval($decimal_part[1])) > 3 ? 3 : strlen(intval($decimal_part[1]));
            } else {
                $decimals = 0;
            }

            if (!empty($data)) {
                return number_format($data, $decimals);
            }
        } else {
            return number_format($data, 0);
        }
    }

    public static function getDateFilterType($type)
    {
        $startDate = null;
        $currentDate = Carbon::now();
        $endDate = Carbon::today()->format('Y-m-d');
        if ($type == self::DATE_FILTER_TYPE['TODAY']) {
            $startDate = Carbon::today()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['YESTERDAY']) {
            $startDate = Carbon::yesterday()->format('Y-m-d');
            $endDate = Carbon::yesterday()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['THIS_WEEK']) {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['LAST_WEEK']) {
            $startDate = $currentDate->startOfWeek()->subWeek()->format('Y-m-d');
            $endDate = $currentDate->endOfWeek()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['THIS_MONTH']) {
            $startDate = $currentDate->firstOfMonth()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['LAST_MONTH']) {
            $startDate = $currentDate->startOfMonth()->subMonth()->format('Y-m-d');
            $endDate = $currentDate->endOfMonth()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['THIS_YEAR']) {
            $startDate = $currentDate->firstOfYear()->format('Y-m-d');
        } else if ($type == self::DATE_FILTER_TYPE['LAST_YEAR']) {
            $startDate = $currentDate->startOfYear()->subYear()->format('Y-m-d');
            $endDate = $currentDate->endOfYear()->format('Y-m-d');
        }
        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }

    public static function getLogDetail($id = null, $name = null, $code = null, $detail = null)
    {
        $str = '';
        if (!empty($id)) {
            $str .= ' Id: ' . $id;
        }
        if (!empty($name)) {
            $str .= ', Name: ' . $name;
        }
        if (!empty($code)) {
            $str .= ', Code: ' . $code;
        }
        if (!empty($detail)) {
            $str .= $detail;
        }
        return $str;
    }

    public static function public_path($path = null)
    {
        return rtrim(app()->basePath('public' . DIRECTORY_SEPARATOR . $path), DIRECTORY_SEPARATOR);
    }

    public static function makeDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    //upload image to server file
    private static function saveImage(
        $image,
        $imgPath,
        $imageName = '',
        $isResize = IsResizeImage::NO['id'],
        $resizeWidth = 350,
        $resizeHeight = 350,
        $isHasWaterMark = IsHasWaterMark::NO['id'],
        $waterMark = ''
    )
    {
        $imageName = empty($imageName) ? uniqid('', true) . '.webp' : $imageName;
        if (!empty($image)) {
            $path = self::public_path($imgPath);
            if (!is_dir($path)) {
                self::makeDirectory($path);
            }

            try {

                $manager = new ImageManager();

                if ($isResize == IsResizeImage::getYes()) {
                    $intervention = $manager->make($image)->fit($resizeWidth, $resizeHeight);
                } else {
                    $intervention = $manager->make($image);
                }

                //Get Info Of Image And Rotate
                //    $exif = $manager->make($image)->setFileInfoFromPath($image)->exif();
                //    if (!empty($exif) && isset($exif['Orientation'])) {
                //        switch ($exif['Orientation']) {
                //            case 8:
                //                $intervention->rotate(90);
                //                break;
                //            case 3:
                //                $intervention->rotate(180);
                //                break;
                //            case 6:
                //                $intervention->rotate(-90);
                //                break;
                //        }
                //    } else {
                //        $intervention->rotate(0);
                //    }


                if ($isHasWaterMark == IsHasWaterMark::getYes()) {
                    $x = self::getCenterCoordinateImage($intervention)['x'];
                    $y = self::getCenterCoordinateImage($intervention)['y'];
                    $intervention->text($waterMark, $x, $y, function ($font) {
                        $font->file(public_path('fonts/FranklinGothicHeavy/frahv.ttf'));
                        $font->size(19);
                        // $font->color('#0f0d0d'); //No Opacity
                        $font->color(array(15, 13, 13, 0.4)); //Have Opacity
                        $font->align('center');
                        $font->valign('middle');
                    });
                }

                $intervention->save($path . DIRECTORY_SEPARATOR . $imageName);
            } catch (Exception $ex) {
                DB::rollBack();
                response()->json(['success' => 0, 'message' => 'Error while processing image.'], 500);
            }
        }
        return $imageName;
    }

    //upload image thumbnail to server file
    private static function saveThumbnailImage($image, $imgPath, $imageName)
    {
        $imageName = empty($imageName) ? uniqid('', true) . '.webp' : $imageName;
        if (!empty($image)) {
            $path = self::public_path($imgPath);
            if (!is_dir($path)) {
                self::makeDirectory($path);
            }
            try {
                $manager = new ImageManager();
                $manager->make($image)->resize(25, 25)->save($imgPath . DIRECTORY_SEPARATOR . $imageName);
            } catch (Exception $ex) {
                response()->json(['success' => 0, 'message' => 'Error while processing image.'], 500);
            }
        }
        return $imageName;
    }

    //upload from source file
    public static function uploadImage(
        $image,
        $imagePath,
        $isHasThumbnail = IsHasThumbnail::NO['id'],
        $isResize = IsResizeImage::NO['id'],
        $resizeWidth = 350,
        $resizeHeight = 350,
        $isHasWaterMark = IsHasWaterMark::NO['id'],
        $waterMark = ''
    )
    {
        //Save Image Normal
        $imageName = uniqid('', true) . '.webp';
        self::saveImage(
            $image,
            $imagePath,
            $imageName,
            $isResize,
            $resizeWidth,
            $resizeHeight,
            $isHasWaterMark,
            $waterMark
        );

        //Save Image Thumbnail
        if ($isHasThumbnail == IsHasThumbnail::getYes()) {
            $thumbnailPath = $imagePath . DIRECTORY_SEPARATOR . 'thumbnail';
            self::saveThumbnailImage($image, $thumbnailPath, $imageName);
        }
        return $imageName;
    }

    public static function editImage(
        $image,
        $oldImage,
        $imagePath,
        $isHasThumbnail = IsHasThumbnail::NO['id'],
        $isResize = IsResizeImage::NO['id'],
        $resizeWidth = 350,
        $resizeHeight = 350,
        $isHasWaterMark = IsHasWaterMark::NO['id'],
        $waterMark = ''
    )
    {
        $newImage = null;
        if ($image == $oldImage) return $image;
        if (empty($image)) return null;

        //Save new image
        $newImage = self::uploadImage(
            $image,
            $imagePath,
            $isHasThumbnail,
            $isResize,
            $resizeWidth,
            $resizeHeight,
            $isHasWaterMark,
            $waterMark
        );

        //Delete Old
        if (!empty($oldImage)) {
            self::deleteImage($oldImage, $imagePath, $isHasThumbnail);
        }
        return $newImage;
    }

    public static function deleteImage($image, $imagePath, $isHasThumbnail = IsHasThumbnail::NO['id'])
    {
        if (!empty($image)) {
            File::delete($imagePath . '/' . $image);

            if ($isHasThumbnail == IsHasThumbnail::getYes()) {
                $thumbnailPath = $imagePath . DIRECTORY_SEPARATOR . 'thumbnail';
                File::delete($thumbnailPath . '/' . $image);
            }
        }
    }

    // Upload Audio
    public static function uploadAudio(
        $audio,
        $audioPath
    ) {
        $audioName = uniqid('', true) . '.m4a';

        if (!empty($audio)) {
            $path = $audioPath;
            if (!is_dir($path)) {
                self::makeDirectory($path);
            }

            try {
                $audioContent = base64_decode($audio);
                $file_path = $path . DIRECTORY_SEPARATOR . $audioName;

                file_put_contents($file_path, $audioContent);
                Storage::disk('public')->put($file_path, file_get_contents($file_path));

            } catch (\Exception $ex) {
                // Log the exception for further analysis (you can customize this based on your needs)
                // Log::error('Error while processing audio: ' . $ex->getMessage());

                return [
                    'success' => 0,
                    'message' => 'Error while processing audio.',
                    'error' => $ex->getMessage(),
                ];
            }
        }

        return $audioName;
    }

    public static function editAudio(
        $audio,
        $oldAudio,
        $audioPath
    ) {
        $newAudio = null;
        if ($audio == $oldAudio) return $audio;
        if (empty($audio)) return null;

        //Save new audio
        $newAudio = self::uploadAudio(
            $audio,
            $audioPath
        );

        //Delete Old
        if (!empty($oldAudio)) {
            self::deleteAudio($oldAudio, $audioPath);
        }
        return $newAudio;
    }

    //Delete Audio
    public static function deleteAudio($audio, $audioPath)
    {
        if (!empty($audio)) {
            File::delete($audioPath . '/' . $audio);
        }
    }

    /**
     * Get Center Coordinate of Image X,Y
     *
     */
    public static function getCenterCoordinateImage($image)
    {
        $imageManager = new ImageManager();
        $width = $imageManager->make($image)->width();
        $height = $imageManager->make($image)->height();
        $center_x = $width / 2;
        $center_y = $height / 2;

        return ['x' => $center_x, 'y' => $center_y];
    }

    /**
     * Get Width and Height of Image
     *
     */
    public static function getWidthHeightImage($image)
    {
        $imageManager = new ImageManager();
        $width = $imageManager->make($image)->width();
        $height = $imageManager->make($image)->height();

        return ['width' => $width, 'height' => $height];
    }

    /**
     * Get Auto Code
     *
     * @param [type] $names
     * @param [type] $local
     * @return void
     */
    public static function getAutoCode($value, $prefix, $length)
    {
        $value++;
        return $prefix . str_pad($value, $length, 0, STR_PAD_LEFT);
    }


    public static function collectionToJson($collection)
    {
        return json_decode(json_encode($collection));
    }

    /**
     * ReFormat Phone with Country Code
     */
    public static function formatPhoneWithCountryCode($requestPhone, $countryCode = "+855")
    {
        $phoneFormat = "";
        if (!empty($requestPhone)) {
            $requestPhone = trim($requestPhone);
            $phone = "";

            if (Str::contains($requestPhone, $countryCode)) {
                $phone = explode($countryCode, $requestPhone)[1];
            } else {
                $phone = $requestPhone;
            }
            if ($phone[0] == 0) {
                $phone = Str::substr($phone, 1, strlen($phone));
            }

            $phoneFormat = $countryCode . $phone;
        }

        return $phoneFormat;
    }

    //Get Transaction Fee Amount
    public static function getTransactionFeeAmount($total, $fee)
    {
        return floatval($total) * (floatval($fee) / 100);
    }

    //Get Commission Amount
    public static function getCommissionAmount($total, $commission)
    {
        return (floatval($commission) * floatval($total)) / 100;
    }

    public static function generatePaginationData($page = 1, $perPage = 10, $list = [])
    {
        $offset = ($page * $perPage) - $perPage;
        $pagination = new LengthAwarePaginator(
            array_slice($list, $offset, $perPage, true),
            count($list), // Total items
            $perPage, // Items per page
            $page // Current page
        );

        return $pagination;
    }

    public static function randomCode($digit = 6)
    {
        return mt_rand(100000, 999999);
    }
}

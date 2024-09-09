<?php


namespace App\Helpers;

use Intervention\Image\ImageManager;

class ImageHelpers{
  public static function uploadImage($image,$imagePath){
    $manager = new ImageManager();
    $img = $manager->make($image);
    $imageName = uniqid("",true).".webp";
    $path = public_path($imagePath);

    $img->save($path."/".$imageName);
    return $imageName;
  }
  public static function updateImage($image,$oldImage,$imagePath){
    if($image==$oldImage){
      return $image;
    }
    if(empty($image)){
      return null;
    }
    $newImage = self::uploadImage($image,$imagePath);
    
    return $newImage;
  }
}
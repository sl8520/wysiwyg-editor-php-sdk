<?php

namespace FroalaEditor;

use FroalaEditor\Utils\DiskManagement;

class Image {
  public static $defaultUploadOptions = array(
    'class'        => 'Image',
    'fieldname'    => 'file',
    'thumb'        => array(
      'enable'    => true,
      'columns'   => 130,
      'rows'      => 130,
      'filter'    => false,
      'blur'      => 1,
      'keepRatio' => true
    ),
    'uploadRoot'   => '',
    'uploadUrl'    => '',
    'rootFolder'   => '',
    'imageFolder'  => '',
    'fileFolder'   => '',
    'thumbFolder'  => '',
    'resize'       => array(
      'enable'    => false,
      'columns'   => 800,
      'rows'      => 600,
      'filter'    => false,
      'blur'      => 1,
      'keepRatio' => true
    ),
    'validation'   => array(
      'allowedExts'      => array('gif', 'jpeg', 'jpg', 'png', 'svg', 'blob'),
      'allowedMimeTypes' => array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/svg+xml')
    )
  );

  /**
  * Image upload to disk.
  *
  * @param options [optional]
  *   (
  *     fieldname => string
  *     validation => array OR function
  *     resize: => array
  *   )
  * @return {link: 'linkPath'} or error string
  */
  public static function upload($options = NULL) {
    // Check if there are any options passed.
    if (is_null($options)) {
      $options = Image::$defaultUploadOptions;
    } else {
      $options = array_merge(Image::$defaultUploadOptions, $options);
    }

    // Upload image.
    return DiskManagement::upload($options);
  }

  /**
  * Delete image from disk.
  *
  * @param src string
  * @return boolean
  */
  public static function delete($src, $options = NULL) {
    // Check if there are any options passed.
    if (is_null($options)) {
      $options = Image::$defaultUploadOptions;
    } else {
      $options = array_merge(Image::$defaultUploadOptions, $options);
    }
    // Delete image.
    return DiskManagement::delete($src, $options);
  }

  /**
  * List images from disk
  *
  * @param options
  *
  * @return array of image properties
  *     - on success : [{url: 'url', thumb: 'thumb', name: 'name'}, ...]
  *     - on error   : {error: 'error message'}
  */
  public static function getList($options = NULL) {
    // Check if there are any options passed.
    if (is_null($options)) {
      $options = Image::$defaultUploadOptions;
    } else {
      $options = array_merge(Image::$defaultUploadOptions, $options);
    }
    // Get file path imformation
    $path = DiskManagement::getPathFromOption($options);

    // Array of image objects to return.
    $response = array();

    // Image types.
    //$image_types = Image::$defaultUploadOptions['validation']['allowedMimeTypes'];
    $image_types = $options['validation']['allowedMimeTypes'];

    // Filenames in the uploads folder.
    $fnames = scandir($path['fullServerThumbPath']);

    // Check if folder exists.
    if ($fnames) {
      // Go through all the filenames in the folder.
      foreach ($fnames as $name) {
        // Filename must not be a folder.
        if (!is_dir($name)) {
          // Check if file is an image.

          if (in_array(mime_content_type($path['fullServerThumbPath'] . $name), $image_types)) {
            // Build the image.
            $img = new \StdClass;
            $img->url = $path['fullFilePath'] . $name;
            $img->thumb = $path['fullFileThumbPath'] . $name;
            $img->name = $name;

            // Add to the array of image.
            array_push($response, $img);
          }
        }
      }
    }

    // Folder does not exist, respond with a JSON to throw error.
    else {
      throw new Exception('Images folder does not exist!');
    }

    return $response;
  }
}

class_alias('FroalaEditor\Image', 'FroalaEditor_Image');
?>

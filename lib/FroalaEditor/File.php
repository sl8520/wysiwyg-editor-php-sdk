<?php

namespace FroalaEditor;

use FroalaEditor\Utils\DiskManagement;

class File {
  public static $defaultUploadOptions = array(
    'class'        => 'File',
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
      'allowedExts'      => array('txt', 'pdf', 'doc'),
      'allowedMimeTypes' => array('text/plain', 'application/msword', 'application/x-pdf', 'application/pdf')
    )
  );

  /**
  * File upload to disk.
  *
  * @param fileRoute string
  * @param options [optional]
  *   (
  *     fieldname => string
  *     validation => array OR function
  *   )
  * @return {link: 'linkPath'} or error string
  */
  public static function upload($options = NULL) {

    if (is_null($options)) {
      $options = File::$defaultUploadOptions;
    } else {
      $options = array_merge(File::$defaultUploadOptions, $options);
    }

    return DiskManagement::upload($options);
  }

  /**
  * Delete file from disk.
  *
  * @param src string
  * @return boolean
  */
  public static function delete($src) {
    // Check if there are any options passed.
    if (is_null($options)) {
      $options = Image::$defaultUploadOptions;
    } else {
      $options = array_merge(Image::$defaultUploadOptions, $options);
    }
    return DiskManagement::delete($src, $options);
  }
}

class_alias('FroalaEditor\File', 'FroalaEditor_File');
?>

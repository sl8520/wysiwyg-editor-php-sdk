<?php

namespace FroalaEditor\Utils;

use FroalaEditor\Utils\Utils;

class DiskManagement {
  /**
  * Upload a file to the specified location.
  *
  * @param options
  *   (
  *     fieldname => string
  *     validation => array OR function
  *     resize: => array [only for images]
  *   )
  *
  * @return {link: 'linkPath'} or error string
  */
  public static function upload($options) {

    $fieldname = $options['fieldname'];

    if (empty($fieldname) || empty($_FILES[$fieldname])) {
      throw new \Exception('Fieldname is not correct. It must be: ' . $fieldname);
    }

    if (
      isset($options['validation']) &&
      !Utils::isValid($options['validation'], $fieldname)
    ) {
      throw new \Exception('File does not meet the validation.');
    }

    // Get filename.
    $temp = explode(".", $_FILES[$fieldname]["name"]);

    // Get extension.
    $extension = end($temp);

    // Generate new random name.
    $name = sha1(microtime()) . "." . $extension;

    // Get path imformation from options
    $path = self::getPathFromOption($options);

    $mimeType = Utils::getMimeType($_FILES[$fieldname]["tmp_name"]);

    if ($options['resize']['enable']===true && $mimeType != 'image/svg+xml') {
      // Resize image.
      $resize = $options['resize'];

      // Parse the resize params.
      $columns = $resize['columns'];
      $rows = $resize['rows'];
      $filter = isset($resize['filter']) && $resize['filter'] ? $resize['filter'] : \Imagick::FILTER_UNDEFINED;
      $blur = isset($resize['blur']) ? $resize['blur'] : 1;
      $bestfit = isset($resize['keepRatio']) ? $resize['keepRatio'] : false;

      $imagick = new \Imagick($_FILES[$fieldname]["tmp_name"]);

      $imagick->resizeImage($columns, $rows, $filter, $blur, $bestfit);
      $imagick->writeImage($path['fullServerPath'] . $name);
      $imagick->destroy();
    } else {
      // Save file in the uploads folder.
      move_uploaded_file($_FILES[$fieldname]["tmp_name"], $path['fullServerPath'] . $name);
    }

    // Create thumbnail (only for image upload)
    if ($options['class']=="Image" && $options['thumb']['enable'] === true && $mimeType != 'image/svg+xml') {
      // Resize image.
      $resize = $options['thumb'];

      // Parse the resize params.
      $columns = $resize['columns'];
      $rows = $resize['rows'];
      $filter = isset($resize['filter']) && $resize['filter'] ? $resize['filter'] : \Imagick::FILTER_UNDEFINED;
      $blur = isset($resize['blur']) ? $resize['blur'] : 1;
      $bestfit = isset($resize['keepRatio']) ? $resize['keepRatio'] : false;

      $imagick = new \Imagick($path['fullServerPath'] . $name);

      $imagick->resizeImage($columns, $rows, $filter, $blur, $bestfit);
      $imagick->writeImage($path['fullServerThumbPath'] . $name);
      $imagick->destroy();
    }

    // Generate response.
    $response = new \StdClass;
    $response->link = $path['fullFilePath'] . $name;

    return $response;
  }


  /**
  * Delete file from disk.
  *
  * @param src string
  * @return boolean
  */
  public static function delete($src, $options) {
    // Get path imformation from options
    $path = self::getPathFromOption($options);

    // Convert filePath to serverPath
    $filePath = str_replace(array($path['fullFilePath'], $path['fullFileThumbPath']), $path['fullServerPath'], $src);
    $thumbPath = str_replace(array($path['fullFilePath'], $path['fullFileThumbPath']), $path['fullServerThumbPath'], $src);

    // Check if file exists.
    if (file_exists($filePath)) {
      // Delete file.
      unlink($filePath);
    }
    // Check thumb file exists.
    if (file_exists($thumbPath)) {
      // Delete file.
      unlink($thumbPath);
    }

    return true;
  }

  /**
   * get path imformation for options
   * @param  array   $options   option from Image/File class
   * @param  boolean $autoMkdir if ture, make dir if not exists
   * @return array of path imformation
   */
  public static function getPathFromOption($options, $autoMkdir=true) {
    $path = array();
    $path['serverPath'] = $options['uploadRoot'] . $options['rootFolder'];
    if (!Utils::chkFolderExists($path['serverPath'], $autoMkdir)) {
      throw new \Exception('rootFolder not Exists or mkdir fail.');
    }
    $path['filePath'] = $options['uploadUrl'] . $options['rootFolder'];
    $path['method'] = str_replace(array('Image', 'File'), array('image', 'file'), $options['class']);
    $path['fileFolder'] = isset($options[ $path['method'] . 'Folder' ]) ? $options[ $path['method'] . 'Folder' ] : '';

    $path['fullServerPath'] =  $path['serverPath'] . $path['fileFolder'];
    $path['fullFilePath'] = $path['filePath'] . $path['fileFolder'];
    if (!Utils::chkFolderExists($path['fullServerPath'], $autoMkdir)) {
      throw new \Exception('fileFolder not Exists or mkdir fail.');
    }
    $path['fullServerThumbPath'] =  $path['serverPath'] . $path['fileFolder'] . $options['thumbFolder'];
    $path['fullFileThumbPath'] = $path['filePath'] . $path['fileFolder'] . $options['thumbFolder'];
    if ($options['thumb']['enable']===true){
      if (!Utils::chkFolderExists($path['fullServerThumbPath'], $autoMkdir)) {
        throw new \Exception('fileFolder not Exists or mkdir fail.');
      }
    }
    return $path;
  }
}

// Define alias.
class_alias('FroalaEditor\Utils\DiskManagement', 'FroalaEditor_DiskManagement');
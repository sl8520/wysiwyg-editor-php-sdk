<?php

require '../lib/froala_editor.php';
$options = array(
  'validation' => null
);
$response = FroalaEditor_File::upload('/examples/uploads/', $options);

if (is_string($response)) {
  echo $response;
  http_response_code(404);
  return;
}

echo stripslashes(json_encode($response));
?>
<?php
namespace Drupal\import_export\Controller;

use Drupal\Core\Controller\ControllerBase;

class ImportPage extends ControllerBase
{
    public static function getAllContentTypes() {
        $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
        $contentTypesList = [];
        foreach ($contentTypes as $contentType) {
            $contentTypesList[$contentType->id()] = $contentType->label();
        }
        return $contentTypesList;
    }
}
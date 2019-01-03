<?php
/**
 * Created by PhpStorm.
 * User: dung.bt
 * Date: 21/11/2018
 * Time: 16:29
 */

namespace Drupal\import_export\Controller;

use \Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

class ContentExport extends ControllerBase
{
    /**
     * Get Content Type List
     */
    public static function getContentType(){
        $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
        $contentTypesList = [];
        foreach ($contentTypes as $contentType) {
            $contentTypesList[$contentType->id()] = $contentType->label();
        }
        return array(0 => 'All') + $contentTypesList;
    }

    /**
     * Gets NodesIds based on Node Type
     */
    public static function getNodeIds($nodeType){
        if (!empty($nodeType) && 0 < $nodeType) {
            $entityQuery = \Drupal::entityQuery('node');
            $entityQuery->condition('status',1);
            $entityQuery->condition('type',$nodeType);
        } else {
            $entityQuery = \Drupal::entityQuery('node');
            $entityQuery->condition('status',1);
        }

        $entityIds = $entityQuery->execute();
        return $entityIds;
    }

    /**
     * Collects Node Data
     */
    public static function getNodeDataList($entityIds,$nodeType){
        $nodeData = Node::loadMultiple($entityIds);
        foreach($nodeData as $nodeDataEach){
            $nodeCsvData[] = implode(',',self::getNodeData($nodeDataEach,$nodeType));
        }
        return $nodeCsvData;
    }

    /**
     * Gets Valid Field List
     */
    public static function getValidFieldList($nodeType){
        $nodeArticleFields = \Drupal::entityManager()->getFieldDefinitions('node',$nodeType);
        $nodeFields = array_keys($nodeArticleFields);
        $unwantedFields = array('comment','sticky','revision_default','revision_translation_affected','revision_timestamp','revision_uid','revision_log','vid','uuid','promote');

        foreach($unwantedFields as $unwantedField){
            $unwantedFieldKey = array_search($unwantedField,$nodeFields);
            unset($nodeFields[$unwantedFieldKey]);
        }
        return $nodeFields;
    }

    /**
     * Gets Manipulated Node Data
     */
    public static function getNodeData($nodeObject,$nodeType){
        $nodeData = array();
        $nodeFields = self::getValidFieldList($nodeType);
        foreach($nodeFields as $nodeField){
            $nodeData[] = (isset($nodeObject->{$nodeField}->value)) ? '"' . htmlspecialchars(strip_tags($nodeObject->{$nodeField}->value)) . '"': ((isset($nodeObject->{$nodeField}->target_id)) ? '"' . htmlspecialchars(strip_tags($nodeObject->{$nodeField}->target_id)) . '"' : '"' . htmlspecialchars(strip_tags($nodeObject->{$nodeField}->langcode)) . '"');

        }
        return $nodeData;
    }

    /**
     * Get Node Data in CSV Format
     */
    public static function getNodeCsvData($nodeType){
        $entityIds = self::getNodeIds($nodeType);
        $nodeCsvData = self::getNodeDataList($entityIds,$nodeType);
        return $nodeCsvData;
    }

}
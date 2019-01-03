<?php
namespace Drupal\import_export;

use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
class ImportContent
{
    /**
     * To get all Content Type Fields.
     */
    public static function getFields($contentType)
    {
        $fields = [];
        $arrFieldDefinitions = \Drupal::entityManager()->getFieldDefinitions('node', $contentType);
        foreach ($arrFieldDefinitions as $fieldDefinition) {
            if (!empty($fieldDefinition->getTargetBundle())) {
                $fields['name'][] = $fieldDefinition->getName();
                $fields['type'][] = $fieldDefinition->getType();
                $fields['setting'][] = $fieldDefinition->getSettings();
            }
        }
        return $fields;
    }

    public static function createNode($filedata, $contentType)
    {
        drupal_flush_all_caches();
        global $base_url;
        $logFileName = "contentimportlog.txt";
        $logFile = fopen("sites/default/files/".$logFileName, "w") or die("There is no permission to create log file. Please give permission for sites/default/file!");

        $fields = ImportForm::getFields($contentType);
        $fieldNames = $fields['name'];
        $fieldTypes = $fields['type'];

        $mimetype = 1;
        if ($mimetype) {
            if (isset($filedata['files'])) {
                $location = $filedata['files']['tmp_name']['file_upload'];
                if (($handle = fopen($location, "r")) !== FALSE) {
                    $keyIndex = [];
                    $index = 0;
                    $logVariationFields = "Content Import Begins \n \n ";
                    while (($data = fgetcsv($handle)) !== FALSE) {
                        $index++;
                        if ($index < 2) {
                            array_push($fieldNames, 'title');
                            array_push($fieldTypes, 'text');
                            array_push($fieldNames, 'langcode');
                            array_push($fieldTypes, 'lang');

                            if (!isset($data['langcode'])) {
                                $logVariationFields .= "Langcode missing --- Assuming EN as default langcode.. Import continues  \n \n";
                                $data[count($data)] = 'langcode';
                            }

                            foreach ($fieldNames as $fieldValues) {
                                $i = 0;
                                foreach ($data as $dataValues) {
                                    if ($fieldValues == $dataValues) {
                                        $logVariationFields .= "Data Type : " . $fieldValues . "  Matches \n";
                                        $keyIndex[$fieldValues] = $i;
                                    }
                                    $i++;
                                }
                            }
                            continue;
                        }

                        if (!isset($keyIndex['title']) || !isset($keyIndex['langcode'])) {
                            drupal_set_message(t('title or langcode is missing in CSV file. Please add these fields and import again'), 'error');
                            $url = $base_url . "/admin/config/content/import-content";
                            header('Location:' . $url);
                            exit;
                        }

                        $logVariationFields .= "Importing node \n \n";
                        $nodeArray = [];
                        for ($f = 0; $f < count($fieldNames); $f++) {
                            switch ($fieldTypes[$f]) {
                                case 'image':
                                    $logVariationFields .= "Importing Image (" . trim($data[$keyIndex[$fieldNames[$f]]]) . ") :: ";
                                    if (!empty($data[$keyIndex[$fieldNames[$f]]])) {
                                        $imgIndex = trim($data[$keyIndex[$fieldNames[$f]]]);
                                        $files = glob('sites/default/files/' . $contentType . '/images/' . $imgIndex);
                                        $fileExists = file_exists('sites/default/files/' . $imgIndex);
                                        if (!$fileExists) {
                                            $images = [];
                                            foreach ($files as $file_name) {
                                                $image = File::create(['uri' => 'public://' . $contentType . '/images/' . basename($file_name)]);
                                                $image->save();
                                                $images[basename($file_name)] = $image;
                                                $imageId = $images[basename($file_name)]->id();
                                                $imageName = basename($file_name);
                                            }

                                            $nodeArray[$fieldNames[$f]] = [
                                                [
                                                    'target_id' => $imageId,
                                                    'alt' => $nodeArray['title'],
                                                    'title' => $nodeArray['title'],
                                                ]
                                            ];
                                            $logVariationFields .= "Image uploaded successfully \n ";
                                        }

                                    }
                                    $logVariationFields .= " Success \n";
                                    break;
                                case 'datetime':
                                    $logVariationFields .= "Importing Datetime (" . $fieldNames[$f] . ") :: ";
                                    $dateArray = explode(':', $data[$keyIndex[$fieldNames[$f]]]);
                                    if (count($dateArray) > 1) {
                                        $dateTimeStamp = strtotime($data[$keyIndex[$fieldNames[$f]]]);
                                        $newDateString = date('Y-m-d\TH:i:s', $dateTimeStamp);
                                    } else {
                                        $dateTimeStamp = strtotime($data[$keyIndex[$fieldNames[$f]]]);
                                        $newDateString = date('Y-m-d', $dateTimeStamp);
                                    }
                                    $nodeArray[$fieldNames[$f]] = ["value" => $newDateString];

                                    $logVariationFields .= " Success \n";
                                    break;
                                case 'text_long':
                                case 'text':
                                    $logVariationFields .= "Importing Content (" . $fieldNames[$f] . ") :: ";
                                    $nodeArray[$fieldNames[$f]] = [
                                        'value' => $data[$keyIndex[$fieldNames[$f]]],
                                        'format' => 'full_html'
                                    ];
                                    $logVariationFields .= " Success \n";
                                    break;
                                case 'list_string':
                                case 'string':
                                    $logVariationFields .= "Importing Content (" . $fieldNames[$f] . ") :: ";
                                    $nodeArray[$fieldNames[$f]] = [
                                        'value' => $data[$keyIndex[$fieldNames[$f]]],
                                    ];
                                    $logVariationFields .= " Success \n";
                                    break;

                                //                            case 'entity_reference_revisions':
                                //                            case 'text_with_summary':
                                //                                $logVariationFields .= "Importing Content (".$fieldNames[$f].") :: ";
                                //                                $nodeArray[$fieldNames[$f]] = [
                                //                                    'summary' => substr(strip_tags($data[$keyIndex[$fieldNames[$f]]]), 0, 100),
                                //                                    'value' => $data[$keyIndex[$fieldNames[$f]]],
                                //                                    'format' => 'full_html'
//                                ];
//                                $logVariationFields .= " Success \n";
//                                break;

//                            case 'timestamp':
//                                $logVariationFields .= "Importing Content (".$fieldNames[$f].") :: ";
//                                $nodeArray[$fieldNames[$f]] = ["value" => $data[$keyIndex[$fieldNames[$f]]]];
//                                $logVariationFields .= " Success \n";
//                                break;
//
//                            case 'boolean':
//                                $logVariationFields .= "Importing Boolean (".$fieldNames[$f].") :: ";
//                                $nodeArray[$fieldNames[$f]] = ($data[$keyIndex[$fieldNames[$f]]] == 'On' ||
//                                    $data[$keyIndex[$fieldNames[$f]]] == 'Yes' ||
//                                    $data[$keyIndex[$fieldNames[$f]]] == 'on' ||
//                                    $data[$keyIndex[$fieldNames[$f]]] == 'yes') ? 1 : 0;
//                                $logVariationFields .= " Success \n";
//                                break;

                                case 'langcode':
                                    $logVariationFields .= "Importing Langcode (" . $fieldNames[$f] . ") :: ";
                                    $nodeArray[$fieldNames[$f]] = ($data[$keyIndex[$fieldNames[$f]]] != '') ? $data[$keyIndex[$fieldNames[$f]]] : 'en';
                                    $logVariationFields .= " Success \n";
                                    break;

                                default:
                                    $nodeArray[$fieldNames[$f]] = $data[$keyIndex[$fieldNames[$f]]];
                                    break;

                            }
                        }

                        if (!isset($nodeArray['langcode'])) {
                            $nodeArray['langcode'] = 'en';
                        }

                        $nodeArray['type'] = strtolower($contentType);
                        $nodeArray['uid'] = 1;
                        $nodeArray['promote'] = 0;
                        $nodeArray['sticky'] = 0;
                        if ($nodeArray['title']['value'] != '') {
                            $node = Node::create($nodeArray);
                            $node->save();

                            $logVariationFields .= "Node Imported successfully \n\n";
                            fwrite($logFile, $logVariationFields);
                        }
                    }
                    fclose($handle);
                    $url = $base_url . "/admin/content";
                    header('Location:' . $url);
                    exit;
                }
            }
        }
    }

    public static function nodeImportFinished($success, $results, $operations)
    {
        if ($success) {
            $message = \Drupal::translation()->formatPlural(
                count($results),
                'One node processed.', '@count posts processed.'
            );
        }
        else {
            $message = t('Finished with an error.');
        }
        drupal_set_message($message);
    }
}
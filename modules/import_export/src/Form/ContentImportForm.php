<?php
namespace Drupal\import_export\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\import_export\Controller\ImportPage;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class ContentImportForm extends ConfigFormBase {

    /**
     * Returns a unique string identifying the form.
     *
     * The returned ID should be a unique string that can be a valid PHP function
     * name, since it's used in hook implementation names such as
     * hook_form_FORM_ID_alter().
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'import_content';
    }

    /**
     * Gets the configuration names that will be editable.
     *
     * @return array
     *   An array of configuration object names that are editable if called in
     *   conjunction with the trait's config() method.
     */
    protected function getEditableConfigNames()
    {
        return ['import_export.settings'];
    }

    /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $contentTypes = ImportPage::getAllContentTypes();
        $selected = 0;
        $form['import_content_content_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Select Content Type'),
            '#options' => $contentTypes,
            '#default_value' => $selected,
        ];

        $form['file_upload'] = [
            //'#type' => 'managed_file',
            '#type' => 'file',
            '#title' => t('Import CSV File'),
            '#description' => t('Select the CSV file to be imported.'),
//            '#upload_location' => 'public://importcsv/',
//            '#default_value' => '',
            "#upload_validators"  => array("file_validate_extensions" => array("csv")),
//            '#states' => array(
//                'visible' => array(
//                    ':input[name="File_type"]' => array('value' => t('Upload Your File')),
//                ),
//            ),
        ];

        $form['actions']['#type'] = 'actions';

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Import'),
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $content_type = $form_state->getValue('import_content_content_type');
        /*$file_upload = $form_state->getValue('file_upload');
        $file = File::load($file_upload[0]);
        if (!empty($file)) {
            $file->setPermanent();
            $file->save();
        $data = $this->CSVToArray($file->getFileUri(), ',');

            foreach($data as $row) {
                $operations[] = ['\Drupal\import_export\Form\ContentImportForm::addImportContentItem', [$row, $content_type]];
            }*/
        $data = $this->CSVToArray($_FILES);
        $operations[] = ['\Drupal\import_export\Form\ContentImportForm::addImportContentItem', [$data, $content_type]];

        $batch = [
            'title' => t('Importing Nodes...'),
            'operations' => $operations,
            'finished' => '\Drupal\import_export\Form\ContentImportForm::nodeImportFinished',
        ];

        batch_set($batch);
        drupal_set_message(t('Node has been imported succesfully.'));

    }

    public function CSVToArray($filename)
    {

        $data = array();
        $location = $filename['files']['tmp_name']['file_upload'];
        if (($handle = fopen($location, 'r')) !== FALSE ) {
            $index = 0;
            while (($row = fgetcsv($handle)) !== FALSE)
            {$data[$index] = $row;
                $index++;

            }
            fclose($handle);
        }

        return $data;
    }
    /*public function CSVToArray($filename = '', $delimiter)
    {
        if(!file_exists($filename) || !is_readable($filename)) return FALSE;
        $header = NULL;
        $data = array();

        if (($handle = fopen($filename, 'r')) !== FALSE ) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header){
                    $header = $row;
                }else{
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }*/

    public static function addImportContentItem(array $item, $content_type, &$context)
    {
        $context['sandbox']['current_item'] = $item;
        $message = 'Creating ' . $item['title'];
        $results = array();
        //self::createNodeByDomynic($item, $content_type);
        self::createNodeContent($item, $content_type);
        $context['message'] = $message;
        $context['results'][] = count($item) - 1;
    }

    /**
     * To get all Content Type Fields.
     */
    public static function getFields($contentType) {
        $fields = [];
        foreach (\Drupal::entityManager()
                     ->getFieldDefinitions('node', $contentType) as $field_definition) {
            if (!empty($field_definition->getTargetBundle())) {
                $fields['name'][] = $field_definition->getName();
                $fields['type'][] = $field_definition->getType();
                $fields['setting'][] = $field_definition->getSettings();
            }
        }
        return $fields;
    }

    public static function createNodeContent (array $arr_data, $contentType)
    {
        global $base_url;
        $fields = self::getFields($contentType);
        $fieldNames = $fields['name'];
        $fieldTypes = $fields['type'];

        $mimetype = 1;
        if ($mimetype) {
            $keyIndex = [];
            $a = 0;
            foreach($arr_data as $k => $data) {
                $a++;
                if ($a < 2) {
                    array_push($fieldNames, 'title');
                    array_push($fieldTypes, 'text');
                    array_push($fieldNames, 'langcode');
                    array_push($fieldTypes, 'lang');
                    if(!isset($data['langcode'])) {
                        $data[count($data)] = 'langcode';
                    }
                    foreach ($fieldNames as $fieldValues) {
                        $i = 0;
                        foreach ($data as $dataValues) {
                            if ($fieldValues == $dataValues) {
                                $keyIndex[$fieldValues] = $i;
                            }
                            $i++;
                        }
                    }
                    continue;
                }
                for ($f = 0; $f < count($fieldNames); $f++) {
                    switch ($fieldTypes[$f]) {
                        case 'image':
                            if (!empty($data[$keyIndex[$fieldNames[$f]]])) {
                                $imgIndex = trim($data[$keyIndex[$fieldNames[$f]]]);
                                $files = glob('sites/default/files/' . date('Y-m') . '/' . $imgIndex);
                                $fileExists = file_exists('sites/default/files/'.$imgIndex);
                                if(!$fileExists) {
                                    $images = [];
                                    foreach ($files as $file_name) {
                                        $image = File::create([
                                            'uri' => 'public://' . date('Y-m') . '/' . basename($file_name),
                                            'uid' => 1
                                        ]);
                                        $image->save();
                                        $images[basename($file_name)] = $image;
                                        $imageId = $images[basename($file_name)]->id();
                                    }
                                    $nodeArray[$fieldNames[$f]] = [
                                        [
                                            'target_id' => $imageId,
                                            'alt' => $nodeArray['title'],
                                            'title' => $nodeArray['title'],
                                        ]
                                    ];
                                }

                            }
                            break;

                        case 'text_long':
                        case 'text':
                            $nodeArray[$fieldNames[$f]] = [
                                'value' => $data[$keyIndex[$fieldNames[$f]]],
                                'format' => 'full_html'
                            ];
                            break;

                        case 'entity_reference_revisions':
                        case 'text_with_summary':
                            $nodeArray[$fieldNames[$f]] = [
                                'summary' => substr(strip_tags($data[$keyIndex[$fieldNames[$f]]]), 0, 100),
                                'value' => $data[$keyIndex[$fieldNames[$f]]],
                                'format' => 'full_html'
                            ];
                            break;
                        case 'datetime':
                            $dateArray = explode(':', $data[$keyIndex[$fieldNames[$f]]]);
                            if(count($dateArray) > 1) {
                                $dateTimeStamp = strtotime($data[$keyIndex[$fieldNames[$f]]]);
                                $newDateString = date('Y-m-d\TH:i:s', $dateTimeStamp);
                            } else {
                                $dateTimeStamp = strtotime($data[$keyIndex[$fieldNames[$f]]]);
                                $newDateString = date('Y-m-d', $dateTimeStamp);
                            }
                            $nodeArray[$fieldNames[$f]] = ["value" => $newDateString];
                            break;

                        case 'langcode':
                            $nodeArray[$fieldNames[$f]] = ($data[$keyIndex[$fieldNames[$f]]] != '') ? $data[$keyIndex[$fieldNames[$f]]] : 'en';
                            break;

                        default:
                            $nodeArray[$fieldNames[$f]] = $data[$keyIndex[$fieldNames[$f]]];
                            break;
                    }
                }
                if (!isset($nodeArray['langcode'])){
                    $nodeArray['langcode'] = 'en';
                }

                $nodeArray['type'] = strtolower($contentType);
                $nodeArray['uid'] = 1;
                $nodeArray['promote'] = 0;
                $nodeArray['sticky'] = 0;
                if ($nodeArray['title']['value'] != '') {
                    $node = Node::create($nodeArray);
                    $node->save();
                }

            }
        }

    }

    public static function createNodeByDomynic($data, $contentType)
    {

        $node_array = [];
        $node_array['title'] = [
            [
                'value' => $data['field_title'],
                'format' =>  'full_html'
            ]
        ];
        $node_array['field_birth'] = [
            [
                'value' => $data['field_birth']

            ]
        ];
        $node_array['field_hobies'] = [
            [
                'value' => $data['field_hobies'],
                'format' => 'full_html'
            ]
        ];

        $imgIndex = $data['field_image'];
        $files = glob('sites/default/files/' . date('Y-m') . '/' . $imgIndex);
        $fileExists = file_exists('sites/default/files/'.$imgIndex);
        if(!$fileExists) {
            $images = [];
            foreach ($files as $file_name) {
                $image = File::create(['uri' => 'public://' . date('Y-m') . '/' . basename($file_name), 'uid' => 1]);
                $image->save();
                $images[basename($file_name)] = $image;
                $imageId = $images[basename($file_name)]->id();
            }

            $node_array['field_image'] = [
                [
                    'target_id' => $imageId,
                    'alt' => $data['field_title'],
                    'title' => $data['field_title'],
                ]
            ];
        }
        $node_array['field_gender'] = [
            [
                'value' => $data['field_gender']
            ]

        ];
        $node_array['langcode'] = 'en';
        $node_array['type'] = strtolower($contentType);
        $node_array['uid'] = 1;
        $node_array['promote'] = 0;
        $node_array['sticky'] = 0;

        $node = Node::create($node_array);
        $node->save();

    }

    public static function nodeImportFinished($success, $results, $operations)
    {
        if ($success) {
            $message = \Drupal::translation()->formatPlural(
                $results[0],
                'One node processed.', '@count posts processed.'
            );
        }
        else {
            $message = t('Finished with an error.');
        }
        drupal_set_message($message);
    }

}
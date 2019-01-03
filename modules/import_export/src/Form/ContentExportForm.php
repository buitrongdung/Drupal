<?php
/**
 * Created by PhpStorm.
 * User: dung.bt
 * Date: 21/11/2018
 * Time: 16:30
 */

namespace Drupal\import_export\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\import_export\Controller\ContentExport;
use Drupal\Core\StreamWrapper\PrivateStream;
use Drupal\Core\StreamWrapper\PublicStream;

class ContentExportForm extends FormBase
{

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
        return 'content_export_form';
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
        $form['content_type_list'] = [
            '#title'=> $this->t('Content Type'),
            '#type'=> 'select',
            '#options'=> ContentExport::getContentType()
        ];

        $form['export'] = [
            '#value'=> 'Export',
            '#type'=> 'submit'
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
        $nodeType = $form_state->getValue('content_type_list');
//        ContentExportForm::exportNode($nodeType);
        $batch = [
            'title' => t('Exporting Nodes...'),
            'init_message' => t('Exporting'),
            'operations' => [
                ['\Drupal\import_export\Form\ContentExportForm::exportNode', [$nodeType]]
            ],
            'progress_message' => t('Processed @current out of @total.'),
            'error_message' => t('An error occurred during processing'),
            'finished' => '\Drupal\import_export\Form\ContentExportForm::nodeImportFinished',
        ];

        batch_set($batch);
        drupal_set_message(t('Please copy the Export Code and paste in your other drupal site.'));


    }

    public static function exportNode($nodeType)
    {
        $csvData = ContentExport::getNodeCsvData($nodeType);
        $private_path = PrivateStream::basepath();
        $public_path = PublicStream::basepath();
        $file_base = ($private_path) ? $private_path : $public_path;
        $filename = 'content_export'. time(). '.csv';
        $filepath = $file_base . '/' . $filename;
        $csvFile = fopen($filepath, "w");
        $fieldNames = implode(',',ContentExport::getValidFieldList($nodeType));
        fwrite($csvFile,$fieldNames . "\n");
        $results = [];
        foreach($csvData as $csvDataRow){
            fwrite($csvFile,$csvDataRow . "\n");
            $results[] = $csvDataRow;
        }
        fclose($csvFile);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'. basename($filepath) . '";');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        unlink($filepath);
        exit;
    }

    public static function nodeImportFinished($success, $results, $operations)
    {
        if ($success) {
            $message = \Drupal::translation()->formatPlural(
                count($results),
                'One node exported. ','@count nodes exported.'
            );
        }
        else {
            $message = t('Finished with an error.');
        }
        drupal_set_message($message);
    }
}
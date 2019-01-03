<?php

namespace Drupal\taxonomy_import\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\file\Entity\File;
use Drupal\taxonomy\VocabularyForm;


/**
 * Contains \Drupal\taxonomy_import\Form\ImportForm.
 */
class ImportForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'import_taxonomy_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
            'taxonomy_import.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $vocabularies = taxonomy_vocabulary_get_names();
        $form['vocabulary'] = [
            '#type' => 'select',
            '#title' => $this->t('Taxonomy'),
            '#options' => array(0 => 'All Terms') + $vocabularies,
        ];
        $form['file_upload'] = [
            '#type' => 'file',
            '#title' => t('Import CSV File'),
            '#description' => t('Select the CSV file to be imported.'),
            "#upload_validators" => array("file_validate_extensions" => array("csv")),
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
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $vocabulary = $form_state->getValue('vocabulary');
        $data = $this->CSVToArray($_FILES);

        if ($vocabulary == 0) {
            $this->createVocabulary($data);
            $allVocabulary = taxonomy_vocabulary_get_names();
            foreach ($allVocabulary as $vocab) {
                $item = $this->getData($data, $vocab);
                $this->batchImport($item, $vocab);
            }
        }
        else {
            $item = $this->getData($data, $vocabulary);
            if (empty($item)) {
                drupal_set_message(t('All terms have existed or no term exists in the CSV files'));
                return;
            }
            $this->batchImport($item, $vocabulary);

        }

    }

    public function createVocabulary($data) {
        $index = 0;
        foreach ($data as $k => $vocabularies) {
            $index++;
            if ($index < 2) {
                foreach ($vocabularies as $vocabulary => $v) {
                    if (!empty($vocabulary) && !$this->checkVocabulary($this->convert_str($vocabulary))) {
                        $createVocab = Vocabulary::create([
                            'vid' => $this->convert_str($vocabulary),
                            'machine_name' => $this->convert_str($vocabulary),
                            'name' => $vocabulary,
                        ]);
                        $createVocab->save();
                    }
                }
            }
        }
    }

    public function checkVocabulary($vid) {
        $exists = Vocabulary::load($vid);
        if ($exists) return true;
        return;
    }

    public function getData($data, $vocabulary) {
        $item = [];
        foreach ($data as $value) {
            if (!empty($value)) {
                $i = 0;
                foreach ($value as $k => $v) {
                    if ($this->checkTermOnCSV($k, $vocabulary) && $this->checkExistsTerm($v, $vocabulary)) {
                        if (!in_array($v, $item, true)) {
                            array_push($item, $v);
                        }
                    }
                    $i++;
                }
            }
        }
        return $item;
    }

    public function batchImport($item, $vocabulary) {
        try {
            $operations = [];
            foreach ($item as $row) {
                if (!empty($row)) {
                    $operations[] = ['\Drupal\taxonomy_import\Form\ImportForm::addImportTaxonomy', [$row, $vocabulary]];
                }
            }

            $batch = [
                'title' => t('Importing Taxonomy...'),
                'operations' => $operations,
                'init_message' => t('Import is starting.'),
                'finished' => '\Drupal\taxonomy_import\Form\ImportForm::termImportFinished',
            ];

            batch_set($batch);
            drupal_set_message(t('Term has been imported succesfully.'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * check Term On CSV.
     */
    public function checkTermOnCSV($field_name, $vocabulary) {
        $field_name = $this->convert_str($field_name);
        if ($field_name === $vocabulary) {
            return true;
        }
        return;
    }

    /**
     * check Exists of Term.
     */
    public static function checkExistsTerm($term, $vocabulary) {
        if (taxonomy_term_load_multiple_by_name($term, $vocabulary))
            return;
        else
            return true;
    }

    /**
     * convert string Taxonomy.
     */
    public function convert_str($string) {
        $string = strtolower($string);
        $string = str_replace("ß", "ss", $string);
        $string = str_replace("%", "", $string);
        $string = preg_replace("/[^_a-zA-Z0-9 -]/", "", $string);
        $string = str_replace(array('%20', ' '), '_', $string);
        return $string;
    }

    /**
     * convert csv to array.
     */
    public function CSVToArray($filename) {
        try {
            $data = array();
            $header = NULL;
            $location = $filename['files']['tmp_name']['file_upload'];
            if (($handle = fopen($location, 'r')) !== FALSE) {
                $index = 0;
                while (($row = fgetcsv($handle)) !== FALSE) {
                    if (!$header) {
                        $header = $row;
                    }
                    else {
                        $data[] = array_combine($header, $row);
                    }
                    $index++;
                }
                fclose($handle);
            }

            return $data;
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Import Taxonomy.
     */
    public function addImportTaxonomy($data, $vocabulary, &$context) {
        try {
            $item['name'] = $data;
            $context['sandbox']['current_item'] = $item;
            $message = 'Creating ' . $item['name'];
            $results = array();
            $this->createTerm($item, $vocabulary);
            $context['message'] = $message;
            $context['results'][] = $item;
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Function to implement import taxonomy functionality.
     */
    public function createTerm($data, $vocabulary) {
        try {
            $term_array = [];
            $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $term_array['vid'] = $vocabulary;
            $term_array['status'] = $data['status'] ? $data['status'] : 1;
            $term_array['name'] = $data['name'] ? $data['name'] : '';
            $term_array['description__value'] = $data['description__value'] ? $data['description__value'] : '';
            $term_array['description__format'] = $data['description__format'] ? $data['description__format'] : '';
            $term_array['langcode'] = $langcode;
            $term_array['default_langcode'] = $data['default_langcode'] ? $data['default_langcode'] : 1;
            $term_array['weight'] = $data['weight'] ? $data['weight'] : 0;
            $term = Term::create($term_array);
            $term->save();
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Function called when Import Finish.
     */
    public function termImportFinished($success, $results, $operations) {
        if (!$success) {
            $message = t('Finished with an error.');
        }
        drupal_set_message($message);
    }

}

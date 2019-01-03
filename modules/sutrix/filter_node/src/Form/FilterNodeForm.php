<?php
namespace Drupal\filter_node\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;


class FilterNodeForm extends FormBase
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
        return 'filter_node_form';
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
        $list_content_type = $this->getContentType();

        $form['content_type'] = [
            '#type' => 'select',
            '#options' => [0 => 'All'] + $list_content_type,
            '#default_value' => '',
            '#title' => t('Content Type'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => t('Filter')
        ];

        $form['#actions'] = '';
        $form['#method'] = 'GET';

        $form['form'] = [
            '#markup' => $form,
            '#access' => TRUE
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
    }

    public function getContentByType($type = NULL)
    {
        if (!empty($type) || 0 < $type) {
            $query = \Drupal::entityQuery('node')
                ->condition('status', 1)
                ->condition('type', $type);
        } else {
            $query = \Drupal::entityQuery('node')
                ->condition('status', 1);
        }
        $nids = $query->execute();

        $arr_content = [];
        foreach($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $title = $node->title->value;
            $arr_content[$nid] = $title;
        }
        return $arr_content;
    }

    public function getContentType()
    {
        $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();

        $contentTypesList = [];
        foreach ($contentTypes as $contentType) {
            $contentTypesList[$contentType->id()] = $contentType->label();
        }
        return $contentTypesList;
    }
}
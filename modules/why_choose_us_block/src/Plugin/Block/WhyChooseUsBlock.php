<?php
namespace Drupal\why_choose_us_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'service block' block.
 *
 * @Block(
 *   id = "why_choose_us_block",
 *   admin_label = @Translation("Why Choose Us Block"),
 *   category = @Translation("Custom Block")
 * )
 */

class WhyChooseUsBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return parent::defaultConfiguration() + [
                'arr_item' => [],
            ];
    }

    /**
     * Overrides \Drupal\Core\Block\BlockBase::blockForm().
     *
     * Adds body and description fields to the block configuration form.
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);
        $config = $this->getConfiguration();

        $form['#tree'] = TRUE;

        $form['items_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('List items'),
            '#prefix' => '<div id="items-fieldset-wrapper">',
            '#suffix' => '</div>',
        ];

        $form['items_fieldset']['image'] = array(
            '#type' => 'managed_file',
            '#upload_location' => 'public://upload/',
            '#multiple' => TRUE,
            '#title' => t('Slider Image'),
            '#upload_validators' => [
                'file_validate_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'file_validate_size' => array(10485760),
            ],
            '#default_value' => isset($this->configuration['image']) ? $this->configuration['image'] : '',
            '#description' => t('The image to display'),
//            '#required' => true
        );
        $items = [];
        if ($config['arr_item']) {
            $items = $config['arr_item'];
        }

        $form['items_fieldset']['text'] = [
            '#type' => 'textarea',
            '#default_value' => $this->configuration['text'],
            '#title' => $this->t('Text'),
        ];
        if (!$form_state->has('num_items')) {
            $form_state->set('num_items', count($config['arr_item']));
        }

        $name_field = $form_state->get('num_items');

//        if (empty($name_field)) {
//            $name_field = $form_state->set('num_names', 1);
//        }

        for ($i = 0; $i < $name_field; $i++) {
            $items = array_values($items);
            $form['items_fieldset']['items'][$i] = [
                '#type' => 'textfield',
                '#title' => t('Item'),
                '#default_value' => $items[$i],
            ];
        }

        $form['items_fieldset']['actions'] = [
            '#type' => 'actions',
        ];

        $form['items_fieldset']['actions']['add_item'] = [
            '#type' => 'submit',
            '#value' => t('Add one more'),
            '#submit' => [[$this, 'addOne']],
            '#ajax' => [
                'callback' => [$this, 'addmoreCallback'],
                'wrapper' => 'items-fieldset-wrapper',
            ],
        ];

        if ($name_field > 1) {
            $form['items_fieldset']['actions']['remove_item'] = [
                '#type' => 'submit',
                '#value' => t('Remove one'),
                '#submit' => [[$this, 'removeCallback']],
                '#ajax' => [
                    'callback' => [$this, 'addmoreCallback'],
                    'wrapper' => 'items-fieldset-wrapper',
                ]
            ];
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $arr_text = $form_state->getValue(['items_fieldset']);
        $this->configuration['text'] = $arr_text['text'];
        $this->configuration['image'] = $arr_text['image'];
        $items = $arr_text['items'];

        foreach ($items as $key => $item) {
            if ($item === '' || !$item) {
                unset($items[$key]);
            }
        }
        $this->configuration['arr_item'] = $items;

    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    public function addOne(array &$form, FormStateInterface $form_state) {
        $name_field = $form_state->get('num_items');
        $add_button = $name_field + 1;
        $form_state->set('num_items', $add_button);
        $form_state->setRebuild();
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     * @return mixed
     */
    public function addmoreCallback(array &$form, FormStateInterface $form_state) {
        // The form passed here is the entire form, not the subform that is
        // passed to non-AJAX callback.
        return $form['settings']['items_fieldset'];
    }

    /**
     * @param array $form
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     */
    public function removeCallback(array &$form, FormStateInterface $form_state) {
        $name_field = $form_state->get('num_items');
        if ($name_field > 1) {
            $remove_button = $name_field - 1;
            $form_state->set('num_items', $remove_button);
        }
        $form_state->setRebuild();
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $text = $this->configuration['text'];
        $items = $this->configuration['arr_item'];
        $fids = $this->configuration['image'];

        $arr_image = [];
        foreach ($fids as $fid) {
            $file = \Drupal\file\Entity\File::load($fid);
            $file_url = $file->url();
            $file_alt = $file->getFilename();
            $arr_image[$fid] = [
                'url' => $file_url,
                'alt' => $file_alt
            ];
        }

        $build['image'] =  array(
            '#markup' => $arr_image,
            '#access' => TRUE
        );

        $build['text'] =  array(
            '#markup' => $text,
            '#access' => TRUE
        );

        $build['items'] =  array(
            '#markup' => $items,
            '#access' => TRUE
        );
        return $build;
    }


}

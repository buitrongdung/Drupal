<?php
namespace Drupal\testimonial_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 *
 * @Block(
 *   id = "testimonial_block",
 *   admin_label = @Translation("Testimonial Block"),
 *   category = @Translation("Custom block")
 * )
 */

class TestimonialBlock extends BlockBase
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
            '#title' => $this->t('List Testimonial'),
            '#prefix' => '<div id="items-fieldset-wrapper">',
            '#suffix' => '</div>',
        ];

        $items = [];
        if ($config['arr_item']) {
            $items = $config['arr_item'];
        }

        if (!$form_state->has('num_items')) {
            $form_state->set('num_items', count($config['arr_item']));
        }

        $name_field = $form_state->get('num_items');


        for ($i = 0; $i < $name_field; $i++) {
            $items = array_values($items);

            $form['items_fieldset'][$i]['image'] = [
                '#type' => 'managed_file',
                '#upload_location' => 'public://upload/',
                '#title' => $this->t('Image'),
                '#upload_validators' => [
                    'file_validate_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                    'file_validate_size' => array(10485760),
                ],
                '#default_value' => $items[$i]['image'],
                '#description' => $this->t('The image to display'),
                '#required' => true
            ];

            $form['items_fieldset'][$i]['name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Name'),
                '#default_value' => $items[$i]['name'],
                '#required' => true

            ];

            $form['items_fieldset'][$i]['job'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Jobs'),
                '#default_value' => $items[$i]['job'],
                '#required' => true

            ];

            $form['items_fieldset'][$i]['testimonial'] = [
                '#type' => 'textarea',
                '#default_value' => $items[$i]['testimonial'],
                '#title' => $this->t('Testimonial'),
                '#required' => true

            ];
        }

        $form['items_fieldset']['actions'] = [
            '#type' => 'actions',
        ];

        $form['items_fieldset']['actions']['add_item'] = [
            '#type' => 'submit',
            '#value' => $this->t('Add one more'),
            '#submit' => [[$this, 'addOne']],
            '#ajax' => [
                'callback' => [$this, 'addmoreCallback'],
                'wrapper' => 'items-fieldset-wrapper',
            ],
        ];

        if ($name_field > 1) {
            $form['items_fieldset']['actions']['remove_item'] = [
                '#type' => 'submit',
                '#value' => $this->t('Remove one'),
                '#submit' => [[$this, 'removeCallback']],
                '#ajax' => [
                    'callback' => [$this, 'addmoreCallback'],
                    'wrapper' => 'items-fieldset-wrapper',
                ]
            ];
        }


        $form['background'] = [
            '#type' => 'details',
            '#title' => $this->t('Background Image'),
            // Open if not set to defaults.
            '#open' => '',
            '#process' => [[get_class(), 'processParents']],
        ];

        $form['background']['bg_image'] = [
            '#type' => 'managed_file',
            '#upload_location' => 'public://upload/',
            '#upload_validators' => [
                'file_validate_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'file_validate_size' => array(10485760),
            ],
            '#default_value' => $this->configuration['bg_image'],
            '#description' => $this->t('The image to display'),
        ];

        return $form;
    }

    public static function processParents(&$element, FormStateInterface $form_state, &$complete_form) {
        array_pop($element['#parents']);
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $items = $form_state->getValue(['items_fieldset']);
        foreach ($items as $key => $item) {
            if ($item === '' || !$item) {
                unset($items[$key]);
            }
        }
        $this->configuration['arr_item'] = $items;

        $bg_image = $form_state->getValue(['bg_image']);
        $this->configuration['bg_image'] = $bg_image;
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
        $items = $this->configuration['arr_item'];
        $person_inf = [];
        foreach ($items as $k => $v) {
            $file_url = $file_alt = '';
            foreach ($v['image'] as $img) {
                $file = \Drupal\file\Entity\File::load($img);
                $file_url = $file->url();
                $file_alt = $file->getFilename();
            }

            $person_inf[$k] = [
                'name' => $v['name'],
                'testimonial' => $v['testimonial'],
                'job' => $v['job'],
                'url' => $file_url,
                'alt' => $file_alt
            ];
        }

        $bg_image_id = $this->configuration['bg_image'];
        $bg_image_url = '';
        foreach ($bg_image_id as $fid) {
            $load = \Drupal\file\Entity\File::load($fid);
            $bg_image_url = ['url' => $load->url()];
        }

        $build['bg_image_url'] =  array(
            '#markup' => $bg_image_url,
            '#access' => TRUE
        );

        $build['persons'] =  array(
            '#markup' => $person_inf,
            '#access' => TRUE
        );
        return $build;
    }


}

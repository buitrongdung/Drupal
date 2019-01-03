<?php
namespace Drupal\service_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'service block' block.
 *
 * @Block(
 *   id = "service_block",
 *   admin_label = @Translation("Service Block"),
 *   category = @Translation("Custom Service block")
 * )
 */

class ServiceBlock extends BlockBase
{public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['#tree'] = TRUE;

    $form['service'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('List service'),
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
        $form['service'][$i]['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#default_value' => $items[$i]['title'],
        ];

        $form['service'][$i]['desc'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Description'),
            '#default_value' => $items[$i]['desc'],
        ];
    }

    $form['service']['actions'] = [
        '#type' => 'actions',
    ];

    $form['service']['actions']['add_item'] = [
        '#type' => 'submit',
        '#value' => t('Add one more'),
        '#submit' => [[$this, 'addOne']],
        '#ajax' => [
            'callback' => [$this, 'addmoreCallback'],
            'wrapper' => 'items-fieldset-wrapper',
        ],
    ];

    if ($name_field > 1) {
        $form['service']['actions']['remove_item'] = [
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
        $items = $form_state->getValue(['service']);

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
        return $form['settings']['service'];
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
        $list_service = [];
        foreach ($items as $k=>$v) {
            $list_service[$k] = [
                'title' => $v['title'],
                'desc'  => $v['desc']
            ];
        }

        $build['list_service'] =  array(
            '#markup' => $list_service,
            '#access' => TRUE
        );
        return $build;
    }

}

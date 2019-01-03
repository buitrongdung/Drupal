<?php
namespace Drupal\contact_block\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @file
 * Contains \Drupal\contact_block\Form\ContactBlockForm
 */
class ContactBlockForm extends FormBase
{
    protected $configFactory;

    protected $renderer;

    public function __construct(ConfigFactoryInterface $config_factory, RendererInterface $renderer) {
        $this->configFactory = $config_factory;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('config.factory'),
            $container->get('renderer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'contact_block_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $param = NULL)
    {
        // TODO: Implement buildForm() method.

        $form['name'] = [
            '#type' => 'textfield',
            '#default_value' => '',
            '#attributes' => [
                'placeholder' => t('Name'),
            ]
        ];

        $form['email'] = [
            '#type' => 'email',
            '#default_value' => '',
            '#attributes' => [
                'placeholder' => t('Email'),
            ]
        ];

        $form['subject'] = [
            '#type' => 'textfield',
            '#default_value' => '',
            '#attributes' => [
                'placeholder' => t('Subject'),
            ]
        ];

        $form['message'] = [
            '#type' => 'textarea',
            '#default_value' => '',
            '#attributes' => [
                'placeholder' => t('Message'),

            ]
        ];

        $form['#action'] = '';
        $form['#method'] = 'get';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => t('Send message'),
            '#attributes' => [
                'placeholder' => t('Message'),
                'class' => ['main-btn']
            ]
        ];

        $form['contact_inf'] = [
            '#markup' => $param,
            '#access' => TRUE
        ];

        $form['form'] = [
            '#markup' => $form,
            '#access' => TRUE
        ];

        return $form;

    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        if (strpos($values['email'], '.com') === FALSE ) {
            $form_state->setErrorByName('email', t('This is not a .com email address.'));
        }
        if (strlen($values['name']) === '') {
            $form_state->setErrorByName('name', t('Please enter your name'));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        drupal_set_message($this->t('Your email address is @email', array('@email' => $form_state['values']['email'])));
    }

}
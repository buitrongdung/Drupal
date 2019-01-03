<?php
namespace Drupal\copyright_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 *
 * @Block(
 *   id = "copyright_block",
 *   admin_label = @Translation("Copyright Block"),
 *   category = @Translation("Custom block")
 * )
 */

class CopyrightBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    protected $configFactory;

    public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->configFactory = $config_factory;

    }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('config.factory')
        );
    }

    public function blockForm($form, FormStateInterface $form_state)
    {
        parent::blockForm($form, $form_state); // TODO: Change the autogenerated stub
        $form['copy_right_block'] = [
            '#type' => 'fieldset',
            '#title' => t('Footer Copyright'),
            '#description' => t(''),
        ];
        $form['copy_right_block']['text'] = [
            '#type' => 'text_format',
            '#default_value' => $this->configuration['copy_right_block']['text'],
            '#title' => $this->t('Copyright'),
            '#format' => 'full_html'
        ];

        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state); // TODO: Change the autogenerated stub
        $copyright_text= $form_state->getValue('copy_right_block');
        $this->configuration['copy_right_block'] = $copyright_text;
    }

    public function blockValidate($form, FormStateInterface $form_state)
    {
        parent::blockValidate($form, $form_state); // TODO: Change the autogenerated stub

    }


    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $build = [];
        $copy_right_text = $this->configuration['copy_right_block'];
        $build['copy_right_text'] =  array(
            '#markup' => $copy_right_text,
            '#access' => TRUE
        );
        return $build;
    }

}
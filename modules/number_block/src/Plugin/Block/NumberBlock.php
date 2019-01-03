<?php
namespace Drupal\number_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 *
 * @Block(
 *   id = "number_block",
 *   admin_label = @Translation("Number Block"),
 *   category = @Translation("Custom block")
 * )
 */

class NumberBlock extends BlockBase
{
//    protected $configFactory;
//    public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory)
//    {
//        parent::__construct($configuration, $plugin_id, $plugin_definition);
//        $this->configFactory = $config_factory;
//    }
//
//    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
//    {
//        return new static(
//            $configuration,
//            $plugin_id,
//            $plugin_definition,
//            $container->get('config.factory')
//        );
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getFormId()
//    {
//        return 'number_block';
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function defaultConfiguration()
//    {
//        return [
//            'select_number' => TRUE
//        ];
//    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return array(
            '#type' => 'markup',     
            '#markup' => ''
        );
    }

}

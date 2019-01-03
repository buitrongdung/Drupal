<?php
namespace Drupal\home_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'home content' block.
 *
 * @Block(
 *   id = "home_content",
 *   admin_label = @Translation("Home content block"),
 *   category = @Translation("Custom block")
 * )
 */
class HomeContentBlock extends BlockBase implements ContainerFactoryPluginInterface
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

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'home_content';
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
          'select_article' => TRUE
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $config = $this->configuration;
        $defaults = $this->defaultConfiguration();

        $options = [];
        $query = \Drupal::entityQuery('node')
            ->condition('status',1);
        $nids = $query->execute();

        foreach ($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $id = $node->nid->value;
            $title = $node->title->value;
            $options[$id] = $title;
        }

        $form = parent::blockForm($form, $form_state);
        $form['home_content'] = [
            '#type' => 'fieldset',
            '#title' => 'Home content',
            '#description' => 'Choose a article'
        ];
        $form['home_content']['select_article'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['home_content'],
            '#title' => 'Article',
            '#options' => $options,
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state);
        $home_content = $form_state->getValue('home_content');
        $this->configuration['home_content'] = $home_content['select_article'];
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $build = [];
        $site_config = $this->configFactory->get('home_content.site');

        $nid = $this->configuration['home_content'];
        $node = \Drupal\node\Entity\Node::load($nid);
        $summary = $node->body->summary;
        $title = $node->title->value;
        $url = '/node/' . $nid;

        $build['site_title'] = [
            '#markup' => $title,
            '#access' => TRUE
        ];

        $build['site_summary'] = [
            '#markup' => $summary,
            '#access' => TRUE
        ];

        $build['site_url'] = [
            '#markup' => $url,
            '#access' => TRUE
        ];

        return $build;
    }

    /**
     * {@inheritdoc}
     */
//    public function getCacheTags() {
//        return Cache::mergeTags(
//            parent::getCacheTags(),
//            $this->configFactory->get('system.site')->getCacheTags()
//        );
//    }

}

?>
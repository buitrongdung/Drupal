<?php
namespace Drupal\blog_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 *
 * @Block(
 *   id = "blog_block",
 *   admin_label = @Translation("Blog Block"),
 *   category = @Translation("Custom block")
 * )
 */

class BlogBlock extends BlockBase implements ContainerFactoryPluginInterface
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
        return 'blog_block';
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
            'select_number' => TRUE
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $config = $this->configuration;
        $defaults = $this->defaultConfiguration();

        $options = [
            3 => 3,
            2 => 2
        ];

        $form = parent::blockForm($form, $form_state);
        $form['blog_block'] = [
            '#type' => 'fieldset',
            '#title' => 'Blog Block',
            '#description' => 'Choose number article'
        ];
        $form['blog_block']['select_article'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['blog_block'],
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
        $home_content = $form_state->getValue('blog_block');
        $this->configuration['blog_block'] = $home_content['select_article'];
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $arr_list = [];
        $limit = $this->configuration['blog_block'];
        $nids = \Drupal::entityQuery('node')
            ->condition('status',1)->range(0, $limit)->execute();
        foreach ($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $id = $node->nid->value;
            $title = $node->title->value;
            $summary = $node->body->summary;
            $comment_count = $node->comment->comment_count;
            $date_created = date('j M', $node->created->value);
            $user_created = $node->uid->entity->name->value;
            $file_image = file_url_transform_relative(file_create_url($node->field_images->entity->getFileUri()));
            $alt = $node->field_images->alt;
            $arr_list[$id] = [
                'url' => '/node/' . $id,
                'title' => $title,
                'summary' => $summary,
                'comment' => $comment_count,
                'date_created' => $date_created,
                'user_created' => ucwords($user_created),
                'image' => $file_image,
                'alt' => $alt
            ];
        }

        $build['article_inf'] = [
            '#markup' => $arr_list,
            '#access' => TRUE
        ];

        $build['article_limit'] = [
            '#markup' => $limit,
            '#access' => TRUE
        ];

        return $build;
    }

}

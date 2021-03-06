<?php
namespace Drupal\about_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'home content' block.
 *
 * @Block(
 *   id = "about_block",
 *   admin_label = @Translation("About block"),
 *   category = @Translation("Custom About block")
 * )
 */

class AboutBlock extends BlockBase
{
    public function blockForm($form, FormStateInterface $form_state)
    {
        $form['about'] = [
            '#type' => 'fieldset',
            '#title' => t('Welcome to Website'),
            '#description' => t('Select one or more terms ')
        ];


        $form['about']['nodes'] = [
            '#type' => 'select',
            '#multiple' => TRUE,
            '#size' => '10',
            '#options' => $this->getNodeCateAbout(),
            '#default_value' => $this->configuration['nodes'],
        ];

        return $form;
    }

    public function getNodeCateAbout()
    {
        $nodes = [];
        $query = \Drupal::entityQuery('node')
            ->condition('status',1);
        $nids = $query->execute();

        foreach ($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $id = $node->nid->value;
            $title = $node->title->value;
            $nodes[$id] = $title;
        }
        return $nodes;
    }


    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state); // TODO: Change the autogenerated stub

        $num_term = $form_state->getValue('about');
        $this->configuration['nodes'] = $num_term['nodes'];
    }

    public function loadNodes($nids = null)
    {
        $arr_list = [];
        foreach ($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $id = $node->nid->value;
            $title = $node->title->value;
            $summary = $node->body->summary;
            $arr_list[$id] = [
                'url' => '/node/' . $id,
                'title' => $title,
                'summary' => $summary,
            ];
        }
        return $arr_list;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $node_ids = $this->configuration['nodes'];
        $arr_node = $this->loadNodes($node_ids);

        $build['nodes'] = [
            '#markup' => $arr_node,
            '#access' => TRUE
        ];
        return $build;
    }

}
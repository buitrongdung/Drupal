<?php
namespace Drupal\filter_node\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 * @Block(
 *   id = "filter_node_block",
 *   admin_label = @Translation("Filter Node Block"),
 *   category = @Translation("Custom block")
 * )
 */
class FilterNodeBlock extends BlockBase
{
    public function build()
    {
        $build = \Drupal::formBuilder()->getForm('Drupal\filter_node\Form\FilterNodeForm');
        $build['form_id']['#access'] = FALSE;
        $build['form_build_id']['#access'] = FALSE;
        $build['form_token']['#access'] = FALSE;
        $build['op']['#access'] = FALSE;

        return $build;
    }
}
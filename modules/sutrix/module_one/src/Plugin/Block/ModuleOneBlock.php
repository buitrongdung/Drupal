<?php
namespace Drupal\module_one\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Class ModuleOneBlock
 * @package Drupal\module_one\Plugin\Block
 *
 * @Block(
 *   id = "module_one_block",
 *   admin_label = @Translation("Module One Block"),
 *   category = @Translation("Custom block")
 * )
 */
class ModuleOneBlock extends BlockBase
{

    /**
     * Builds and returns the renderable array for this block plugin.
     *
     * If a block should not be rendered because it has no content, then this
     * method must also ensure to return no content: it must then only return an
     * empty array, or an empty array with #cache set (with cacheability metadata
     * indicating the circumstances for it being empty).
     *
     * @return array
     *   A renderable array representing the content of the block.
     *
     * @see \Drupal\block\BlockViewBuilder
     */
    public function build()
    {
        return [
            '#type' => t('Module one Block'),
            '#markup' => t('This is module one block')
        ];
    }

    public function getContentType()
    {

    }
}
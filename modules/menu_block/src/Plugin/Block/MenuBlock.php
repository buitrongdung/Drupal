<?php
namespace Drupal\menu_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 *
 * @Block(
 *   id = "menu_block",
 *   admin_label = @Translation("Menu Block"),
 *   category = @Translation("Custom block"),
 * )
 */

class MenuBlock extends BlockBase implements ContainerFactoryPluginInterface
{

    protected $menuTree;

    public function __construct(array $configuration, $plugin_id, $plugin_definition, MenuLinkTreeInterface $menu_tree) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->menuTree = $menu_tree;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('menu.link_tree')
        );
    }


    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [
            'level' => 1,
            'depth' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $config = $this->configuration;

        $defaults = $this->defaultConfiguration();
        $form['menu_levels'] = [
            '#type' => 'details',
            '#title' => $this->t('Menu levels'),
            // Open if not set to defaults.
            '#open' => $defaults['level'] !== $config['level'] || $defaults['depth'] !== $config['depth'],
            '#process' => [[get_class(), 'processMenuLevelParents']],
        ];

        $options = range(0, $this->menuTree->maxDepth());
        unset($options[0]);

        $form['menu_levels']['level'] = [
            '#type' => 'select',
            '#title' => $this->t('Initial visibility level'),
            '#default_value' => $config['level'],
            '#options' => $options,
            '#description' => $this->t('The menu is only visible if the menu item for the current page is at this level or below it. Use level 1 to always display this menu.'),
            '#required' => TRUE,
        ];

        $options[0] = $this->t('Unlimited');

        $form['menu_levels']['depth'] = [
            '#type' => 'select',
            '#title' => $this->t('Number of levels to display'),
            '#default_value' => $config['depth'],
            '#options' => $options,
            '#description' => $this->t('This maximum number includes the initial level.'),
            '#required' => TRUE,
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->configuration['level'] = $form_state->getValue('level');
        $this->configuration['depth'] = $form_state->getValue('depth');

    }

    /**
    * Form API callback: Processes the menu_levels field element.
    *
    * Adjusts the #parents of menu_levels to save its children at the top level.
    */
    public static function processMenuLevelParents(&$element, FormStateInterface $form_state, &$complete_form) {
        array_pop($element['#parents']);
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $menu_name = 'main';
        $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);

        $level = $this->configuration['level'];//1
        $depth = $this->configuration['depth'];//0
        $parameters->setMinDepth($level);

        if ($depth > 0) {
            $parameters->setMaxDepth(min($level + $depth - 1, $this->menuTree->maxDepth()));
        }

        if ($level > 1) {
            if (count($parameters->activeTrail) >= $level) {
                $menu_trail_ids = array_reverse(array_values($parameters->activeTrail));
                $menu_root = $menu_trail_ids[$level - 1];
                $parameters->setRoot($menu_root)->setMinDepth(1);
                if ($depth > 0) {
                    $parameters->setMaxDepth(min($level - 1 + $depth - 1, $this->menuTree->maxDepth()));
                }
            }
            else {
                return [];
            }
        }

        $tree = $this->menuTree->load($menu_name, $parameters);
        $manipulators = [
            ['callable' => 'menu.default_tree_manipulators:checkAccess'],
            ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
        ];
        $tree = $this->menuTree->transform($tree, $manipulators);
        $menu_build = $this->menuTree->build($tree);
        $menu = array();
        foreach ($menu_build['#items'] as $item) {
            $menu[] = [
                'title' => $item['title'],
                'url' => $item['url']->getOptions()['fragment']
            ];
        }

        $build['menu'] = array(
            '#markup' => $menu,
            '#access' => TRUE
        );
        return $build;
//


    }

}

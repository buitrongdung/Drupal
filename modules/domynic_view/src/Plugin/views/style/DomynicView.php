<?php
/**
 * @file
 * Contains \Drupal\views\Plugin\views\style\HtmlList.
 */
namespace Drupal\domynic_view\Plugin\views\style;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render each item in an ordered or unordered
list.
 *
 * @ViewsStyle(
 *  id = "domynic_view",
 *  title = @Translation("Domynic view"),
 *  help = @Translation("Configurable domynic view for views fields."),
 *  theme = "domynic_view_format",
 *  display_types = {"normal"}
 * )
 */

class DomynicView extends StylePluginBase
{

    /**
     * Does the style plugin allows to use style plugins.*
     * @var bool
     */
    protected $usesRowPlugin = TRUE;

    /**
     * Does the style plugin support custom css class for the
    rows.
     *
     * @var bool
     */
    protected $usesRowClass = TRUE;
    /**
     * Does the style plugin support grouping of rows.
     *
     * @var bool
     */
    protected $usesGrouping = FALSE;

    /**
     * Set default options
     */

    protected function defineOptions()
    {
        $options = parent::defineOptions();
        $options['group'] = array('default' => array());
        return $options;
    }

    /**
     * Render the given style.
     */

    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {

        $options = array('' => $this->t('- None -'));
        $field_labels = $this->displayHandler->getFieldLabels(TRUE);
        $options += $field_labels;
        $grouping = $this->options['group'];
        $form['group'] = array(
            '#type' => 'select',
            '#title' => $this->t('Grouping field'),
            '#options' => $options,
            '#default_value' => $grouping,
            '#description' => $this->t('You should specify a field by which to group the records.'),
            '#required' => TRUE,
        );
        parent::buildOptionsForm($form, $form_state);
    }
}
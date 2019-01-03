<?php
namespace Drupal\country\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * @FieldFormatter(
 *     id = "country_formatter",
 *     label = @Translation("Country Formatter"),
 *     field_types = {
 *          "country"
 *     }
 * )
 */

class CountryFormatter extends FormatterBase
{

    /**
     * Builds a renderable array for a field value.
     *
     * @param \Drupal\Core\Field\FieldItemListInterface $items
     *   The field values to be rendered.
     * @param string $langcode
     *   The language that should be used to render the field.
     *
     * @return array
     *   A renderable array for $items, as an array of child elements keyed by
     *   consecutive numeric indexes starting from 0.
     */
    public function viewElements(FieldItemListInterface $items, $langcode)
    {
        // TODO: Implement viewElements() method.

        $elements = [];
        $countries = \Drupal::service('country_manager')->getList();
        foreach ($items as $delta => $item){
            if(isset($countries[$item->value])){
                $elements[$delta] = [
                    '#type' => 'markup',
                    '#markup' => '<h1>'.$countries[$item->value].'</h1>',
                ];
            }
        }

        return $elements;
    }
}
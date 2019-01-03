<?php
namespace Drupal\field_datetime\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * @FieldFormatter(
 *     id = "field_datetime_default",
 *     label = @Translation("Field DateTime Formatter"),
 *     field_types = {
 *          "field_datetime"
 *     }
 * )
 */

class FieldDateTimeFormatter extends FormatterBase
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
        foreach ($items as $delta => $item){
            $elements = [
                '#type' => 'markup',
                '#markup' => '<p>' . $item->value . '</p>',
            ];
        }

        return $elements;
    }
}
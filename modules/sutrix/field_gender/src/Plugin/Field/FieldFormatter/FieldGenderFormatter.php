<?php
namespace Drupal\field_gender\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * @FieldFormatter(
 *     id = "field_gender_formatter",
 *     label = @Translation("Field Gender Formatter"),
 *     field_types = {
 *          "field_gender"
 *     }
 * )
 */

class FieldGenderFormatter extends FormatterBase
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
        $gender = array('Male' => 'Male', 'Female' => 'Female');
        foreach ($items as $delta => $item){
            print $item->value;
//            if (isset($gender[$item->value])) {
//                $elements[$delta] = [
//                    '#type' => 'markup',
//                    '#markup' => '<p>' . $gender[$item->value] . '</p>',
//                ];
//            }

        }

        return $elements;
    }
}
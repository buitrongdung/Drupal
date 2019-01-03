<?php
/**
 * @file
 * Contains \Drupal\custom_field\Plugin\field\formatter\NutritioninfoDefaultFormatter.
 */

namespace Drupal\nutritioninfo\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'nutritioninfo_default' formatter.
 *
 * @FieldFormatter(
 *   id = "nutritioninfo_default",
 *   label = @Translation("Nutritioninfo Formatter"),
 *   field_types = {
 *      "nutritioninfo"
 *   }
 * )
 */
class NutritioninfoDefaultFormatter extends FormatterBase
{
    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode)

    {
        $rows = array();
        foreach ($items as $delta => $item) {
            $rows[] = array(
                'data' => array(
                    $item->calories,
                    $item->carbohydrate_content,
                    $item->cholesterol_content,
                    $item->fat_content,
                    $item->fiber_content,
                    $item->protein_content,
                    $item->saturated_fat_content,
                    $item->serving_size,
                    $item->sodium_content,
                    $item->sugar_content,
                    $item->trans_fat_content,
                    $item->unsaturated_fat_content
                )

            );

        }

        $headers = array(
            t('Calories'),
            t('Carbohydrate Content'),
            t('Cholesterol Content'),
            t('Fat Content'),
            t('Fiber Content'),
            t('Protein Content'),
            t('Saturated Fat Content'),
            t('Serving Size'),
            t('Sodium Content'),
            t('Sugar Content'),
            t('Trans Fat Content'),
            t('Unsaturated Fat Content'),
        );

        $table = array(
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $rows,
            '#empty' => t('No calories information available'),
            '#attributes' => array('id' => 'nutrition-info'),
        );

        return $elements = array('#markup' => drupal_render($table));

    }
}


<?php
/**
 * @file
 * Contains \Drupal\custom_field\Plugin\Field\FieldWidget\NutritioninfoDefaultWidget.
 */

namespace Drupal\nutritioninfo\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'nutritioninfo_default' widget.
 *
 * @FieldWidget(
 *   id = "nutritioninfo_default",
 *   label = @Translation("Nutrition Field Widget"),
 *   field_types = {
 *     "nutritioninfo"
 *   }
 * )
 */
class NutritioninfoDefaultWidget extends WidgetBase {
    /**
     * {@inheritdoc}
     */
    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
      $element['calories'] = array(
            '#title' => t('Calories'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->calories) ? $items[$delta]->calories : NULL,
          );
      $element['carbohydrate_content'] = array(
            '#title' => t('Carbohydrate Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->carbohydrate_content) ? $items[$delta]->carbohydrate_content : NULL,
          );
      $element['cholesterol_content'] = array(
            '#title' => t('Cholesterol Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->cholesterol_content) ? $items[$delta]->cholesterol_content : NULL,
          );

        $element['fat_content'] = array(
            '#title' => t('Fat Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->fat_content) ? $items[$delta]->fat_content : NULL,
        );

        $element['fiber_content'] = array(
            '#title' => t('Fiber Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->fiber_content) ? $items[$delta]->fiber_content : NULL,
        );

        $element['protein_content'] = array(
            '#title' => t('Protein Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->protein_content) ? $items[$delta]->protein_content : NULL,
        );

        $element['saturated_fat_content'] = array(
            '#title' => t('Saturated Fat Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->saturated_fat_content) ? $items[$delta]->saturated_fat_content : NULL,
        );

        $element['serving_size'] = array(
            '#title' => t('Serving Size'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->serving_size) ? $items[$delta]->serving_size : NULL,
        );

        $element['sodium_content'] = array(
            '#title' => t('sodium Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->sodium_content) ? $items[$delta]->sodium_content : NULL,
        );

        $element['sugar_content'] = array(
            '#title' => t('Sugar Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->sugar_content) ? $items[$delta]->sugar_content : NULL,
        );

        $element['trans_fat_content'] = array(
            '#title' => t('Trans Fat Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->trans_fat_content) ? $items[$delta]->trans_fat_content : NULL,
        );

        $element['unsaturated_fat_content'] = array(
            '#title' => t('Unsaturated Fat Content'),
            '#type' => 'textfield',
            '#default_value' => isset($items[$delta]->unsaturated_fat_content) ? $items[$delta]->unsaturated_fat_content : NULL,
        );

        return $element;
    }
}

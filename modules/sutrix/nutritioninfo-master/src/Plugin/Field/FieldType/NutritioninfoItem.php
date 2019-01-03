<?php
/**
 * @file
 * Contains \Drupal\custom_field\Plugin\Field\FieldType\CustomFieldItem.
 */

namespace Drupal\nutritioninfo\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'custom_field' field type.
 *
 * @FieldType(
 *   id = "nutritioninfo",
 *   label = @Translation("Nutrition Information"),
 *   description = @Translation("A field type used for storing nutrition information as defined by the Microdata spec at http://schema.org/ NutritionInformation."),
 *   default_widget = "nutritioninfo_default",
 *   default_formatter = "nutritioninfo_default"
 * )
 */
class NutritioninfoItem extends FieldItemBase
{
    /**
     * {@inheritdoc}
     */
    public static function schema(FieldStorageDefinitionInterface $field)
    {
        return array(
            'columns' => array(
                'calories' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'carbohydrate_content' => array(
                    'type' => 'text',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'cholesterol_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'fat_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'fiber_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'protein_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'saturated_fat_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'serving_size' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'sodium_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'sugar_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'trans_fat_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),
                'unsaturated_fat_content' => array(
                    'type' => 'varchar',
                    'length' => 256,
                    'not null' => FALSE,
                ),

            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $calories = $this->get('calories')->getValue();
        $carbohydrate_content = $this->get('carbohydrate_content')->getValue();
        $cholesterol_content = $this->get('cholesterol_content')->getValue();
        $fat_content = $this->get('fat_content')->getValue();
        $fiber_content = $this->get('fiber_content')->getValue();
        $protein_content = $this->get('protein_content')->getValue();
        $saturated_fat_content = $this->get('saturated_fat_content')->getValue();
        $serving_size = $this->get('serving_size')->getValue();
        $sodium_content = $this->get('sodium_content')->getValue();
        $sugar_content = $this->get('sugar_content')->getValue();
        $trans_fat_content = $this->get('trans_fat_content')->getValue();
        $unsaturated_fat_content = $this->get('unsaturated_fat_content')->getValue();

        //the nutrition field is empty if all of its properties are empty
        return empty($calories)
        && empty($carbohydrate_content)
        && empty($cholesterol_content)
        && empty($fat_content)
        && empty($fiber_content)
        && empty($protein_content)
        && empty($saturated_fat_content)
        && empty($serving_size)
        && empty($sodium_content)
        && empty($sugar_content)
        && empty($trans_fat_content)
        && empty($unsaturated_fat_content);
    }

    /**
     * {@inheritdoc}
     */
    static $propertyDefinitions;

    /**
     * {@inheritdoc}
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
        $properties['calories'] = DataDefinition::create('string')
            ->setLabel(t('Calories'))
            ->setDescription(t('The number of calories.'));


        $properties['carbohydrate_content'] = DataDefinition::create('string')
            ->setLabel(t('Carbohydrate Content'))
            ->setDescription(t('The number of grams of carbohydrates.'));

        $properties['cholesterol_content'] = DataDefinition::create('string')
            ->setLabel(t('Cholesterol Content'))
            ->setDescription(t('The number of milligrams of cholesterol.'));

        $properties['fat_content'] = DataDefinition::create('string')
            ->setLabel(t('Fat Content'))
            ->setDescription(t('The number of grams of fat.'));

        $properties['fiber_content'] = DataDefinition::create('string')
            ->setLabel(t('Fiber Content'))
            ->setDescription(t('The number of grams of fiber.'));

        $properties['protein_content'] = DataDefinition::create('string')
            ->setLabel(t('Protein Content'))
            ->setDescription(t('The number of grams of protein.'));

        $properties['saturated_fat_content'] = DataDefinition::create('string')
            ->setLabel(t('Saturated Fat Content'))
            ->setDescription(t('The number of grams of saturated fat.'));

        $properties['serving_size'] = DataDefinition::create('string')
            ->setLabel(t('Serving Size'))
            ->setDescription(t('The serving size, in terms of the number of volume or mass.'));

        $properties['sodium_content'] = DataDefinition::create('string')
            ->setLabel(t('Sodium Content'))
            ->setDescription(t('The number of milligrams of sodium.'));

        $properties['sugar_content'] = DataDefinition::create('string')
            ->setLabel(t('Sugar Content'))
            ->setDescription(t('The number of grams of sugar.'));

        $properties['trans_fat_content'] = DataDefinition::create('string')
            ->setLabel(t('Trans Fat Content'))
            ->setDescription(t('The number of grams of trans fat.'));

        $properties['unsaturated_fat_content'] = DataDefinition::create('string')
            ->setLabel(t('Unsaturated Fat Content'))
            ->setDescription(t('The number of grams of unsaturated fat.'));

        return $properties;
    }
}

<?php

/**
 * @file
 * Contains \Drupal\field_validation\Plugin\FieldValidationRule\IntegerFieldValidationRule.
 */

namespace Drupal\field_validation\Plugin\FieldValidationRule;

use Drupal\Core\Form\FormStateInterface;
use Drupal\field_validation\ConfigurableFieldValidationRuleBase;
use Drupal\field_validation\FieldValidationRuleSetInterface;

/**
 * IntegerFieldValidationRule.
 *
 * @FieldValidationRule(
 *   id = "integer_field_validation_rule",
 *   label = @Translation("Integer"),
 *   description = @Translation("Integer values.")
 * )
 */
class IntegerFieldValidationRule extends ConfigurableFieldValidationRuleBase {

  /**
   * {@inheritdoc}
   */
   
  public function addFieldValidationRule(FieldValidationRuleSetInterface $field_validation_rule_set) {

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = array(
      '#theme' => 'field_validation_rule_summary',
      '#data' => $this->configuration,
    );
    $summary += parent::getSummary();

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'min' => NULL,
	  'max' => NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['min'] = array(
      '#type' => 'textfield',
      '#title' => t('Minimum value'),
      '#default_value' => $this->configuration['min'],
      '#required' => TRUE,
    );
    $form['max'] = array(
      '#type' => 'textfield',
      '#title' => t('Maximum value'),
      '#default_value' => $this->configuration['max'],
      '#required' => TRUE,
    );	
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['min'] = $form_state->getValue('min');
	$this->configuration['max'] = $form_state->getValue('max');
  }
  
  public function validate($params) {
    $value = isset($params['value']) ? $params['value'] : '';
	$rule = isset($params['rule']) ? $params['rule'] : null;
	$context = isset($params['context']) ? $params['context'] : null;
	$settings = array();
	if(!empty($rule) && !empty($rule->configuration)){
	  $settings = $rule->configuration;
	}
	//drupal_set_message('134:' . $value);
	//$settings = $this->rule->settings;
    if ($value !== '' && !is_null($value)) {
      $options = array();
      if (isset($settings['min']) && $settings['min'] != '') {
	    $min = $settings['min'];
        $options['options']['min_range'] = $min;
      }
      if (isset($settings['max']) && $settings['max'] != '') {
	    $max = $settings['max'];
        $options['options']['max_range'] = $max;
      }  
      //drupal_set_message('134:' . var_export($options, true));	  
      if (FALSE === filter_var($value, FILTER_VALIDATE_INT, $options)) {
        $context->addViolation($rule->getErrorMessage());
      }      

    }	
    //return true;
  }
}

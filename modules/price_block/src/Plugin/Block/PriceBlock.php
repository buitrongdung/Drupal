<?php
namespace Drupal\price_block\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 *
 * @Block(
 *   id = "price_block",
 *   admin_label = @Translation("Price Block"),
 *   category = @Translation("Custom block")
 * )
 */

class PriceBlock extends BlockBase
{
    public function blockForm($form, FormStateInterface $form_state)
    {
        parent::blockForm($form, $form_state); // TODO: Change the autogenerated stub
        $form['basic'] = [
            '#type' => 'fieldset',
            '#title' => t('Basic Plan'),
        ];
        $form['basic']['price'] = [
            '#type' => 'textfield',
            '#size' => 5,
            '#title' => t('Price'),
            '#default_value' => $this->configuration['basic']['price'],
            '#attributes' => [
                'placeholder' => t('9')
            ]
        ];
        $form['basic']['currency'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['basic']['currency'],
            '#options' => [
                'usd' => t('USD'),
            ]
        ];
        $form['basic']['time'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['basic']['time'],
            '#options' => [
                'month' => t('/ Month'),
                'year' => t('/ Year')
            ]
        ];

        $form['basic']['disk'] = [
            '#type' => 'textfield',
            '#size' => 5,
            '#default_value' => $this->configuration['basic']['disk'],
            '#title' => t('Disk Space'),
            '#attributes' => [
                'placeholder' => t('1GB')
            ]
        ];
        $form['basic']['size'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['basic']['size'],
            '#options' => [
                'gb' => t('GB'),
                'tb' => t('TB')
            ]
        ];
        $form['basic']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#default_value' => $this->configuration['basic']['email'],
            '#title' => t('Number email account'),
            '#attributes' => [
                'placeholder' => t('100 Email Account')
            ]
        ];
        $form['basic']['support'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => t('Time support'),
            '#default_value' => $this->configuration['basic']['support'],
            '#attributes' => [
                'placeholder' => t('24/24 Support')
            ]
        ];

        $form['silver'] = [
            '#type' => 'fieldset',
            '#title' => t('Silver Plan'),
        ];
        $form['silver']['price'] = [
            '#type' => 'textfield',
            '#size' => 5,
            '#title' => t('Price'),
            '#default_value' => $this->configuration['silver']['price'],
            '#attributes' => [
                'placeholder' => t('19')
            ]
        ];
        $form['silver']['currency'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['silver']['currency'],
            '#options' => [
                'usd' => t('USD'),
            ]
        ];
        $form['silver']['time'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['silver']['time'],
            '#options' => [
                'month' => t('/ Month'),
                'year' => t('/ Year')
            ]
        ];

        $form['silver']['disk'] = [
            '#type' => 'textfield',
            '#size' => 5,
            '#title' => t('Disk Space'),
            '#default_value' => $this->configuration['silver']['disk'],
            '#attributes' => [
                'placeholder' => t('1GB')
            ]
        ];
        $form['silver']['size'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['silver']['size'],
            '#options' => [
                'gb' => t('GB'),
                'tb' => t('TB')
            ]
        ];
        $form['silver']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => t('Number email account'),
            '#default_value' => $this->configuration['silver']['email'],
            '#attributes' => [
                'placeholder' => t('100 Email Account')
            ]
        ];
        $form['silver']['support'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => t('Time support'),
            '#default_value' => $this->configuration['silver']['support'],
            '#attributes' => [
                'placeholder' => t('24/24 Support')
            ]
        ];


        $form['gold'] = [
            '#type' => 'fieldset',
            '#title' => t('Gold Plan'),
        ];
        $form['gold']['price'] = [
            '#type' => 'textfield',
            '#size' => 5,
            '#title' => t('Price'),
            '#default_value' => $this->configuration['gold']['price'],
            '#attributes' => [
                'placeholder' => t('39 ')
            ]
        ];
        $form['gold']['currency'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['gold']['currency'],
            '#options' => [
                'usd' => t('USD'),
            ]
        ];
        $form['gold']['time'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['gold']['time'],
            '#options' => [
                'month' => t('/ Month'),
                'year' => t('/ Year')
            ]
        ];

        $form['gold']['disk'] = [
            '#type' => 'textfield',
            '#size' => 5,
            '#title' => t('Disk Space'),
            '#default_value' => $this->configuration['gold']['disk'],
            '#attributes' => [
                'placeholder' => t('1GB')
            ]
        ];
        $form['gold']['size'] = [
            '#type' => 'select',
            '#default_value' => $this->configuration['gold']['size'],
            '#options' => [
                'gb' => t('GB'),
                'tb' => t('TB')
            ]
        ];
        $form['gold']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => t('Number email account'),
            '#default_value' => $this->configuration['gold']['email'],
            '#attributes' => [
                'placeholder' => t('100 Email Account')
            ]
        ];
        $form['gold']['support'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => t('Time support'),
            '#default_value' => $this->configuration['gold']['support'],
            '#attributes' => [
                'placeholder' => t('24/24 Support')
            ]
        ];


        $form['#attached']['library'] = 'price_block/manage';

        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $price_basic = $form_state->getValue('basic');
        $price_silver = $form_state->getValue('silver');
        $price_gold = $form_state->getValue('gold');
        $this->configuration['basic'] = $price_basic;
        $this->configuration['silver'] = $price_silver;
        $this->configuration['gold'] = $price_gold;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $price_basic = $this->configuration['basic'];
        $price_silver = $this->configuration['silver'];
        $price_gold = $this->configuration['gold'];
        $list_price[] = [
            'basic'  => $price_basic,
            'silver' => $price_silver,
            'gold'   => $price_gold
        ];

        $build['cate_price'] = [
            '#markup' => $list_price,
            '#access' => TRUE
        ];

        return $build;
    }

}

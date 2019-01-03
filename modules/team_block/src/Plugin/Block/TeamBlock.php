<?php
namespace Drupal\team_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 *
 * @Block(
 *   id = "team_block",
 *   admin_label = @Translation("Team Block"),
 *   category = @Translation("Custom block")
 * )
 */

class TeamBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'team_block';
    }

    public function blockForm($form, FormStateInterface $form_state)
    {
        parent::blockForm($form, $form_state); // TODO: Change the autogenerated stub

        $arr_terms = $this->getTeams();

        $form['teams'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Teams'),
            '#description' => $this->t('Select the number of teams')
        ];

        $form['teams']['select_number'] = [
            '#type' => 'select',
            '#multiple' => TRUE,
            '#default_value' => '',
            '#options' => $arr_terms,
            '#size' => 10,
        ];

        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state); // TODO: Change the autogenerated stub

        $num_term = $form_state->getValue('teams');
        $this->configuration['teams'] = $num_term['select_number'];
    }


    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $tids = $this->configuration['teams'];
        $arr_teams = [];
        foreach ($tids as $tid) {
            $taxonomy = \Drupal\taxonomy\Entity\Term::load($tid);
            $t_name = $taxonomy->getName();
            $t_image =  file_url_transform_relative(file_create_url($taxonomy->field_images->entity->getFileUri()));
            $t_facebook = $taxonomy->field_facebook->uri;
            $t_twitter = $taxonomy->field_twitter->uri;
            $t_google = $taxonomy->field_google_plus->uri;
            $t_desc = $taxonomy->getDescription();
            $arr_teams[$tid] = [
                'url' => 'taxonomy/term/' . $tid,
                'name' => $t_name,
                'desc' => $t_desc,
                'image' => $t_image,
                'facebook'  => $t_facebook,
                'twitter'  => $t_twitter,
                'google'  => $t_google,
            ];
        }

        $build['teams'] =  array(
            '#markup' => $arr_teams,
            '#access' => TRUE
        );
        return $build;
    }

    public function getTeams()
    {
        $query = \Drupal::entityQuery('taxonomy_term')->condition('status', 1)->condition('vid', 'teams');
        $tids = $query->execute();
        $arr_term_ops = [];
        foreach ($tids as $tid) {
            $taxonomy = \Drupal\taxonomy\Entity\Term::load($tid);
            $t_name = $taxonomy->getName();
            $arr_term_ops[$tid] = $t_name;
        }
        return $arr_term_ops;
    }

}

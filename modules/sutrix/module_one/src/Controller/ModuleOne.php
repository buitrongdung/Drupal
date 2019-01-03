<?php
namespace Drupal\module_one\Controller;

class ModuleOne
{
    public function index()
    {
        $content_type = '';
        if (isset($_GET['content_type'])) {
            $content_type = $_GET['content_type'];
        }
        $list_content = $this->getContentByType($content_type);
        $form = \Drupal::formBuilder()->getForm('Drupal\module_one\Form\ContentTypeForm');
        $form['form_id']['#access'] = FALSE;
        $form['form_build_id']['#access'] = FALSE;
        $form['form_token']['#access'] = FALSE;
        $form['op']['#access'] = FALSE;

        $build = [
            '#theme' => 'module_one',
            '#form' => $form,
            '#list_content' => $list_content
        ];
        return $build;
    }

    public function getContentByType($type = NULL)
    {
        if (!empty($type) || 0 < $type) {
            $query = \Drupal::entityQuery('node')
                ->condition('status', 1)
                ->condition('type', $type);
        } else {
            $query = \Drupal::entityQuery('node')
                ->condition('status', 1);
        }
        $nids = $query->execute();

        $arr_content = [];
        foreach($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $id = $node->nid->value;
            $title = $node->title->value;
            $summary = $node->body->summary;
            $arr_content[$nid] = [
                'url' => '/node/' . $id,
                'title' => $title,
                'summary' => $summary,
            ];
        }

        return $arr_content;

    }

}
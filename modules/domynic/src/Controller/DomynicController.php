<?php
/**
 * Created by PhpStorm.
 * User: dung.bt
 * Date: 28/11/2018
 * Time: 16:50
 */
namespace Drupal\domynic\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DomynicController extends ControllerBase
{
    public static $content_type = 'domynic';
    public static function showListNode()
    {
        $list_node = self::getListNodeByDomynic();
        $render = [
            '#theme' => 'domynic_view',
            '#nodes' => $list_node
        ];

        return $render;
    }

    public static function getNodeBySlug($node_slug)
    {
        $query = \Drupal::entityQuery('node');
        $query->condition('status',1);
        $query->condition('type',self::$content_type);
        $query->condition('field_slug', $node_slug);
        $nid = $query->execute();
        if (!$nid) return false;
        return $nid;
    }

    public static function detailNode($node_slug)
    {
//       if (!is_numeric($node_id) || $node_id == 0) {
//           throw new AccessDeniedHttpException();
//       }
        $nid = self::getNodeBySlug($node_slug);
        foreach ($nid as $id) {
            $node = self::getDetailNode($id);
        }

       $render = [
            '#theme' => 'domynic_view_detail',
            '#node'  => $node
       ];
       return $render;
    }

    public static function getListNodeByDomynic()
    {
        $query = \Drupal::entityQuery('node');
        $query->condition('status',1);
        $query->condition('type',self::$content_type);
        $nids = $query->execute();
        $nodes = [];
        foreach ($nids as $nid) {
            $node = \Drupal\node\Entity\Node::load($nid);
            $id     = $node->field_slug->value;
            $name   = $node->title->value;
            $birth  = date('F j, Y', strtotime($node->field_birth->value));
            $alt    = $node->field_image->alt;
            $image  = file_url_transform_relative(file_create_url($node->field_image->entity->getFileUri()));
            $url    = '/domynic/' . $id;
            $nodes[$id] = [
                'name'   => $name,
                'birth'  => $birth,
                'image'  => $image,
                'url'    => $url,
                'alt'    => $alt,
            ];
        }

        return $nodes;
    }

    public static function getDetailNode($nid)
    {
        $nodes = [];
        $node = \Drupal\node\Entity\Node::load($nid);
        $name   = $node->title->value;
        $birth  = date('F j, Y', strtotime($node->field_birth->value));
        $hobies = strip_tags($node->field_hobies->value);
        $alt    = $node->field_image->alt;
        $gender = strip_tags($node->field_gender->value);
        $image  = file_url_transform_relative(file_create_url($node->field_image->entity->getFileUri()));
        $nodes[] = [
            'name'   => $name,
            'birth'  => $birth,
            'image'  => $image,
            'hobies' => $hobies,
            'gender'  => $gender,
            'alt'    => $alt,
        ];
        return $nodes[0];
    }

    public static function sanitizeTitle($string) {
        if(!$string) return false;
        $utf8 = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd'=>'đ|Đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i'=>'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($utf8 as $ascii=>$uni) {
            $string = preg_replace("/($uni)/i",$ascii,$string);
        }
        $string = self::utf8Url($string);
        return $string;
    }

    public static function utf8Url($string){
        $string = strtolower($string);
        $string = str_replace( "ß", "ss", $string);
        $string = str_replace( "%", "", $string);
        $string = preg_replace("/[^_a-zA-Z0-9 -]/", "",$string);
        $string = str_replace(array('%20', ' '), '-', $string);
        $string = str_replace("----","-",$string);
        $string = str_replace("---","-",$string);
        $string = str_replace("--","-",$string);
        return $string;
    }
}
<?php
namespace Drupal\taxonomy_import\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class EventSubscriber implements EventSubscriberInterface {

    protected $base;
    protected $path;
    protected $account;
    protected $data;

    public function __construct() {
        global $base_url;
        $this->base = $base_url;
        $this->account = \Drupal::currentUser();
        $this->path = \Drupal::service('path.current')->getPath();
    }

    public function importFinish(GetResponseEvent $event) {

    }

    public static function getSubscribedEvents() {
        // TODO: Implement getSubscribedEvents() method.
    }
}
<?php
namespace Indeed\Uap\Migration;

class BaseMigration
{
    public function __construct(){}

    public function run($params=array())
    {
        if (empty($params['serviceType'])){
            return false;
        }

        switch ($params['serviceType']){
            case 'affiliate-wp':
              $object = new \Indeed\Uap\Migration\AffiliateWp($params);
              break;
            case 'wp-affiliate':
              $object = new \Indeed\Uap\Migration\WpAffiliates($params);
              break;
            case 'affiliates-pro':
              $object = new \Indeed\Uap\Migration\AffiliatesPro($params);
              break;
        }
        if (empty($object)){
            return false;
        }

        $object->initJobs()
               ->run()
               ->updateOffset()
               ->updateEntity()
               ->updateLog()
               ->redirect();
    }

}

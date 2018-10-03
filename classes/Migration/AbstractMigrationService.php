<?php
namespace Indeed\Uap\Migration;

abstract class AbstractMigrationService
{
    protected $serviceType = '';
    protected $entityType = '';
    protected $offset = 0;
    protected $limit = 100;
    protected $assignRank = false;
    protected $wpOptionName = 'uap_do_migrate_log';
    protected $entitiesList = array('affiliates');
    protected $logData = array();
    protected $stop = false;

    public function __construct($attr=array())
    {
        $this->serviceType = $attr['serviceType'];
        $this->entityType = $attr['entityType'];
        $this->offset = $attr['offset'];
        $this->assignRank = $attr['assignRank'];
    }

    abstract public function run();
    abstract protected function countAffiliates();
    abstract protected function countReferrals();

    public function initJobs()
    {
        $entireLog = get_option($this->wpOptionName);
        $this->logData = isset($entireLog[$this->serviceType]) ? $entireLog[$this->serviceType] : false;
        if (empty($this->logData) || $this->logData=='completed'){
            $this->logData = array(
                'affiliates-offset' => 0,
                'referrals-offset' => 0,
                'affiliates-count' => $this->countAffiliates(),
                'referrals-count' => $this->countReferrals(),
            );
            $entireLog[$this->serviceType] = $this->logData;
            update_option($this->wpOptionName, $entireLog);
        }
        if (empty($this->entityType)){
            $this->entityType = $this->entitiesList[0];
        }
        return $this;
    }

    public function updateOffset()
    {
        $this->offset = $this->offset + $this->limit;
        $this->logData[$this->entityType . '-offset'] = $this->offset;
        if ($this->logData[$this->entityType . '-offset']>=$this->logData[$this->entityType . '-count']){
            $this->logData[$this->entityType . '-offset'] = $this->logData[$this->entityType . '-count'];
            $this->offset = 0;
        }
        return $this;
    }

    public function updateEntity()
    {
        if (!isset($this->logData[$this->entityType . '-offset'])){
            return $this;
        }
        if ($this->logData[$this->entityType . '-offset']<$this->logData[$this->entityType . '-count']){
            return $this;
        }
        $key = array_search($this->entityType , $this->entitiesList);
        $key++;
        if (empty($this->entitiesList[$key])){
            $this->stop = true;
            return $this;
        }
        /// change the entity type
        $this->entityType = $this->entitiesList[$key];
        return $this;
    }

    public function updateLog()
    {
        $entireLog = get_option($this->wpOptionName);
        if ($this->stop){
            $entireLog[$this->serviceType] = 'completed';
        } else {
            $entireLog[$this->serviceType] = $this->logData;
        }
        update_option($this->wpOptionName, $entireLog);
        return $this;
    }

    public function redirect()
    {
        if ($this->stop==true){
            return true;
        }
        $target = admin_url('admin.php?uap_act=migrate'
                            . '&service_type=' . $this->serviceType
                            . '&entity_type=' . $this->entityType
                            . '&offset=' . $this->offset
                            . '&assignRank=' . $this->assignRank
        );
        wp_redirect($target);
        exit;
    }

}

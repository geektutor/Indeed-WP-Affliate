<?php
namespace Indeed\Uap\Migration;

class WpAffiliates extends \Indeed\Uap\Migration\AbstractMigrationService
{
    protected $entitiesList = array('affiliates');

    public function run()
    {
        $this->proceedAffiliates();
        return $this;
    }

    private function proceedAffiliates()
    {
        global $wpdb, $indeed_db;
        $query = "
                  SELECT b.ID FROM {$wpdb->prefix}affiliates_tbl a
                        INNER JOIN {$wpdb->users} b
                        ON a.email=b.user_email
                        ORDER BY b.ID DESC
                        LIMIT {$this->offset}, {$this->limit}
        ";
        $data = $wpdb->get_results($query);
        if (!$data){
            return;
        }
        $default_rank = empty($this->assignRank) ? get_option('uap_register_new_user_rank') : $this->assignRank;
        foreach ($data as $object){
            $inserted = $indeed_db->save_affiliate($object->ID);
            if ($inserted){
              $indeed_db->update_affiliate_rank_by_uid($object->ID, $default_rank);
            }
        }
    }

    protected function proceedReferrals()
    {
        return ;
    }

    protected function countAffiliates()
    {
        global $wpdb;
        $query = "SELECT IFNULL(COUNT(*), 0) FROM {$wpdb->prefix}affiliates_tbl";
        return $wpdb->get_var($query);
    }

    protected function countReferrals()
    {
        return 0;
    }

}

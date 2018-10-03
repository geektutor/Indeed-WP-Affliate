<?php
namespace Indeed\Uap\Migration;

class AffiliatesPro extends \Indeed\Uap\Migration\AbstractMigrationService
{
    protected $entitiesList = array('affiliates', 'referrals');

    public function run()
    {
        switch ($this->entityType){
            case 'affiliates':
              $this->proceedAffiliates();
              break;
            case 'referrals':
              $this->proceedReferrals();
              break;
        }
        return $this;
    }

    private function proceedAffiliates()
    {
        global $wpdb, $indeed_db;
        $query = "
                  SELECT b.user_id FROM {$wpdb->prefix}aff_affiliates a
                        INNER JOIN {$wpdb->prefix}aff_affiliates_users b
                        ON a.affiliate_id=b.affiliate_id
                        ORDER BY a.affiliate_id DESC
                        LIMIT {$this->offset}, {$this->limit}
        ";
        $data = $wpdb->get_results($query);
        if (!$data){
            return;
        }

        $default_rank = empty($this->assignRank) ? get_option('uap_register_new_user_rank') : $this->assignRank;
        foreach ($data as $object){
            $inserted = $indeed_db->save_affiliate($object->user_id);
            if ($inserted){
              $indeed_db->update_affiliate_rank_by_uid($uid, $default_rank);
            }
        }
    }

    private function proceedReferrals()
    {
        global $wpdb, $indeed_db;
        $query = "
                    SELECT a.*, c.id as new_affiliate_id
                        FROM {$wpdb->prefix}aff_referrals a
                        INNER JOIN {$wpdb->prefix}aff_affiliates_users b
                        ON a.affiliate_id=b.affiliate_id
                        INNER JOIN {$wpdb->prefix}uap_affiliates c
                        ON b.user_id=c.uid
                    LIMIT {$this->offset}, {$this->limit}";
        $data = $wpdb->get_results($query);
        if (!$data){
            return;
        }
        foreach ($data as $object){
            $status = 0;
            if ($object->status=='accepted'){
                $status = 2;
            } else if ($object->status=='pending'){
                $status = 1;
            }
            $insertData = array(
              'refferal_wp_uid'     => '',
              'campaign'            => '',
              'affiliate_id'        => $object->new_affiliate_id,
              'visit_id'            => '',
              'description'         => $object->description,
              'source'              => '',
              'reference'           => $object->reference,
              'parent_referral_id'  => '',
              'child_referral_id'   => '',
              'amount'              => $object->amount,
              'currency'            => strtoupper($object->currency_id),
              'date'                => $object->datetime,
              'status'              => $status,
              'payment'             => '',
            );
            $indeed_db->save_referral($insertData);
        }
    }

    protected function countAffiliates()
    {
        global $wpdb;
        $query = "SELECT IFNULL(COUNT(*), 0) FROM {$wpdb->prefix}aff_affiliates";
        return $wpdb->get_var($query);
    }

    protected function countReferrals()
    {
        global $wpdb;
        $query = "SELECT IFNULL(COUNT(*), 0) FROM {$wpdb->prefix}aff_referrals";
        return $wpdb->get_var($query);
    }

}

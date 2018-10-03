<?php
namespace Indeed\Uap;

class CPM
{
    private $affiliateId = 0;
    private $limit = 1000;

    public function __construct($affiliateId=0)
    {
        global $indeed_db;
        if (!$indeed_db->is_magic_feat_enable('cpm_commission')){
            return;
        }
        $this->affiliateId = $affiliateId;
        $this->trackTheVisit();
    }

    private function trackTheVisit()
    {
        $db = new \Indeed\Uap\Db\CPM($this->affiliateId);
        $theCount = $db->save();
        if (!$theCount){
            return false;
        }
        if ($theCount==$this->limit){
            $this->saveReferral();
            $db->reset();
        }
    }

    private function saveReferral()
    {
        global $indeed_db;
        $affiliateRank = $indeed_db->get_affiliate_rank($this->affiliateId);
        $amountValue = $indeed_db->getCPMValueForRank($affiliateRank);
        $currency = get_option('uap_currency');
        $referralStatus = get_option('uap_pay_per_click_default_referral_sts');

        $args = [
            'refferal_wp_uid' => 0,
            'campaign' => '',
            'affiliate_id' => $this->affiliateId,
            'visit_id' => '',
            'description' => 'cpm',
            'source' => 'cpm',
            'reference' => 0,
            'reference_details' => 'cpm',
            'amount' => $amountValue,
            'currency' => $currency,
            'date' => date('Y-m-d H:i:s', time()),
            'status' => $referralStatus,
            'payment' => 0,
            'parent_referral_id' => '',
            'child_referral_id' => '',
        ];
        return $indeed_db->save_referral($args);
    }



}

<?php
namespace Indeed\Uap;

class Uap_GDPR
{
  private $pluginName = 'Ultimate Affiliate Pro';
  private $uid = 0;
  private $affiliateId = 0;

  public function __construct()
  {
      add_action('admin_init', array($this, 'privacyPolicy'));
      add_filter('wp_privacy_personal_data_exporters', array($this, 'registerExport') );
      add_filter('wp_privacy_personal_data_erasers', array($this, 'registerErase') );
  }

  public function privacyPolicy()
  {
      if (!function_exists('wp_add_privacy_policy_content')) return;
      $policyText = '';
      wp_add_privacy_policy_content($this->pluginName, $policyText);
  }

  public function registerExport($exporters=array())
  {
      $exporters['uap-exporter'] = array(
          'exporter_friendly_name'	=> $this->pluginName,
          'callback'		          	=> array($this, 'doExport'),
      );
      return $exporters;
  }

  public function registerErase($erasers=array())
  {
      $erasers['uap-eraser'] = array(
          'eraser_friendly_name'    => $this->pluginName,
          'callback'                => array($this, 'doErase'),
      );

      return $erasers;
  }

    /*
      Export data from the following tables:
          uap_affiliates
          uap_offers_affiliates_reference
          uap_campaigns
          uap_payments
          uap_ranks_history
          uap_coupons_code_affiliates
          uap_reports
          uap_ref_links
    */
    public function doExport($emailAddress='', $page=1)
    {
      global $indeed_db;
      $user = get_user_by( 'email', $emailAddress );
      $this->uid = isset($user->ID) ? $user->ID : 0;
      $this->affiliateId = $indeed_db->get_affiliate_id_by_wpuid($this->uid);
      $done = false;

      if (empty($this->uid) || empty($this->affiliateId)){
          return array(
            'data' => array(),
            'done' => 0
          );
      }

      $userMetas = $this->getUserMetas();
      if ($userMetas && count($userMetas)){
          $done = true;
      }
      $affiliateTableData = $this->getAffiliateTableData();
      if ($affiliateTableData && count($affiliateTableData)){
          $done = true;
      }
      $offers = $this->getOffers();
      if ($offers && count($offers)){
          $done = true;
      }
      $campaigns = $this->getCampaigns();
      if ($campaigns && count($campaigns)){
          $done = true;
      }
      $payments = $this->getPayments();
      if ($payments && count($payments)){
          $done = true;
      }
      $rankHistory = $this->getRanksHistory();
      if ($rankHistory && count($rankHistory)){
          $done = true;
      }
      $coupons = $this->getCouponsCode();
      if ($coupons && count($coupons)){
          $done = true;
      }
      $reports = $this->getReports();
      if ($reports && count($reports)){
          $done = true;
      }
      $refLinks = $this->getRefLinks();
      if ($refLinks && count($refLinks)){
          $done = true;
      }

      $exportData = array(
          $userMetas,
          $affiliateTableData,
          $offers,
          $campaigns,
          $payments,
          $rankHistory,
          $coupons,
          $refLinks,
      );

      return array(
        'data' => $exportData,
        'done' => 1
      );
    }

    /*
      Delete data from the following tables:
			    usermeta (all uap usermetas)
          uap_affiliates
          uap_offers_affiliates_reference
          uap_campaigns
          uap_payments
          uap_ranks_history
          uap_coupons_code_affiliates
          uap_reports
          uap_ref_links
    */
  public function doErase($emailAddress='', $page=1)
  {
      global $indeed_db, $wpdb;
      $user = get_user_by( 'email', $emailAddress );
      $this->uid = isset($user->ID) ? $user->ID : 0;
      $this->affiliateId = $indeed_db->get_affiliate_id_by_wpuid($this->uid);
      $done = false;

      if (empty($this->uid) || empty($this->affiliateId)){
          return array(
            'data' => array(),
            'done' => 0
          );
      }

      /// usermeta
      $wpdb->query("DELETE FROM {$wpdb->usermeta}
                          WHERE
                          user_id={$this->uid}
                          AND
                          meta_key LIKE '%uap_%' ");
      /// uap_affiliates
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_affiliates WHERE uid={$this->uid} ");
      /// uap_offers_affiliates_reference
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_offers_affiliates_reference WHERE affiliate_id={$this->affiliateId} ");
      /// uap_campaigns
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_campaigns WHERE affiliate_id={$this->affiliateId} ");
      /// uap_payments
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_payments WHERE affiliate_id={$this->affiliateId} ");
      /// uap_ranks_history
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_ranks_history WHERE affiliate_id={$this->affiliateId} ");
      /// uap_coupons_code_affiliates
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_coupons_code_affiliates WHERE affiliate_id={$this->affiliateId} ");
      /// uap_reports
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_reports WHERE affiliate_id={$this->affiliateId} ");
      /// uap_ref_links
      $wpdb->query("DELETE FROM {$wpdb->prefix}uap_ref_links WHERE affiliate_id={$this->affiliateId} ");

      return array(
          				'items_removed' => true,
          				'items_retained' => false,
           				'messages' => array( '' ),
           				'done' => 1,
  		);
  }

  private function getUserMetas()
  {
      global $wpdb;
      $query = "SELECT meta_key, meta_value
                      FROM {$wpdb->usermeta}
                      WHERE
                      meta_key LIKE '%uap_%'
                      AND
                      user_id={$this->uid} ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
          return false;
      }
      $data = array();
      foreach ($dataDb as $object){
        $data[] = array(
              'name'  => $object->meta_key,
              'value' => $object->meta_value,
        );
      }

      return array(
            'group_id'    => 'uap_usermetas',
            'group_label' => __('UAP user metas'),
            'item_id'     => 'uap_usermetas' . $this->uid,
            'data'        => $data,
      );
  }

  private function getAffiliateTableData()
  {
      global $wpdb;
      $query = "
        SELECT 	rank_id, start_data, status
            FROM {$wpdb->prefix}uap_affiliates
            WHERE uid={$this->uid}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
      $data[] = array(
            'name'  => 'Entry',
            'value' => 'Rank Id' . $object->rank_id
                        . ', start time: ' . $object->start_data
                        . ', status: ' . $object->status,
      );
      }
      return array(
          'group_id'    => 'uap_affiliates',
          'group_label' => __('UAP affiliate data'),
          'item_id'     => 'uap_affiliates' . $this->uid,
          'data'        => $data,
      );
  }

  private function getOffers()
  {
      global $wpdb;
      $query = "
        SELECT source, products
            FROM {$wpdb->prefix}uap_offers_affiliates_reference
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
      $data[] = array(
            'name'  => 'Entry',
            'value' => ' source: ' . $object->source
                       . ', products: ' . $object->products,
      );
      }
      return array(
          'group_id'    => 'uap_offers_affiliates_reference',
          'group_label' => __('UAP offers data'),
          'item_id'     => 'uap_offers_affiliates_reference' . $this->uid,
          'data'        => $data,
      );
  }

  private function getCampaigns()
  {
      global $wpdb;
      $query = "
        SELECT name, referrals, visit_count, unique_visits_count
            FROM {$wpdb->prefix}uap_campaigns
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
        $data[] = array(
              'name'  => 'Entry',
              'value' => 'Name: ' . $object->name
                         . ', referrals: ' . $object->referrals
                         . ', visit count: ' . $object->visit_count
                         . ', unique visits count: ' . $object->unique_visits_count
        );
      }
      return array(
          'group_id'    => 'uap_campaigns',
          'group_label' => __('UAP campaigns data'),
          'item_id'     => 'uap_campaigns' . $this->uid,
          'data'        => $data,
      );
  }

  private function getPayments()
  {
      global $wpdb;
      $query = "
        SELECT 	payment_type,	transaction_id,	amount,
        				currency,	payment_details, create_date,	update_date,
        				status
            FROM {$wpdb->prefix}uap_payments
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
      $data[] = array(
            'name'  => 'Entry',
            'value' => 'Payment type: ' . $object->payment_type
                       . ', transaction id: ' . $object->transaction_id
                       . ', amount: ' . $object->amount
                       . ', currency: ' . $object->currency
                       . ', payment details: ' . $object->payment_details
                       . ', create date: ' . $object->create_date
                       . ', update: ' . $object->update_date
                       . ', status: ' . $object->status
      );
      }
      return array(
          'group_id'    => 'uap_payments',
          'group_label' => __('UAP payments data'),
          'item_id'     => 'uap_payments' . $this->uid,
          'data'        => $data,
      );
  }

  private function getRanksHistory()
  {
      global $wpdb;
      $query = "
        SELECT prev_rank_id, rank_id,	add_date
            FROM {$wpdb->prefix}uap_ranks_history
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
          $data[] = array(
                'name'  => 'Entry',
                'value' => 'Previous rank id: ' . $object->prev_rank_id
                           . ', rank id: ' . $object->rank_id
                           . ', date: ' . $object->add_date
          );
      }
      return array(
          'group_id'    => 'uap_ranks_history',
          'group_label' => __('UAP rank history'),
          'item_id'     => 'uap_ranks_history' . $this->uid,
          'data'        => $data,
      );
  }

  private function getCouponsCode()
  {
      global $wpdb;
      $query = "
        SELECT code, status
            FROM {$wpdb->prefix}uap_coupons_code_affiliates
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
          $data[] = array(
                'name'  => 'Entry',
                'value' => 'Code: ' . $object->prev_rank_id
                           . ', status: ' . $object->status
          );
      }
      return array(
          'group_id'    => 'uap_coupons_code_affiliates',
          'group_label' => __('UAP coupons code', 'uap'),
          'item_id'     => 'uap_coupons_code_affiliates' . $this->uid,
          'data'        => $data,
      );
  }

  private function getReports()
  {
      global $wpdb;
      $query = "
        SELECT email,	period,	last_sent
            FROM {$wpdb->prefix}uap_reports
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
          $data[] = array(
                'name'  => 'Entry',
                'value' => 'E-mail: ' . $object->email
                           . ', period: ' . $object->period
                           . ', last seen: ' . $object->last_sent
          );
      }
      return array(
          'group_id'    => 'uap_reports',
          'group_label' => __('UAP reports', 'uap'),
          'item_id'     => 'uap_reports' . $this->uid,
          'data'        => $data,
      );
  }

  private function getRefLinks()
  {
      global $wpdb;
      $query = "
        SELECT url,	status
            FROM {$wpdb->prefix}uap_ref_links
            WHERE affiliate_id={$this->affiliateId}
      ";
      $dataDb = $wpdb->get_results($query);
      if (!$dataDb){
        return false;
      }
      foreach ($dataDb as $object){
          $data[] = array(
                'name'  => 'Entry',
                'value' => 'URL: ' . $object->url
                           . ', status: ' . $object->status
          );
      }
      return array(
          'group_id'    => 'uap_reports',
          'group_label' => __('UAP reports', 'uap'),
          'item_id'     => 'uap_reports' . $this->uid,
          'data'        => $data,
      );
  }


}

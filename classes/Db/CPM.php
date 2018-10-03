<?php
namespace Indeed\Uap\Db;

class CPM
{
    private $affiliateId = 0;

    public function __construct($affiliateId=0)
    {
        $this->affiliateId = $affiliateId;
    }

    public function save()
    {
        global $wpdb;
        if (empty($this->affiliateId)){
            return false;
        }
        $oldValue = $this->get();
        if ($oldValue){
            /// update
            $count = $oldValue->count_number + 1;
            $time = date("Y-m-d H:i:s");
            $query = "UPDATE {$wpdb->prefix}uap_cpm SET count_number=$count, update_time='$time'
                          WHERE affiliate_id={$this->affiliateId} ;";
        } else {
            /// insert
            $count = 1;
            $query = "INSERT INTO {$wpdb->prefix}uap_cpm VALUES(NULL, {$this->affiliateId}, $count, NULL);";
        }
        $inserted = $wpdb->query($query);
        if ($inserted)
            return $count;
        else
            return 0;
    }

    public function reset()
    {
        global $wpdb;
        $time = date("Y-m-d H:i:s");
        $query = "UPDATE {$wpdb->prefix}uap_cpm SET count_number=0, update_time='$time'
                      WHERE affiliate_id={$this->affiliateId} ;";
        return $wpdb->query($query);
    }

    public function get()
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}uap_cpm WHERE affiliate_id={$this->affiliateId}");
    }

    public function delete()
    {
      global $wpdb;
      return $wpdb->query("DELETE FROM {$wpdb->prefix}uap_cpm WHERE affiliate_id={$this->affiliateId}");
    }

}

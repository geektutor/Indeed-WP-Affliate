<?php
namespace Indeed\Uap;

class RestAPI extends \WP_REST_Controller
{

    public function __construct()
    {
        $isOn = get_option('uap_rest_api_enabled');
        if ($isOn){
            return;
        }
        add_action( 'rest_api_init', [$this, 'register_routes'] );
    }

    public function register_routes()
    {
        register_rest_route( 'ultimate-affiliates-pro/v1', 'affiliates', [
                'methods'               => 'GET',
                'callback'              => [$this, 'getAffiliates'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'approve-affiliate/(?P<affiliateId>\d+)', [
                'methods'               => 'POST',
                'callback'              => [$this, 'approveAffiliate'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'update-affiliate-rank/(?P<affiliateId>\d+)/(?P<rankId>\d+)', [
                'methods'               => 'POST',
                'callback'              => [$this, 'updateAffiliateRank'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'get-user-data/(?P<affiliateId>\d+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'getAllUserData'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'get-user-field-value/(?P<affiliateId>\d+)/(?P<fieldName>[a-z0-9_.\-]+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'getUserFieldValue'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'get-affiliate-rank/(?P<affiliateId>\d+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'getAffiliateRank'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'get-affiliate-rank-details/(?P<affiliateId>\d+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'getAffiliateRankDetails'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'search-affiliate/(?P<search>[a-z0-9 .\-]+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'searchAffiliate'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'list-ranks', [
                'methods'               => 'GET',
                'callback'              => [$this, 'listRanks'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'list-affiliates-by-rank/(?P<rankId>\d+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'getAffiliatesByRank'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'make-user-affiliate/(?P<userId>\d+)', [
                'methods'               => 'PUT',
                'callback'              => [$this, 'makeUserAffiliate'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'add-referral', [
                'methods'               => 'PUT',
                'callback'              => [$this, 'createReferral'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'list-referrals', [
                'methods'               => 'GET',
                'callback'              => [$this, 'listReferrals'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);

        register_rest_route( 'ultimate-affiliates-pro/v1', 'list-referrals/(?P<affiliateId>\d+)', [
                'methods'               => 'GET',
                'callback'              => [$this, 'listReferrals'],
                'permission_callback'   => [$this, 'permissions'],
                'args'                  => $this->get_collection_params(),
        ]);


    }

    public function permissions($request) {
    		if ( ! empty( $request['roles'] ) && ! current_user_can( 'administrator' ) ) {
            return false;
    		}
    		return true;
    }

    public function getAffiliates(\WP_REST_Request $request)
    {
        global $indeed_db;
        $page = isset($request['page']) ? $request['page'] : 1;
        $limit = isset($request['limit']) ? $request['limit'] : 30;
        $offset = ($page - 1) * $limit;
        $data = $indeed_db->get_affiliates($limit, $offset);
        return json_encode( $data );
    }

    public function approveAffiliate(\WP_REST_Request $request)
    {
        global $indeed_db;
        if (isset($request['affiliateId'])){
            $indeed_db->doApproveAffiliate($request['affiliateId']);
            $success = true;
        }
        return json_encode( [
            'success' => $success
        ] );
    }

    public function updateAffiliateRank(\WP_REST_Request $request)
    {
        global $indeed_db;
        $success = false;
        if (isset($request['affiliateId']) && isset($request['rankId'])){
            $indeed_db->update_affiliate_rank($request['affiliateId'], $request['rankId']);
            $success = true;
        }
        return json_encode( [
            'success' => $success
        ] );
    }

    public function getAllUserData(\WP_REST_Request $request)
    {
        global $indeed_db;
        $userData = '';
        if (isset($request['affiliateId'])){
            $uid = $indeed_db->get_uid_by_affiliate_id($request['affiliateId']);
            $userData = $indeed_db->getAllUserData($uid);
        }
        return json_encode( $userData );
    }

    public function getUserFieldValue(\WP_REST_Request $request)
    {
        global $indeed_db;
        $metaValue = '';
        if (isset($request['affiliateId']) && isset($request['fieldName'])){
            $uid = $indeed_db->get_uid_by_affiliate_id($request['affiliateId']);
            $metaValue = $indeed_db->getUserMetaValue($uid, $request['fieldName']);
        }
        return json_encode( [
            'response' => $metaValue
        ] );
    }


    public function getAffiliateRank(\WP_REST_Request $request)
    {
        global $indeed_db;
        $rankId = 0;
        if (isset($request['affiliateId'])){
            $rankId = $indeed_db->get_affiliate_rank($request['affiliateId']);
        }
        return json_encode( [
            'rank_id' => $rankId
        ] );
    }


    public function getAffiliateRankDetails(\WP_REST_Request $request)
    {
        global $indeed_db;
        $rankData = [];
        if (isset($request['affiliateId'])){
            $rankId = $indeed_db->get_affiliate_rank($request['affiliateId']);
            $rankData = $indeed_db->get_rank($rankId);
        }
        return json_encode($rankData);
    }

    public function searchAffiliate(\WP_REST_Request $request)
    {
        global $indeed_db;
        $affiliates = [];
        if (isset($request['search'])){
            $affiliates = $indeed_db->search_affiliates_by_char($request['search']);
        }
        return json_encode( $affiliates );
    }

    public function listRanks(\WP_REST_Request $request)
    {
        global $indeed_db;
        $ranksObject = $indeed_db->get_ranks();
        if (empty($ranksObject)){
            return json_encode([]);
        }
        $return = [];
        foreach ($ranksObject as $object){
            $return[] = (array)$object;
        }
        return json_encode($return);
    }

    public function getAffiliatesByRank(\WP_REST_Request $request)
    {
        global $indeed_db;
        $affiliates = [];
        if (isset($request['rankId'])){
            $affiliates = $indeed_db->get_affiliates(-1, -1, FALSE, '', '', [], $request['rankId']);
        }
        return json_encode( $affiliates );
    }

    public function makeUserAffiliate(\WP_REST_Request $request)
    {
        global $indeed_db;
        $success = false;
        if (isset($request['userId'])){
          $inserted = $indeed_db->save_affiliate($request['userId']);
          if ($inserted){
            /// put default rank on this new affiliate
            $default_rank = get_option('uap_register_new_user_rank');
            $indeed_db->update_affiliate_rank_by_uid($request['userId'], $default_rank);
            $success = true;
          }
        }
        return json_encode( [
            'success' => $success
        ] );
    }


    public function listReferrals(\WP_REST_Request $request)
    {
        global $indeed_db;
        $condition = '';
        if (!empty($request['affiliateId'])){
            $condition = "r.affiliate_id=" . esc_sql($request['affiliateId']);
        }
        $page = isset($request['page']) ? $request['page'] : 1;
        $limit = isset($request['limit']) ? $request['limit'] : 30;
        $offset = ($page - 1) * $limit;

        $data = $indeed_db->get_referrals($limit, $offset, FALSE, '', '', $condition);
        return json_encode( $data );
    }

    public function createReferral(\WP_REST_Request $request)
    {
        global $indeed_db;
        $success = false;
        $data = $request->get_body();
        $data = stripslashes($data);
        if (empty($data)){
          return json_encode( [
              'success' => $success
          ] );
        }
        $referralData = json_decode($data, true);
        if (empty($referralData)){
          return json_encode( [
              'success' => $success
          ] );
        }
        $response = $indeed_db->save_referral($referralData);
        if ($response){
            $success = true;
        }
        return json_encode( [
            'success' => $success
        ] );
    }

}

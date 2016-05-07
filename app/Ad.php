<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Cache;

class Ad extends Model
{
    protected $table = 'ad';
    protected $primaryKey = 'ad_id';
    
    protected $fillable = ['ad_id', 'user_id', 'category_id', 'location_id', 'type_id', 'condition_id', 'ad_email', 
    'ad_publish_date', 'ad_valid_until', 'ad_active', 'ad_ip', 'ad_price', 'ad_free', 'ad_phone', 'ad_title', 'ad_description', 
    'ad_description_hash', 'ad_puslisher_name', 'code', 'ad_promo', 'ad_promo_until', 'ad_link', 'ad_video', 'ad_lat_lng', 
    'ad_skype', 'ad_address', 'ad_pic', 'ad_view', 'estate_type_id', 'estate_sq_m', 'estate_year', 'estate_construction_type_id', 
    'estate_floor', 'estate_num_floors_in_building', 'estate_heating_type_id', 'estate_furnishing_type_id', 'car_brand_id', 
    'car_model_id', 'car_engine_id', 'car_transmission_id', 'car_modification_id', 'car_condition_id', 'car_year', 'car_kilometeres', 'created_at', 'updated_at'];
    
    //used for $fillable generation
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    /*
     * get the user for this ad
     */
    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id', 'user_id');
    }
    
    public function pics()
    {
        return $this->hasMany('App\AdPic', 'ad_id', 'ad_id');
    }
    
    public function getAdList($_where = array(), $_order = array(), $_limit = 0, $_order_raw = '')
    {
        $cache_key = __CLASS__ . '_' . __LINE__ . '_' . md5(config('dc.site_name') . serialize(func_get_args()));
        $ret = Cache::get($cache_key, new Collection());
        if($ret->isEmpty()){
            $q = $this->newQuery();
            
            $q->select('ad.ad_id', 'ad.ad_title', 'ad.ad_pic', 'ad.ad_price', 'ad.ad_free', 'ad.ad_promo', 'L.location_name');
            
            if(!empty($_where)){
                foreach ($_where as $k => $v){
                    $q->where($k, $v);
                }
            }
            
            if(!empty($_order)){
                foreach($_order as $k => $v){
                    $q->orderBy($k, $v);
                }
            }
            
            if(!empty($_order_raw)){
                $q->orderByRaw($_order_raw);
            }
            
            if($_limit > 0){
                $q->take($_limit);
            }
            
            $q->leftJoin('location AS L', 'L.location_id' , '=', 'ad.location_id');
            
            $res = $q->get();
            if(!$res->isEmpty()){
                $ret = $res;
                Cache::put($cache_key, $ret, config('dc.cache_expire'));
            }
        }
        return $ret;
    }
}

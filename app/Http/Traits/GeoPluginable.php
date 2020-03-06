<?php
namespace App\Http\Traits;
trait GeoPluginable{
    public $geoPluginURL;
    public $addrDetailsArr;       
    public function addressDetailsFromIP($ip){     
        $this->geoPluginURL = config('app.geo_plugin').'?ip='.$ip;
        $this->addrDetailsArr = unserialize(file_get_contents($this->geoPluginURL));
        return $this->addrDetailsArr;
    }
    public function addressDetailsFromLatLon($lat,$lon){
        $this->geoPluginURL = config('app.geo_plugin').'?lat='.$lat.'&lon='.$lon;
        $this->addrDetailsArr = unserialize(file_get_contents($this->geoPluginURL));
        return $this->addrDetailsArr;
    }
    public function  addressDetailsFromCountryCity($countryName,$cityName){
        $this->geoPluginURL = config('app.geo_plugin').'?geoplugin_countryName='.$countryName.'&geoplugin_city='.$cityName;
        $this->addrDetailsArr = unserialize(file_get_contents($this->geoPluginURL));
        return $this->addrDetailsArr;
    }
}

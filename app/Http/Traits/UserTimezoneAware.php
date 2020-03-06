<?php
namespace App\Http\Traits;
use Auth;
use Carbon\Carbon;
trait UserTimezoneAware{
    public function getDateTime($value) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s',$value);
        $date->setTimezone(Auth::check() ? Auth::user()->timezone: config('app.timezone'));
        return $date->toDateTimeString();
    }
    public function getTime($value) {
        $date = Carbon::createFromFormat('H:i:s',$value);
        $date->setTimezone(Auth::check() ? Auth::user()->timezone: config('app.timezone'));
        return $date->toTimeString();
    }
    public function getDate($value) {
        $date = Carbon::createFromFormat('Y-m-d',$value);
        $date->setTimezone(Auth::check() ? Auth::user()->timezone: config('app.timezone'));
        return $date->toDateString();
    }
    public function setDateTime($value) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s',$value);
        $date->setTimezone(Auth::check() ? config('constants.SERVER_TIME_ZONE'): config('app.timezone'));
        return $date->toDateTimeString();
    }
    public function setTime($value) {
        $date = Carbon::createFromFormat('H:i:s',$value);
        $date->setTimezone(Auth::check() ? config('constants.SERVER_TIME_ZONE') : config('app.timezone'));
        return $date->toTimeString();
    }
    public function setDate($value) {
        $date = Carbon::createFromFormat('Y-m-d',$value);
        $date->setTimezone(Auth::check() ? config('constants.SERVER_TIME_ZONE') : config('app.timezone'));
        return $date->toDateString();
    }
}
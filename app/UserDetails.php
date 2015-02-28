<?php namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;


class UserDetails extends Model {
    protected $table = 'user_details';

    public $timestamps = true;

    //
    public static function maxRow(){

        return  UserDetails::where('user_id', Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function User()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}

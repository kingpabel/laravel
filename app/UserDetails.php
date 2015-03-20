<?php namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserDetails extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'user_details';

    public $timestamps = true;

    //
    public static function maxRow(){

        return  UserDetails::where('user_id', Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public static function maxRowToday(){

        return  UserDetails::where('user_id', Auth::user()->id)
            ->where('login_date', date('Y-m-d'))
            ->where('logout_date', '0000-00-00')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function User()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}

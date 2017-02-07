<?php


class Feedback extends Eloquent {
    
	protected $table = 'feedback';
    public $timestamps = false;
    
    protected $guarded = array('id');
    
    
    // Define relationships between this model and others
    
    public static $rules = array(
        'workshop_rating' => 'required',
        'presenter_rating' => 'required'
    );
    
    public static $messages = array(
        'workshop_rating.required' => 'You must rate the workshop.',
        'presenter_rating.required' => 'You must rate the presenter.'
    );
    
    public function workshop()
    {
        return $this->belongsTo('Workshop', 'workshop_id');
    }
    
    public function department()
    {
        return $this->belongsTo('Department', 'department_id');
    }
    
    
    public static function WSAvg($ws_id)
    {
        $WS_rating = Feedback::
            select('workshop_rating')->
            where('workshop_id', '=', $ws_id)->
            whereNotNull('workshop_rating')->
            get();
        
        $sum = 0;
        foreach ($WS_rating as $wsr) {
            $sum += $wsr->workshop_rating;
        }
        if (count($WS_rating) > 0) {
            return number_format($sum / count($WS_rating), 2);
        } else {
            return 0;
        }
    }
    
    
    public static function PresAvg($ws_id)
    {
        $pres_rating = Feedback::
            select('presenter_rating')->
            where('workshop_id', '=', $ws_id)->
            whereNotNull('presenter_rating')->
            get();
        
        $sum = 0;
        foreach ($pres_rating as $pr) {
            $sum += $pr->presenter_rating;
        }
        
        if (count($pres_rating) > 0) {
            return number_format($sum / count($pres_rating), 2);
        } else {
            return 0;
        }
    }
}
<?php


class Presenter extends Eloquent {
    
	protected $table = 'presenters';
    public $timestamps = false;
    
    protected $guarded = array('id');
    
    
    // Define relationships between this model and others
    
    public function feedback()
    {
        return $this->belongsTo('Workshop', 'workshop_id');
    }
    
    public function teachers()
    {
        return $this->belongsTo('Teacher', 'teacher_id');
    }
}
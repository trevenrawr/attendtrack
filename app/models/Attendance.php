<?php


class Attendance extends Eloquent {
    
	protected $table = 'attendance';
    public $timestamps = false;
    
    protected $guarded = array('id');
    
    
    // Define relationships between this model and others
    public function workshop()
    {
        return $this->belongsTo('Workshop', 'workshop_id');
    }
    
    public function teacher()
    {
        return $this->belongsTo('Teacher', 'teacher_id');
    }
}
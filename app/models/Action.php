<?php

class Action extends Eloquent {
    
    protected $table = 'actions';
    public $timestamps = false;
    
    protected $guarded = array('id');
    
    public function getIpAttribute($value)
    {
        return inet_ntop($value);
    }
    
    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = inet_pton($value);
    }
    
    public function teacher()
    {
        return $this->belongsTo('teacher', 'teacher_id');
    }
}
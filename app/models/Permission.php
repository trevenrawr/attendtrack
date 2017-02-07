<?php


class Permission extends Eloquent {
    
	protected $table = 'permissions';
    public $timestamps = false;
    
    protected $guarded = array('id');
    
    
    // Define relationships between this model and others
    public function teachers()
    {
        return $this->belongsToMany('Teacher', 'userpermissions');
    }
}
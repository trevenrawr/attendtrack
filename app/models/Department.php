<?php


class Department extends Eloquent {
    
	protected $table = 'departments';
    public $timestamps = false;
    
    ///////       Validation rules       ///////
    
    public static $rules = array(
        'title' => 'required'
    );
    
    public static $messages = array(
        'title.required' => 'You should really name the department something.'
    );
    
    // Define relationships between this model and others
    
    public function feedback()
    {
        return $this->hasMany('Feedback', 'department_id');
    }
    
    public function teachers()
    {
        return $this->hasMany('Teacher', 'department_id');
    }
}
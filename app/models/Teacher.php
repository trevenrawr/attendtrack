<?php

class Teacher extends Eloquent {
    
    protected $table = 'teachers';
    public $timestamps = false;
    
    protected $guarded = array('id');
    
    public static $rules = array(
        'name' => 'required',
        'email' => 'required|email',
        'department_id' => 'required',
        'program' => 'required',
        'affiliation' => 'required'
    );
    
    public static $messages = array(
        'name.required' => 'A  name is required.',
        'email.required' => 'An email address is requried.',
        'email.email' => 'A valid email is required.',
        'department_id.required' => 'Please choose a department.',
        'year.required' => 'Please specify your year in graduate school.',
        'program.required' => 'You must specify which program you are in.',
        'affiliation.required' => 'You must specify what your position is at the university.'
    );
    
    
    // Define relationships between this model and others
    
    public function department()
    {
        return $this->belongsTo('Department', 'department_id');
    }
    
    public function firstVTCer()
    {
        return $this->belongsTo('Teacher', 'firstVTCer');
    }
    public function firstVTCed()
    {
        return $this->hasMany('Teacher', 'firstVTCer');
    }
    
    public function secondVTCer()
    {
        return $this->belongsTo('Teacher', 'secondVTCer');
    }
    public function secondVTCed()
    {
        return $this->hasMany('Teacher', 'secondVTCer');
    }
    
    public function workshops()
    {
        return $this->belongsToMany('Workshop', 'attendance');
    }
    
    public function attendance()
    {
        return $this->hasMany('Attendance', 'teacher_id');
    }
    
    public function presentations()
    {
        return $this->belongsToMany('Workshop', 'presenters');
    }
    
    public function permissions($permission = 'any')
    {
        $up = DB::table('userpermissions')->select('permission_id')->where('teacher_id', '=', $this->id)->get();
        foreach ($up as $u) $ups[] = $u->permission_id;
        
        if (empty($ups)) return false;
        
        $pms = Permission::all();
        foreach ($pms as $pm) $perms[$pm->id] = $pm->permission;
        
        foreach ($perms as $p_id => $perm) {
            if (in_array($p_id, $ups))
                $p[] = $perm;
        }
        
        if (in_array($permission, $p)) return true;
        elseif ($permission == 'any') return true;
        else return false;
    }
    
    // Returns whether or not the teacher attended the workshop
    public function attended($ws_id)
    {
        $att = Attendance::where('workshop_id', '=', $ws_id)->where('teacher_id', '=', $this->id)->first();
        return !empty($att);
    }
    
    public function scopeProgram($query)
    {
    }
    
    public function scopeCertFilter($query)
    {
        $ds = Session::get('date_start');
        $cert = Session::get('filter_cert');
        $prog = Session::get('filter_prog');
        
        if (empty($cert)) $cert = 'both';
        
        if ($cert == 'both') {
            $query->
                where(function($q) {
                    $q->where('CCT_status', '=', 'certified');

                    if (!empty(Session::get('filter_prog')))
                        $q->where('program', '=', Session::get('filter_prog'));

                    if (!empty(Session::get('date_start'))) {
                        $q->where('CCT_date', '>=', Session::get('date_start'))->
                            where('CCT_date', '<=', Session::get('date_end'));
                    }
                })->
                orWhere(function($q) {
                    $q->where('PDC_status', '=', 'certified');

                    if (!empty(Session::get('filter_prog')))
                        $q->where('program', '=', Session::get('filter_prog'));

                    if (!empty(Session::get('date_start'))) {
                        $q->where('PDC_date', '>=', Session::get('date_start'))->
                            where('PDC_date', '<=', Session::get('date_end'));
                    }
                });
        } else {
            if (!empty($prog)) {
                $query->where('program', '=', $prog);
            }
            if ($cert == 'CCT') {
                if (!empty($ds)) {
                    $query->where($CCT_date);
                } else {
                    $query->where('CCT_status', '=', 'certified');
                }
            } elseif ($cert == 'PDC') {
                if (!empty($ds)) {
                    $query->where($PDC_date);
                } else {
                    $query->where('PDC_status', '=', 'certified');
                }
            }
        }
        
        return $query;
    }
}
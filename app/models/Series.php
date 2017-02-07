<?php


class Series extends Eloquent {
    
    protected $table = 'series';
    public $timestamps = false;
    
    ///////       Validation rules       ///////
    
    public static $rules = array(
        'title' => 'required'
    );
    
    public static $messages = array(
        'title.required' => 'You should really name the department something.'
    );
    
    
    // Define relationships between this model and others
    
    public function workshops()
    {
        return $this->hasMany('Workshop', 'series_id');
    }
    
    public function wsCount() {
        $ws = Workshop::where('series_id', '=', $this->id)->
            wsFilter()->
            get();
        
        return $ws->count();
    }
    
    public function attendance()
    {
        Session::flash('ser_search_id', $this->id);
        
        $att = Attendance::
            join('workshops', function($j) {
                $j->on('workshops.id', '=', 'attendance.workshop_id')->
                    where('workshops.series_id', '=', Session::get('ser_search_id'));
                
                $semsel = Session::get('semsel');
                $dstart = Session::get('date_start');
                $dend = Session::get('date_end');

                // Only do the semester sorting if the start date isn't set.
                if (empty($dstart) && !empty($semsel)) {
                    $semsel = explode(' ', $semsel);
                    $sem = $semsel[0];
                    $year = $semsel[1];
                    
                    $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                } elseif (!empty($dstart)) {
                    $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                }
            })->
            get();
        
        return $att;
    }
    
    public function individuals()
    {
        Session::flash('ser_search_id', $this->id);
        
        $att = Attendance::
            join('workshops', function($j) {
                $j->on('workshops.id', '=', 'attendance.workshop_id')->
                    where('workshops.series_id', '=', Session::get('ser_search_id'));
                
                $semsel = Session::get('semsel');
                $dstart = Session::get('date_start');
                $dend = Session::get('date_end');

                // Only do the semester sorting if the start date isn't set.
                if (empty($dstart) && !empty($semsel)) {
                    $semsel = explode(' ', $semsel);
                    $sem = $semsel[0];
                    $year = $semsel[1];
                    
                    $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                } elseif (!empty($dstart)) {
                    $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                }
            })->
            join('teachers', 'teachers.id', '=', 'attendance.teacher_id')->
            groupBy('teachers.id')->
            orderBy('teachers.name', 'asc')->
            get();
            
        return $att;
    }
    
    
    public function STEMattendance()
    {
        Session::flash('ser_search_id', $this->id);
        
        $att = Attendance::
            join('workshops', function($j) {
                $j->on('workshops.id', '=', 'attendance.workshop_id')->
                    where('workshops.series_id', '=', Session::get('ser_search_id'));
                
                $semsel = Session::get('semsel');
                $dstart = Session::get('date_start');
                $dend = Session::get('date_end');

                // Only do the semester sorting if the start date isn't set.
                if (empty($dstart) && !empty($semsel)) {
                    $semsel = explode(' ', $semsel);
                    $sem = $semsel[0];
                    $year = $semsel[1];
                    
                    $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                } elseif (!empty($dstart)) {
                    $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                }
            })->
            join('departments', function($j) {
                $j->on('departments.id', '=', 'attendance.department_id')->
                    where('departments.STEM', '=', true);
            })->
            get();
        
        return $att;
    }
    
    
    public function STEMindividuals()
    {
        Session::flash('ser_search_id', $this->id);
        
        $att = Attendance::
            join('workshops', function($j) {
                $j->on('workshops.id', '=', 'attendance.workshop_id')->
                    where('workshops.series_id', '=', Session::get('ser_search_id'));
                
                $semsel = Session::get('semsel');
                $dstart = Session::get('date_start');
                $dend = Session::get('date_end');

                // Only do the semester sorting if the start date isn't set.
                if (empty($dstart) && !empty($semsel)) {
                    $semsel = explode(' ', $semsel);
                    $sem = $semsel[0];
                    $year = $semsel[1];
                    
                    $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                } elseif (!empty($dstart)) {
                    $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                }
            })->
            join('departments', function($j) {
                $j->on('departments.id', '=', 'attendance.department_id')->
                    where('departments.STEM', '=', true);
            })->
            join('teachers', 'teachers.id', '=', 'attendance.teacher_id')->
            groupBy('teachers.id')->
            orderBy('teachers.name', 'asc')->
            get();
            
        return $att;
    }
    
    
    public function fbYes()
    {
        Session::flash('ser_search_id', $this->id);
        
        $fb = Feedback::
            where('recommend', '=', 'yes')->
            join('workshops', function($j) {
                $j->on('workshops.id', '=', 'feedback.workshop_id')->
                    where('workshops.series_id', '=', Session::get('ser_search_id'));
                
                $semsel = Session::get('semsel');
                $dstart = Session::get('date_start');
                $dend = Session::get('date_end');

                // Only do the semester sorting if the start date isn't set.
                if (empty($dstart) && !empty($semsel)) {
                    $semsel = explode(' ', $semsel);
                    $sem = $semsel[0];
                    $year = $semsel[1];
                    
                    $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                } elseif (!empty($dstart)) {
                    $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                }
            })->
            get();
            
        return $fb;
    }
    
    public function fbNo()
    {
        Session::flash('ser_search_id', $this->id);
        
        $fb = Feedback::
            where('recommend', '=', 'no')->
            join('workshops', function($j) {
                $j->on('workshops.id', '=', 'feedback.workshop_id')->
                    where('workshops.series_id', '=', Session::get('ser_search_id'));
                
                $semsel = Session::get('semsel');
                $dstart = Session::get('date_start');
                $dend = Session::get('date_end');

                // Only do the semester sorting if the start date isn't set.
                if (empty($dstart) && !empty($semsel)) {
                    $semsel = explode(' ', $semsel);
                    $sem = $semsel[0];
                    $year = $semsel[1];
                    
                    $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                } elseif (!empty($dstart)) {
                    $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                }
            })->
            get();
            
        return $fb;
    }
}
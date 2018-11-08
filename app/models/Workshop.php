<?php

Validator::extend('isTime', function($attribute, $value, $parameters)
{
    if (preg_match('/[0-9]{2}\:[0-9]{2}/', $value)) {
        $time = explode(':', $value);
        $hr = $time[0];
        $min = $time[1];
        if ($min > 59 || $min < 0) return false;
        if ($hr > 24  || $hr < 0) return false;
        return true;
    }
});

class Workshop extends Eloquent {
    
	protected $table = 'workshops';
    public $timestamps = false;
    const NUMPRES = 6;
    
    protected $guarded = array('id');
    
    
    ///////       Validation rules       ///////
    
    public static $rules = array(
        'title' => 'required',
        'date' => 'required|date_format:Y-m-d',
        'time' => 'required|isTime',
        'presenter0' => 'required_without_all:presenter1,presenter2,presenter3,presenter4,presenter5',
        'presenter1' => 'required_without_all:presenter0,presenter2,presenter3,presenter4,presenter5',
        'presenter2' => 'required_without_all:presenter1,presenter0,presenter3,presenter4,presenter5',
        'presenter3' => 'required_without_all:presenter1,presenter2,presenter0,presenter4,presenter5',
        'presenter4' => 'required_without_all:presenter1,presenter2,presenter3,presenter0,presenter5',
        'presenter5' => 'required_without_all:presenter1,presenter2,presenter3,presenter4,presenter0',
        'series_id' => 'required',
        'semester' => 'required'
    );
    
    public static $messages = array(
        'title.required' => 'You should really name the workshop something.',
        'date.required' => 'A workshop must have a date.',
        'date.date_format' => 'Please insert dates in format YYYY-MM-DD.',
        'time.required' => 'When does the workshop start?',
        'time.is_time' => 'Please insert a valid time in the format ##:##',
        'presenter0.required_without_all' => 'You must specify at least one presenter.',
        'presenter1.required_without_all' => 'You must specify at least one presenter.',
        'presenter2.required_without_all' => 'You must specify at least one presenter.',
        'presenter3.required_without_all' => 'You must specify at least one presenter.',
        'presenter4.required_without_all' => 'You must specify at least one presenter.',
        'presenter5.required_without_all' => 'You must specify at least one presenter.',
        'series_id.required' => 'A workshop should not be alone.  What series does it belong to?',
        'semester.required' => 'What semester did this workshop happen during?'
    );
    
    
    // Define relationships between this model and others
    
    public function presenters()
    {
        return $this->hasMany('Presenter', 'workshop_id');
    }
    
    public function series()
    {
        return $this->belongsTo('Series', 'series_id');
    }
    
    public function feedback()
    {
        return $this->hasMany('Feedback', 'workshop_id');
    }
    
    public function attendees()
    {
        return $this->belongsToMany('Teacher', 'attendance');
    }
    
    public function demographics()
    {
        return $this->hasMany('Attendance', 'workshop_id');
    }
    
    public function scopeSeries($query, $series)
    {
        return $query->where('series_id', '=', $series);
    }
    
    public function scopeSemester($query, $semester)
    {
        return $query->where('semester', '=', $semester);
    }
    public function scopeYear($query, $year)
    {
        return $query->where(DB::raw('YEAR(date)'), '=', $year);
    }
    
    public function scopeDateRange($query, $ds, $de)
    {
		if(empty($de)){
			return $query->where('date', '>=', $ds);
		}else{
			return $query->where('date', '>=', $ds)->where('date', '<=', $de);
		}
    }
    
    public function isPresenter($id)
    {
        $pres = Presenter::where('workshop_id', '=', $this->id)->get();
        
        if (empty($pres) || empty($id)) return false;
        
        foreach($pres as $p) {
            if ($p->teacher_id == $id)
                return true;
        }
        return false;
    }
    
    public function scopeSemesterList($query)
    {
        return $query->select(DB::raw('CONCAT(semester, " ", YEAR(date)) AS semsel'))->
            groupBy(DB::raw('YEAR(date)'))->
            groupBy('semester')->
            orderBy(DB::raw('YEAR(date)'), 'desc')->
            orderBy('semester', 'desc');
    }
    
    public function scopeWsFilter($query)
    {
        $semsel = Session::get('semsel');
        $series_id = Session::get('series_id');
        $dstart = Session::get('date_start');
        $dend = Session::get('date_end');
        
        if (!empty($semsel)) {
            $semsel = explode(' ', $semsel);
            $sem = $semsel[0];
            $year = $semsel[1];
        }
        
        // Only do the semester sorting if the start date isn't set.
        if (empty($dstart)) {
            if (!empty($semsel) && !empty($series_id)) {
                return $query->series($series_id)->semester($sem)->year($year);
            } else if (!empty($semsel)) {
                return $query->semester($sem)->year($year);
            } else if (!empty($series_id)) {
                return $query->series($series_id);
            } else {
                return $query;
            }
        } else {
            if (empty($series_id)) {
                return $query->dateRange($dstart, $dend);
            } else {
                return $query->dateRange($dstart, $dend)->series($series_id);
            }
        }
    }
    
    
    public function fbYN($yesno)
    {
        $fb = Feedback::where('workshop_id', '=', $this->id)->
            where('recommend', '=', $yesno)->
            get();
        
        return $fb;
    }
}

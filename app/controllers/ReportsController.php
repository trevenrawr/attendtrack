<?php

class ReportsController extends BaseController {
    
    public function reportList()
    {
        $title = 'Available Reports';
        return View::make('reportList', array('title' => $title));
    }
    
    
    public function deptList()
    {
        $depts = Department::orderBy('title', 'asc')->get();
        
        $title = 'Department List';
        return View::make('departmentList', array('title' => $title, 'depts' => $depts));
    }
    
    
    public function deptInfo($id)
    {
        $dept = Department::with(array('teachers' => function($q) {
            $q->orderBy('name', 'asc')->
                paginate(50);
        }))->find($id);
        
        $this->wsFilterFlash();
        
        $title = $dept->title.' Info';
        return View::make('departmentInfo', array('title' => $title, 'dept' => $dept));
    }
    
    public function deptEdit()
    {
        if ($this->getUser()->permissions('su')) {
            $depts = Department::orderBy('title', 'asc')->get();

            $title = 'Add or Remove Department';
            return View::make('departmentEdit', array('title' => $title, 'depts' => $depts));
        } else {
            return Redirect::back();
        }
    }
            
    public function deptAdd()
    {
        if ($this->getUser()->permissions('su')) {
            $v = Validator::make(Input::all(), Department::$rules, Department::$messages);
            
            if ($v->passes()) {
                $dept = new Department;

                $dept->title = Input::get('title');
                $dept->STEM = Input::get('STEM');
                
                $dept->save();
                $this->logAction('insert', 'DP'.$dept->id);
                
                Session::flash('message', $dept->title.' added successfully!');
                return Redirect::to('/R/dept/edit');
            } else {
                return Redirect::to('/R/dept/edit')->withInput()->withErrors($v);
            }
        } else {
            return Redirect::back();
        }
    }
    
    public function deptRem()
    {
        if ($this->getUser()->permissions('su')) {
            $dept = Department::find(Input::get('d_id'));
            $title = $dept->title;
            
            $this->logAction('delete', 'DP'.$dept->id);
            $dept->delete();
            
            Session::flash('message', $title.' removed successfully!');
            
            return Redirect::to('/R/dept/edit');
        }
    }
    
    public function certList()
    {
        $this->wsFilterFlash();
        
        Session::flash('filter_prog', Input::get('filter_prog'));
        Session::flash('filter_cert', Input::get('filter_cert'));
        
        $tList = Teacher::certFilter()->program()->get();
        
        $title = 'Certificates';
        return View::make('certificateList', array('title' => $title, 'tList' => $tList));
    }
    
    
    ////// Series controller functions //////
    
    public function seriesList()
    {
        $series = Series::orderBy('title', 'asc')->get();
        
        $this->wsFilterFlash();
        
        $title = 'Series List';
        return View::make('seriesList', array('title' => $title, 'series' => $series));
    }
    
    public function seriesEdit()
    {
        if ($this->getUser()->permissions('su')) {
            $series = Series::orderBy('title', 'asc')->get();

            $title = 'Add or Remove Series';
            return View::make('seriesEdit', array('title' => $title, 'series' => $series));
        } else {
            return Redirect::back();
        }
    }
            
    public function seriesAdd()
    {
        if ($this->getUser()->permissions('su')) {
            $v = Validator::make(Input::all(), Series::$rules, Series::$messages);
            
            if ($v->passes()) {
                $ser = new Series;

                $ser->title = Input::get('title');
                
                $ser->save();
                $this->logAction('insert', 'SE'.$ser->id);
                
                Session::flash('message', $ser->title.' added successfully!');
                return Redirect::to('/R/series/edit');
            } else {
                return Redirect::to('/R/series/edit')->withInput()->withErrors($v);
            }
        } else {
            return Redirect::back();
        }
    }
    
    public function seriesRem()
    {
        if ($this->getUser()->permissions('su')) {
            $ser = Series::find(Input::get('s_id'));
            $title = $ser->title;
            
            $this->logAction('delete', 'SE'.$ser->id);
            $ser->delete();
            
            Session::flash('message', $title.' removed successfully!');
            
            return Redirect::to('/R/series/edit');
        }
    }
    
    public function seriesInfo($s_id)
    {
        $perPage = 25;
        $title = 'Series Details';
        
        $this->wsFilterFlash();
        
        $workshops = Workshop::where('series_id', '=', $s_id)->
            with('demographics')->
            with('presenters')->
            wsFilter()->
            orderBy('date', 'desc')->
            paginate($perPage);
        
        return View::make('seriesInfo', array('title' => $title, 's_id' => $s_id, 'workshops' => $workshops));
    }
    
    
    public function fbReports()
    {
        $series = Series::orderBy('title', 'asc')->get();
        
        $this->wsFilterFlash();
        
        $title = 'Feedback Aggregate Stats';
        return View::make('feedbackReports', array('title' => $title, 'series' => $series));
    }
    
    public function leadsList()
    {
        $leadSeries = Series::where('title', '=', 'Lead Training')->first();
        
        $leads = Teacher::
            select('teachers.*', 'workshops.date')->
            join('attendance', 'attendance.teacher_id', '=', 'teachers.id')->
            join('workshops', 'workshops.id', '=', 'attendance.workshop_id')->
            where('workshops.series_id', '=', $leadSeries->id)->
            orderBy('workshops.date', 'asc')->
            paginate(50);
        
        $title = 'Leads List';
        return View::make('leadsList', array('title' => $title, 'leads' => $leads, 'VTCs' => false));
    }
    
    public function VTCList()
    {
        $ds = Session::get('date_start');
        $de = Session::get('date_end');
        
        $leads = Teacher::select('teachers.*')->
            join('teachers AS t2', function($j)
                 {
                     $j->on('teachers.id', '=',  't2.firstVTCer')->
                         orOn('teachers.id', '=', 't2.secondVTCer');
                 })->
            paginate(50);
        
        $title = 'VTC List';
        return View::make('leadsList', array('title' => $title, 'leads' => $leads, 'VTCs' => true));
    }
    
    public function actionList()
    {
        $aList = Action::with('teacher')->orderBy('created_at', 'desc')->paginate(50);
        
        $title = 'Action Log';
        return View::make('actionList', array('title' => $title, 'aList' => $aList));
    }
    
    public function suggestionList()
    {
        $this->wsFilterFlash();
        
        $feedback = Feedback::join('workshops', function($j) {
                $j->on('workshops.id', '=', 'feedback.workshop_id');
                
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
                        $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                        $j->where('workshops.series_id', '=', $series_id);
                    } else if (!empty($semsel)) {
                        $j->where('workshops.semester', '=', $sem)->where(DB::raw('YEAR(workshops.date)'), '=', $year);
                    } else if (!empty($series_id)) {
                        $j->where('workshops.series_id', '=', $series_id);
                    }
                } else {
                    if (empty($series_id)) {
                        $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                    } else {
                        $j->where('workshops.date', '>', $dstart)->where('workshops.date', '<', $dend);
                        $j->where('workshops.series_id', '=', $series_id);
                    }
                }
            })->
            orderBy('feedback.created_at', 'desc')->
            paginate(100);
        
        $title = 'Suggestions List';
        return View::make('suggestionList', array('title' => $title, 'feedback' => $feedback));
    }
            
}
<?php

class WorkshopController extends BaseController {
    
    public function getWorkshop($id)
    {
        if ($id == 0) {
            $workshop = new Workshop;
        } else {
            $workshop = Workshop::where('id', '=', $id)->first();
        }
        
        Session::forget('ws_att_id');
        Session::forget('attMess');
        if (!Session::has('message')) {
            Session::flash('message', 'Here you can create workshops and edit workshop particulars!');
        }
        
        if ($this->getUser()->permissions('wsinfo'))
            $edit = true;
        else
            $edit = false;
        
        $title = 'Workshop Information Tool';
        return View::make('workshopTool', array('title' => $title, 'workshop' => $workshop, 'edit' => $edit));
    }

    public function workshopList()
    {
        // Make sure that if someone was adding attendees, they have to select a WS to keep doing so
        Session::forget('ws_att_id');
        Session::forget('attMess');
        
        $perPage = 100;
        $title = 'Workshop List';
        
        $this->wsFilterFlash();
        
        $wsName = Input::get('wsName');

        $workshops = Workshop::with('series')->
            with('demographics')->
            wsFilter()->
            where('title', 'LIKE', '%'.$wsName.'%')->
            orderBy('date', 'desc')->
            paginate($perPage);
        
        return View::make('workshopList', array('title' => $title, 'workshops' => $workshops));
    }
    
    public function attend()
    {
        $id = Input::get('ws_id');
        $ws = Workshop::find($id);
        if (empty($ws)) return Redirect::to('WS/list');
        
        if ($ws->date == date('Y-m-d')) {
            Session::put('workshop_id', $id);
            $this->logAction('info', 'SA'.$id);
            return App::make('TeacherController')->logout();
        } else {
            return Redirect::to('WS/list');
        }
    }
    
    public function stopAttend()
    {
        $this->logAction('info', 'EA'.Session::get('workshop_id'));
        Session::forget('workshop_id');
        return Redirect::to('WS/list');
    }
    
    public function remAttendance()
    {
        $att = Attendance::find(Input::get('att_id'));
        $ws_id = $att->workshop_id;
        
        $this->logAction('delete', 'AT'.$att->id);
        
        $att->delete();
        
        return Redirect::to('WS/info/'.$ws_id);
    }
    
    public function addAttendance($id)
    {
        $ws = Workshop::find($id);
        if (!empty($ws)) {
            Session::put('ws_att_id', $id);
            Session::forget('message');
            Session::put('attMess', 'Search for teachers to add to the workshop:<br />
            <strong><a href="/WS/info/'.$id.'">'.$ws->title.'</a></strong>');

            $name = Input::get('name');

            $tList = $this->teacherSearch($name);

            $title = 'Add Attendance';
            return View::make('teacherFind', array('title' => $title, 'tList' => $tList));
        } else {
            Session::flash('message', 'That workshop does not exist, please choose one from the list below to add new attendees to.');
            return Redirect::to('/WS/list');
        }
    }
    
    public function editAttendance()
    {
        $att = Attendance::find(Input::get('att_id'));
        $att->credits = Input::get('credits');
        
        $att->save();
        
        $this->logAction('update', 'AT'.$att->id);
        
        return Redirect::back();
    }

    
	public function updateDB()
	{
        if (!$this->getUser()->permissions('wsinfo'))
            return Redirect::to('WS/list');
        
        $input = Input::all();
        
        $v = Validator::make($input, Workshop::$rules, Workshop::$messages);
        
        if ($v->passes()) {
            $workshop = Workshop::firstOrNew(array('id' => Input::get('id')));
            if (Input::has('id')) {
                $verb = 'update';
            } else {
                $verb = 'insert';
            }
            
            $changed = false;
            
            $workshop->date = Input::get('date');
            $workshop->time = Input::get('time');
            $workshop->title = Input::get('title');
            $workshop->series_id = Input::get('series_id');
            $workshop->semester = strtolower(trim(Input::get('semester')));
            $workshop->head_count = Input::get('head_count') == '' ? 0 : Input::get('head_count');
            $workshop->credits = Input::get('credits') == '' ? 1 : Input::get('credits');
            
            if (count($workshop->getDirty()) > 0) {
                $workshop->save();
                $changed = true;
            }
            
            $oldPres = Presenter::select('id')->where('workshop_id', '=', $workshop->id)->get();
            foreach ($oldPres as $op) $opList[] = $op->id;
            
            for ($i = 0; $i < $workshop::NUMPRES; $i++) {
                $temp = trim(Input::get('presenter'.$i));
                if ($temp != '') {
                    $names[] = $temp;
                }
            }
            
            foreach ($names as $name) {
                if ($name != '') {
                    $teacher = Teacher::
                        where('name', '=', $name)->
                        get();

                    if ($teacher->count() == 0) {
                        $pres = Presenter::firstOrNew(array('workshop_id' => $workshop->id, 'name' => $name));
                        $pres->name = $name;
                        $pres->teacher_id = null;
                        $pres->workshop_id = $workshop->id;
                        
                        $pres->save();
                        $npList[] = $pres->id;
                    } elseif ($teacher->count() == 1) {
                        $pres = Presenter::firstOrNew(array('workshop_id' => $workshop->id, 'teacher_id' => $teacher[0]->id));
                        $pres->name = $teacher[0]->name;
                        $pres->teacher_id = $teacher[0]->id;
                        $pres->workshop_id = $workshop->id;
                        
                        $pres->save();
                        $npList[] = $pres->id;
                    } else {
                        // Head to the disambiguation tool!
                        Session::flash('message', 'There are two or more teachers by that name!');
                        $unknown[] = $name;
                    }
                }
            }
            
            // Compare the original presenter list with the new presenter list and delete anyone who didn't make the cut
            if (!empty($opList)) {
                $diff = array_diff($opList, $npList);
                foreach ($diff as $del) {
                    $p = Presenter::find($del);
                    $p->delete();
                }
                
                if (count($diff) > 0 || count(array_diff($npList, $opList)) > 0)
                    $changed = true;
            }
            
            if ($changed) $this->logAction($verb, 'WS'.$workshop->id);
            
            // if we had some ambiguous presenters, go to the presenter confirmation page
            if (isset($unknown) && count($unknown) > 0) {
                $title = 'Specify Presenter';
                return View::make('presenterFind', array('title' => $title, 'pres' => $unknown, 'wsid' => $workshop->id));
            }
            
            Session::flash('message', 'Workshop '.$verb.' successful!');
            return Redirect::to('WS/info/'.$workshop->id);
        } else {
            return Redirect::to('WS/info')->withInput()->withErrors($v);
        }
	}
    
    public function addPresenter() {
        $ws = Workshop::find(Input::get('wsid'));
        
        for ($i = 0; $i < $ws::NUMPRES; $i++) {
            if (Input::has('pres'.$i) && !empty(Input::get('pres'.$i))) {
                $tid = Input::get('pres'.$i);
                $pres = Presenter::firstOrNew(array('workshop_id' => $ws->id, 'teacher_id' => $tid));
                $pres->name = Input::get('pres'.$i.'Name');
                $pres->teacher_id = $tid == 0 ? null : $tid;
                $pres->workshop_id = $ws->id;
                
                $pres->save();
            }
        }
        
        return Redirect::to('WS/list');
    }
}
<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
    
    public function getUser()
    {
        if (Session::has('user'))
            return Teacher::find(Session::get('user'));
        else
            return new Teacher;
    }
    
    protected function wsFilterFlash()
    {
        // Get the proper information flashed to show how the results were filtered
        Session::put('date_start', Input::get('date_start'));
        
        if (empty(Input::get('date_start')))
            $dend = '';
        elseif (empty(Input::get('date_end')) && !empty(Input::get('date_start')))
            $dend = date('Y-m-d');
        else
            $dend = Input::get('date_end');
        Session::put('date_end', $dend);
        
        if (empty(Input::get('date_start')))
            Session::put('semsel', Input::get('semsel'));
        else
            Session::put('semsel', null);
        
        Session::put('series_id', Input::get('series_id'));
    }
    
    protected function teacherSearch($name)
    {
        if ($name != '') {
            // Pull in all the teachers, but NEVER the gtp user
            $tList = Teacher::where('name', 'LIKE', '%'.$name.'%')->
                where('id', '<>', 1)->get();
        } else {
            $tList = null;
        }
        
        return $tList;
    }
    
    protected function logAction($action, $note = '')
    {
        $act = new Action;
        $act->teacher_id = Session::get('user');
        $act->ip = Request::getClientIp();
        $act->action = $action;
        $act->note = $note;
        
        $act->save();
    }
    
}

<?php

class FeedbackController extends BaseController {
    
    // Pulls information on a particular feedback
    public function getFeedback($id, $tview = false)
    {
        if (!$this->getUser()->permissions('fbinfo')) {
            Session::forget('editFB');
        }
        
        $fb = Feedback::find($id);
        if (empty($fb)) $fb = new Feedback;
        
        // Don't complete the request if the user isn't a presenter
        if ($tview) {
            $ws = Workshop::find($fb->workshop_id);
            if (empty($ws) || !$ws->isPresenter(Session::get('user')))
                return Redirect::to('FB/list/');
            $title = 'Feedback Viewing Tool';
        } else {
            $title = 'Feedback Entry Tool';
        }
        
        return View::make('feedbackTool', array('title' => $title, 'fb' => $fb, 'tview' => $tview));
    }
    
    // Lists feedback associated with a particular workshop
    public function feedbackList($ws_id, $tview = false)
    {
        $perPage = 25;
        
        Session::put('wsfb_id', $ws_id);
        
        // Don't complete the request if the user isn't a presenter
        if ($tview) {
            $ws = Workshop::find($ws_id);
            if (!$ws->isPresenter(Session::get('user')))
                return Redirect::to('T/info');
        }
        
        $feedback = Feedback::where('workshop_id', '=', $ws_id)->orderBy('created_at', 'desc')->paginate($perPage);
        
        $title = 'Feedback List';
        return View::make('feedbackList', array('title' => $title, 'feedback' => $feedback, 'tview' => $tview));
    }
    
    // Posts updates to (or creates a new) feedback entry in the database
    public function updateDB()
    {
        if (!$this->getUser()->permissions('fbinfo'))
            return Redirect::to('FB/list');
        
        $ws_id = Session::get('wsfb_id');
        Session::forget('wsfb_id');
        
        $fb = Feedback::firstOrNew(array('id' => Input::get('id')));
        $fb->workshop_id = $ws_id;
        $fb->workshop_rating = Input::has('workshop_rating') ? Input::get('workshop_rating') : null;
        $fb->presenter_rating = Input::has('presenter_rating') ? Input::get('presenter_rating') : null;
        $fb->most_helpful = Input::get('most_helpful');
        $fb->least_helpful = Input::get('least_helpful');
        $fb->improve = Input::get('improve');
        $fb->recommend = Input::has('recommend') ? Input::get('recommend') : null;
        $fb->recommend_why = Input::get('recommend_why');
        $fb->suggestions = Input::get('suggestions');
        
        // Determine where all they were referred from
        $refs = array(
            'ref_GTPWeb',
            'ref_CIRTLWeb',
            'ref_CUCalendar',
            'ref_LeadEmail',
            'ref_DeptEmail',
            'ref_DeptPoster',
            'ref_TARec',
            'ref_ClassAssign',
            'ref_RSSFeed',
            'ref_Twitter',
            'ref_Facebook'
        );
        
        foreach ($refs as $ref) {
            if (Input::has($ref)) {
                $fb->$ref = true;
            } else {
                $fb->$ref = false;
            }
        }
            
        $fb->save();
        
        if (empty(Input::get('id'))) $act = 'insert';
        else $act = 'update';
        $this->logAction($act, 'FB'.$fb->id);
        
        return Redirect::to('/FB/list/'.$ws_id);
    }
    
    public function deleteFB()
    {
        $id = Input::get('fb_id');
        if (!$this->getUser()->permissions('fbinfo'))
            return Redirect::to('FB/list');
        
        $fb = Feedback::find($id);
        $fb->delete();
        
        $this->logAction('delete', 'FB'.$id);
        
        $ws_id = Session::get('wsfb_id');
        Session::forget('wsfb_id');
        return Redirect::to('/FB/list/'.$ws_id);
    }
}
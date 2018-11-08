<?php

class TeacherController extends BaseController {
    
    // Set up the login page
    public function login()
    {
        // If someone is already logged in, jump straight to routing
        if (Session::has('user')) {
            $t = Teacher::find(Session::get('user'));
            return $this->loginRoute($t);
        }
        
        $workshop = Workshop::find(Session::get('workshop_id'));
        $title = 'Teacher Login';
        return View::make('teacherStart', array('title' => $title, 'workshop' => $workshop));
    }
    
    
    // Process the login
    public function loginPost()
    {
        // Check to see if that session is locked out
        if (Session::has('unlock_time')) {
            if (Session::get('unlock_time') < time()) {
                Session::forget('unlock_time');
                Session::forget('login_fails');
            } else {
                Session::flash('message', 'Incorrect login info supplied 5 times.<br />You must wait another '.strval(intval(ceil((Session::get('unlock_time') - time()) / 60))).' minutes before attempting to log in again.');
                return Redirect::to('T/login/');
            }
        }
        
        // Collect info from the form
        $identikey = strtolower(Input::get('identikey'));
        $dn = 'uid='.$identikey.',ou=users,dc=colorado,dc=edu';
        $password = Input::get('password');
        $userType = Input::get('usertype');
		
        if($userType == 'Guest'){
			return Redirect::to('T/info/-1');
		}
        $useLDAP = true;
        
        // Verify with LDAP whether or not credentials are appropriate
        if ($useLDAP) {
            $ldapConn = ldap_connect('ldaps://directory.colorado.edu', 636);
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            $success = @ldap_bind($ldapConn, $dn, $password);
            ldap_unbind($ldapConn);
        } else {
            $success = ($password == 'pass');
        }
        
        if ($success) {
            // Log in the teacher
            $t = Teacher::where('identikey', '=', $identikey)->first();
            if (empty($t)) {
                $t = new Teacher;
                $t->identikey = $identikey;
                $t->save();
            }
            Session::put('user', $t->id);
            Session::forget('login_fails');

            return $this->loginRoute($t);
        } else {
            // Increment the login_fails counter and 
            if (Session::has('login_fails')) {
                Session::set('login_fails', Session::get('login_fails') + 1);
                if (Session::get('login_fails') > 4) {
                    Session::set('unlock_time', time() + (5 * 60));
                    return $this->loginPost();
                }
            } else {
                Session::set('login_fails', 1);
            }
            
            Session::flash('message', 'Incorrect Identikey or password.');
            return Redirect::to('T/login/');
        }
    }
    
    
    private function loginRoute($t)
    {
        // Don't pull info on the gtp, send them straight to the WS list
        if ($t->identikey == 'gtp') {
            return Redirect::to('WS/list');
        }
        
        return Redirect::to('T/info/'.$t->id);
    }
    
    
    public function logout()
    {
        Session::forget('user');
        Session::flash('message', 'You have been successfully logged out.');
        return Redirect::to('T/login/');
    }
    
    
    // Pull teacher info from the DB and load into the Teacher Tool
    public function getTeacher($id)
    {
		if($id == -1){
			return View::make('teacherTool', array('title' => 'Teacher Information',
													'teacher' => null,
													'edit' => null,
													'su' => null,
													'full' => false));
		}
        $user = $this->getUser();
        
        // Make sure they are who they say they are, or have permission to edit teacher info
        // If they aren't, force them to view only their profile
        if (Session::get('user') != $id && !$user->permissions('tinfo')) {
            $id = Session::get('user');
        }
        
        if ($id == '')
            return Redirect::to('T/login/');
        
        $teacher = Teacher::find($id);
        
        // If the user is viewing/logging into their own profile
        if (Session::get('user') == $id) {
            // If it's the first time they're logging in, workshop or not
            if (empty($teacher->name)) {
                
                
                $useLDAP = true;
                
                
                if ($useLDAP) {
                    // Bind to LDAP server to pull some demographic info
                    $ldapConn = ldap_connect('ldaps://directory.colorado.edu', 636);

                    // Search for the current user
                    $search = ldap_search($ldapConn, "ou=people,dc=colorado,dc=edu", 'uid='.$teacher->identikey);

                    // Get their info and unbind
                    $info = ldap_get_entries($ldapConn, $search);
                    ldap_unbind($ldapConn);
                } else {
                    $info['count'] = 0;
                }
                
                
                if ($info['count'] == 1) {
                    $teacher->email = strtolower($info[0]['mail'][0]);
                    $teacher->name = $info[0]['displayname'][0];
                }
                
                $teacher->save();
                Session::flash('message', '<strong>Welcome!</strong><br />
                We see this is your first time logging in to our server!  Please fill out the demographic information below.  Don\'t worry, though, it will not be shared with anyone; only used by us internally to make our GTP workshops even more useful for you!  Thanks!');
                
                $this->logAction('insert', 'TD'.$teacher->id);
                
                // No need to show them everything if it's their first time logging in
                $full = false;

            // If it's NOT the first time they're logging in
            } else {
                // If they're logging into a workshop
                if (Session::has('workshop_id')) {
                    Session::flash('message', '<strong>Welcome back!</strong><br /> Please check below and update any information that has changed before confirming your attendance.  Thanks!');
                    $full = false;

                // If they're just viewing their profile
                } else {
                    Session::flash('message', '<strong>Welcome back!</strong><br />  On this page you may edit some of your demographic information, as well as view your completed workshops and other Certificate requirements.');
                    $full = true;
                }
            }
            
        // If someone is trying to view a profile that isn't theirs (with 'tinfo' privs) ...
        } else {
            // ... and they DON'T exist
            if (empty($teacher)) {
                return Redirect::to('T/find');
            
            // ... and they DO exist
            } else {
                $full = true;
            }
        }
        
        $title = 'Teacher Information';
        $edit = $user->permissions('tinfo');
        $su = $user->permissions('su');
        $info = array('title' => $title,
                      'teacher' => $teacher,
                      'edit' => $edit,
                      'su' => $su,
                      'full' => $full);
        return View::make('teacherTool', $info);
    }
    
    // Post changes to the database
	public function updateDB()
	{
		if(empty(Session::get('user'))){
			$this->attend(null, Session::get('workshop_id'));
			// Set it up for the next person
			return Redirect::to('/T/logout');
		}
        $v = Validator::make(Input::all(), Teacher::$rules, Teacher::$messages);
        
        // Check for validation errors, then update the DB!
        if ($v->passes()) {
            $teacher = Teacher::where('identikey', '=', Input::get('identikey'))->first();
            $changed = false;
            
            $sets = array('name', 'email', 'gender', 'program', 'affiliation', 
                          'international', 'year', 'department_id');

            foreach ($sets as $s) {
                $teacher->$s = Input::get($s);
            }
            
            if (count($teacher->getDirty()) > 0) {
                $teacher->save();
                $this->logAction('update', 'TD'.$teacher->id);
            }
            
            // Organize and save extended information, and don't save attendance (in case the gtpAdmin forgot to stop attendance)
            if ($this->getUser()->permissions('tinfo') && !Session::has('workshop_id')) {
                // All the fields that will be updated
                $sets = array('firstVTCdate', 'secondVTCdate', 'CCT_status', 'CCT_disc_spec', 'CCT_obser_who', 'CCT_obser_date', 'CCT_depteval_who', 'CCT_depteval_date', 'CCT_port_status', 'CCT_survey_status', 'CCT_kolb_quad', 'CCT_kolb_who', 'CCT_kolb_date', 'CCT_wing_date', 'CCT_wing_who', 'CCT_notes', 'PDC_status', 'PDC_CV_status', 'PDC_visit_where', 'PDC_visit_date', 'PDC_port_status', 'PDC_pres_title', 'PDC_pres_date', 'PDC_plan_status', 'PDC_mentor_hrs', 'PDC_mentor_who', 'PDC_eval_date', 'PDC_eval_who', 'PDC_survey_status', 'PDC_notes');
                
                $dateCheck = array('CCT_status', 'CCT_port_status', 'CCT_survey_status', 'PDC_status', 'PDC_CV_status', 'PDC_port_status', 'PDC_plan_status', 'PDC_survey_status');
                
                
                foreach ($sets as $s) {
                    $in = Input::get($s);
                    // Set an "empty" date so that getDirty returns changes properly
                    if (preg_match('/date/', $s) && $in == '')
                        $in = '0000-00-00';
                    
                    $teacher->$s = $in;
                }
                
                // update the dates for changed statuses
                foreach ($dateCheck as $dc) {
                    $dirt = $teacher->getDirty();
                    if (array_key_exists($dc, $dirt) && Input::get($dc) != '') {
                        $cng  = preg_replace('/status/', 'date', $dc);
                        $teacher->$cng = date('Y-m-d');
                    }
                }
                
                $firstVTCer = Teacher::where('name', '=', Input::get('firstVTCer'))->first();
                if (!empty($firstVTCer)) $teacher->firstVTCer = $firstVTCer->id;
                else $teacher->firstVTCer = null;
                
                $secondVTCer = Teacher::where('name', '=', Input::get('secondVTCer'))->first();
                if (!empty($secondVTCer)) $teacher->secondVTCer = $secondVTCer->id;
                else $teacher->secondVTCer = null;
                
                // Store files that may have been uploaded
                if (Input::hasFile('firstVTCnotes')) {
                    $path = dirname(__DIR__).'/storage/notes/';
                    $ext = Input::file('firstVTCnotes')->getClientOriginalExtension();
                    Input::file('firstVTCnotes')->move($path, $teacher->identikey.'VTC1.'.$ext);
                    $teacher->firstVTCnotes = $teacher->identikey.'VTC1.'.$ext;
                }
                
                if (Input::hasFile('secondVTCnotes')) {
                    $path = dirname(__DIR__).'/storage/notes/';
                    $ext = Input::file('secondVTCnotes')->getClientOriginalExtension();
                    Input::file('secondVTCnotes')->move($path, $teacher->identikey.'VTC2.'.$ext);
                    $teacher->secondVTCnotes = $teacher->identikey.'VTC2.'.$ext;
                }
                
                // If they're a super user, then check for changes in permissions
                if ($this->getUser()->permissions('su')) {
                    
                    $perms = Permission::get();
                    $toset = array();
                    
                    DB::beginTransaction();
                    
                    $olds = DB::table('userpermissions')->where('teacher_id', '=', $teacher->id)->get();
                    $old = array();
                    foreach ($olds as $i)
                        $old[] = $i->permission_id;
                    
                    DB::table('userpermissions')->where('teacher_id', '=', $teacher->id)->delete();
                    foreach ($perms as $perm)
                        if (Input::has($perm->permission))
                            $toset[] = $perm->permission;
                        
                    // If you're set up to change attendance records, you also need to be able to edit workshops
                    if (in_array('attendance', $toset))
                        $toset[] = 'wsinfo';
                    
                    // If you're a superuser, you get to do EVERYTHING!
                    if (in_array('su', $toset))
                        foreach ($perms as $perm)
                            $toset[] = $perm->permission;
                    
                    foreach ($perms as $perm)
                        if (in_array($perm->permission, $toset))
                            DB::table('userpermissions')->insert(
                                array('teacher_id' => $teacher->id, 'permission_id' => $perm->id)
                            );
                        
                    DB::commit();
                    
                    $news = DB::table('userpermissions')->where('teacher_id', '=', $teacher->id)->get();
                    $new = array();
                    foreach ($news as $i)
                        $new[] = $i->permission_id;
                    
                    if (count(array_diff($old, $new)) > 0 || count(array_diff($new, $old)) > 0)
                        $this->logAction('update', 'TP'.$teacher->id);
                }
                
                $dirt = $teacher->getDirty();
                if (count($dirt) > 0) {
                    $teacher->save();
                    $this->logAction('update', 'TC'.$teacher->id);
                }
                
                return Redirect::to('/T/info/'.$teacher->id);
                
            // Save attendance data, if this sign-in is for attendance
            } else if (Session::has('workshop_id')) {
				
                $this->attend($teacher, Session::get('workshop_id'));
                // Set it up for the next person
                return Redirect::to('/T/logout');
            }
            
            return Redirect::to('T/info/');
            
        } else {
            return Redirect::to('T/info/')->withInput()->withErrors($v);
        }
	}
    
    
    // Prepare VTC notes for download
    public function getNotes($id, $VTC)
    {
        $user = $this->getUser();
        
        if (Session::get('user') != $id && !$user->permissions('tinfo')) {
            return App::abort(404);
        }
     
        $t = Teacher::find($id);
        
        if ($VTC == 1) $p = 'firstVTCnotes';
        elseif ($VTC == 2) $p = 'secondVTCnotes';
        else return App::abort(404);
        
        $path = $t->$p;
        
        if (!empty($path)) {
            $ext = explode('.', $path);
            $ext = $ext[1];

            if ($ext == 'docx') $ct = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            else if ($ext == 'pdf') $ct = 'application/pdf';
            else $ct = 'application/octet-stream';

            $headers = array('Content-Type' => $ct, 'Content-Disposition' => 'attachment');

            $dir = dirname(__DIR__).'/storage/notes/';
            return Response::download($dir.$path, $p.'.'.$ext, $headers);
        } else {
            return App::abort(404);
        }
    }
    
    // Log attendance and a snapshop of the demographic information from the teacher who signed in
    private function attend($teacher, $ws_id)
    {
		if(!isset($teacher)){
			$att = new Attendance;
			
			$att->teacher_id = 0;			
			$att->workshop_id = $ws_id;	
			$att->attendee_name = Input::get('name');
			$att->attendee_email = Input::get('email');
		}
		else{
			$att = Attendance::firstOrNew(array('teacher_id' => $teacher->id , 'workshop_id' => $ws_id));

			$att->department_id = isset($teacher->department_id) ? $teacher->department_id : null;
			$att->gender = isset($teacher->gender) ? $teacher->gender : null;
			$att->program = isset($teacher->program) ? $teacher->program : null;
			$att->affiliation = isset($teacher->affiliation) ? $teacher->affiliation : null;
			$att->international = isset($teacher->international) ? $teacher->international : null;
			$att->year = $teacher->year;
		}
		
        $ws = Workshop::find($ws_id);
        $att->credits = $ws->credits;
        
        if (count($att->getDirty()) > 0) {
            $att->save();
            $this->logAction('insert', 'AT'.$att->id);
        }
    }
    
    // Insert new attendance record (as added by GTP admin)
    public function addAttendance()
    {
        $t_id = Input::get('t_id');
        $ws_id = Session::get('ws_att_id');
        $t = Teacher::find($t_id);
        $this->attend($t, $ws_id);
        
        $title = 'Add Attendance';
        return Redirect::back();
    }
    
    public function findTeacher()
    {
        $name = Input::get('name');
        
        Session::forget('ws_att_id');
        Session::forget('attMess');
        
        $tList = $this->teacherSearch($name);
        
        $title = 'Teacher Search';
        return View::make('teacherFind', array('title' => $title, 'tList' => $tList));
    }
    
    
    public function searchNames()
    {
        $seed = Input::get('q');
        
        if ($seed != '') {
            $list = Teacher::select('name')->
                                    where('name', 'LIKE', '%'.$seed.'%')->
                                    get();
            return $list->toJson();
        } else {
            return '';
        }
    }

}

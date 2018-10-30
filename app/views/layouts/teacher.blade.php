<?php

Form::macro('choices', function($type, $curr)
{
    if ($type == 'gender') {
        $options = array('male', 'female', 'prefer not to answer');
        $req = '';
    } elseif ($type == 'program') {
        $options = array('undergrad', 'masters', 'doctorate', 'postdoc', 'faculty', 'other');
        $req = ' required="true"';
    } elseif ($type == 'affiliation') {
        $options = array('TA', 'GPTI', 'RA', 'instructor', 'professor', 'staff', 'other');
        $req = ' required="true"';
    } elseif ($type == 'international') {
        $options = array('yes', 'no');
        $req = '';
    } else {
        return 0;
    }
    
    $optionList = '';
    for ($i = 0; $i < count($options); $i++) {
        if ($options[$i] == Input::old($type) || $options[$i] == $curr) {
            $selected = ' checked="checked"';
        } else {
            $selected = '';
        }
        
        $optionList .= '
            <li>
                <input type="radio" name="'.$type.'" id="'.$options[$i].'" value="'.$options[$i].'"'.$selected.$req.' />
                <label class="radioLabel" for="'.$options[$i].'">'.ucfirst($options[$i]).'</label>
            </li>';
    }
    
    
    $output = '
    <ul class="radioList">
        '.$optionList.'
    </ul>
    
    ';
    
    return $output;
});


Form::macro('deptList', function($sel)
{
    if ($sel == '' && Input::old('department_id') != '')
        $sel = Input::old('department_id');
    
    $departments = Department::get();
    $output = '
        <select name="department_id" id="department_id" autocomplete="off" required="true">
            <option value=""></option>';
    
    foreach ($departments as $dept) {
        if ($sel == $dept->id) $out = ' selected="selected"';
        else $out = '';
        
        $output .= '
            <option value="'.$dept->id.'"'.$out.'>'.$dept->title.'</option>';
    }
    
    $output .= '
        </select>
        ';
    
    return $output;
});


Form::macro('status', function($name, $t, $dtext = '')
{
    if ($name == 'CCT_kolb_quad')
        $options = array('diverging', 'assimilating', 'converging', 'accomodating');
    elseif ($name == 'CCT_status' || $name == 'PDC_status')
        $options = array('inactive', 'active', 'certified');
    elseif ($name == 'CCT_survey_status' || $name == 'PDC_survey_status')
        $options = array('not sent', 'sent', 'completed');
    else
        $options = array('incomplete', 'in review', 'accepted');
    
    if (empty($t->$name) && Input::old($name) != '') $old = Input::old($name);
    else $old = $t->$name;
    
    $out = '
        <select name="'.$name.'" autocomplete="off" '.$dtext.'>
            <option value=""></option>';
    
    foreach ($options as $o) {
        if ($old == $o) $sel = ' selected="selected"';
        else $sel = '';
        
        $out .= '
            <option value="'.$o.'"'.$sel.'>'.ucfirst($o).'</option>';
    }
    
        $out .= '
        </select>
        ';
    
    return $out;
});


Form::macro('date', function($name, $t, $dtext = '')
{
    $val = isset($t->$name) ? $t->$name : '';
    $out = '
        <input type="date" name="'.$name.'" value="'.$val.'" '.$dtext.'/>
        ';
    
    return $out;
});

    
Form::macro('who', function($name, $t, $dtext = '')
{
    $val = isset($t->$name) ? $t->$name : '';
    $out = '
        <input name="'.$name.'" id="'.$name.'" list="names" value="'.$val.'" onkeyup="dlist(\''.$name.'\', \'names\');" autocomplete="off" '.$dtext.'/>
        ';
    
    return $out;
});

Form::macro('permissions', function($t)
{
    $options = array(
        'tinfo' => 'Edit certification info',
        'wsinfo' => 'Add/edit workshops',
        'wssignin' => 'Officiate workshop signin',
        'attendance' => 'Alter attendance records',
        'fbinfo' => 'Enter/edit feedback',
        'reports' => 'View and print reports',
        'su' => 'All permissions');
    
    $output = '
    <ul class="radioList">
            ';
    
    foreach ($options as $key => $opt) {
        $output .= '
        <li>
            <input type="checkbox" id="'.$key.'" name="'.$key.'"';
        if ($t->permissions($key)) {
            $output .= ' checked="checked"';
        }
        $output .= ' />
            <label class="radioLabel" for="'.$key.'">'.$opt.'</label>
        </li>';
    }

    $output .= '
    </ul>
    ';
    
    return $output;
});


Form::macro('longAnswer', function($name, $old)
{
    $output = '
    <textarea name="'.$name.'" autocomplete="off">'.$old.'</textarea>
    ';
    
    return $output;
});

?>
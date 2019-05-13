<?php


Form::macro('rating', function($type, $old)
{
    if (Session::has('editFB')) {
        $dis = '';
    } else {
        $dis = ' disabled="disabled"';
    }
    
    if ($type == 'WS') {
        $verbage = 'workshop/session overall';
        $name = 'workshop_rating';
    } else {
        $verbage = 'presenter(s)';
        $name = 'presenter_rating';
    }
    
    $options = array(
        'Very Poor',
        'Poor',
        'Average',
        'Good',
        'Very Good',
        'Excellent');
    
    
    $output = '
    <label>
        Please indicate how you rate the '.$verbage.'.
    </label>
    <table class="rating">
        <thead><tr>';
    
    for ($i = 1; $i <= 6; $i++) {
        $output .= '
            <td><label class="radioLabel" for="'.$type.$i.'">'.$options[$i-1].'</label></td>';
    }
        $output .= '
        </tr></thead>
        <tbody><tr>';
    
    for ($i = 1; $i <= 6; $i++) {
        $output .= '
            <td><input type="radio" id="'.$type.$i.'" name="'.$name.'" value="'.$i.'"';
        if ($old == $i) {
            $output .= ' checked="checked" ';
        }
        $output .= $dis.' /></td>';
    }
        $output .= '
        </tr></tbody>
    </table>
    ';
    
    return $output;
});

Form::macro('longAnswer', function($question, $old)
{
    if (Session::has('editFB')) {
        $dis = '';
    } else {
        $dis = ' disabled="disabled"';
    }
    
    if ($question == 'most_helpful') {
        $prompt = 'What was the most helpful part of the workshop/session?';
    } elseif ($question == 'least_helpful') {
        $prompt = 'What was the least helpful part of this workshop/session?';
    } elseif ($question == 'improve') {
        $prompt = 'What teaching and learning activities do you plan to improve due to this workshop/session?';
    } elseif ($question == 'suggestions') {
        $prompt = 'Please give us suggestions for topics you would like to see presented, or the names of faculty whom you think would be able to give outstanding workshops/sessions for the GTP.';
    }
    
    $output = '
    <label>
        '.$prompt.'
    </label>
    <textarea name="'.$question.'"'.$dis.' autocomplete="off">'.$old.'</textarea>
    ';
    
    return $output;
});

Form::macro('yesNoWhy', function($question, $yesNo, $old)
{
    if (Session::has('editFB')) {
        $dis = '';
    } else {
        $dis = ' disabled="disabled"';
    }
    
    $output = '
    <table class="yesNoWhy">
        <tr>
            <td><input type="radio" name="'.$question.'" id="yes" value="yes"';
    if ($yesNo == 'yes') {
        $output .= ' checked="checked"';
    }
    $output .= $dis.'/> <label class="radioLabel" for="yes">Yes</label></td>
            <td rowspan="2"><textarea name="'.$question.'_why"'.$dis.'>'.$old.'</textarea></td>
        </tr>
        <tr>
            <td><input type="radio" name="'.$question.'" id="no" value="no"';
    if ($yesNo == 'no') {
        $output .= ' checked="checked"';
    }
    $output .= $dis.'/> <label class="radioLabel" for="no">No</label></td>
        </tr>
    </table>
    ';
    
    return $output;
});

Form::macro('referred', function($fb)
{
    if (Session::has('editFB')) {
        $dis = '';
    } else {
        $dis = ' disabled="disabled"';
    }
    
    $options = array(
        'ref_GTPWeb' => 'GTP Website',
        'ref_CIRTLWeb' => 'CIRTL Website',
        'ref_CUCalendar' => 'CU Calendar',
        'ref_LeadEmail' => 'Lead Graduate Teacher Email',
        'ref_DeptEmail' => 'Department Listserv',
        'ref_DeptPoster' => 'GTP Poster in Department',
        'ref_TARec' => 'TA Recommended',
        'ref_ClassAssign' => 'Class Assignment',
        'ref_RSSFeed' => 'RSS Feed',
        'ref_Twitter' => 'Twitter',
        'ref_Facebook' => 'Facebook'
    );
    
    
    $output = '
    <ul class="radioList">
            ';
    
    reset($options);
    for ($i = 0; $i < count($options); $i++) {
        $key = key($options);
        $output .= '
        <li><input type="checkbox" id="'.$key.'" name="'.$key.'"';
        if (isset($fb->$key) && $fb->$key) {
            $output .= ' checked="checked"';
        }
        $output .= $dis.' /> <label class="radioLabel" for="'.$key.'">'.$options[$key].'</label></li>';
        next($options);
    }

        $output .= '
    </ul>
    ';
    
    return $output;
});

?>
<?php

Form::macro('presList', function($pres, $num, $dtext)
{
    $output = '';
    for ($i = 0; $i < $num; $i++) {
        if (!isset($pres[$i]->name)) $pres[$i]->name = '';

        $output .= '
            <input name="presenter'.$i.'" id="presenter'.$i.'" list="presList" value="'.$pres[$i]->name.'" onkeyup="dlist(\'presenter'.$i.'\', \'presList\');" autocomplete="off" class="field" placeholder="Presenter Name" '.$dtext.'/>
                ';
        if ($i % 3 == 2)
            $output .= '<br />';
    }
    
    return $output;
});


Form::macro('selects', function($type, $old, $dtext = '')
{
    if ($type == 'semester') {
        $list = array('fall', 'spring', 'summer');
    } elseif ($type == 'series_id') {
        $list = Series::orderBy('title')->get();
    } elseif ($type == 'semsel') {
        $list = Workshop::semesterList()->get();
    } elseif ($type == 'filter_prog') {
        $list = array('masters', 'doctorate', 'postdoc', 'faculty', 'undergrad', 'other');
    } elseif ($type == 'filter_cert') {
        $list = array('CCT', 'PDC', 'both');
    }
    
    $out = '
        <select name="'.$type.'" id="'.$type.'" autocomplete="off" '.$dtext.'>
            <option value=""></option>';
    
    foreach ($list as $l) {
        if ($type == 'series_id') {
            $val = $l->id;
            $disp = $l->title;
        } else if ($type == 'semsel') {
            $val = $l->semsel;
            $disp = $l->semsel;
        } else {
            $val = $l;
            $disp = $l;
        }
        
        if ($val == $old) {
            $chk = ' selected="selected"';
        } else {
            $chk = '';
        }
        
        $out .= '
            <option value="'.$val.'"'.$chk.'>'.ucfirst($disp).'</option>';
    }
    
    $out .= '
        </select>';
    
    return $out;
});


Form::macro('date', function($name, $val, $dtext = '')
{
    $out = '
        <input type="date" name="'.$name.'" value="'.$val.'" '.$dtext.'/>
        ';
    
    return $out;
});

?>
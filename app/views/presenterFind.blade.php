@extends('layouts.master')
@section('content')
<?php

if (Session::has('ws_att_id')) {
    $url = '/WS/addAtt/'.Session::get('ws_att_id');
} else {
    $url = '/T/find';
}

?>

<?php
if (!empty($pres)) {
    foreach ($pres as $p) {
        $ts = Teacher::
            where('name', '=', $p)->
            get();
        $tList[] = $ts;
    }
}
?>

@if(!empty($tList))

{{ Form::open(array('url' => '/WS/presAdd')) }}
{{ Form::hidden('wsid', $wsid) }}

<table class="WSList">
    <thead><tr>
        <td>?</td>
        <td>Teacher Name</td>
        <td>Department</td>
        <td>Email</td>
        <td>Affiliation</td>
    </tr></thead>
    <tbody>
<?php

$j = 0;
foreach ($tList as $ts) {
    echo '
        <tr class="question"><td colspan="5">Presenter '.($j + 1).'</td></tr>';
    
    $i = 0;
    if (count($ts) == 1)
        $chk = 'checked="checked" ';
    else
        $chk = '';
    
    $name = $ts[0]->name;
    
    foreach ($ts as $t) {
        echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td><input type="radio" name="pres'.$j.'" value="'.$t->id.'" '.$chk.'/></td>
            <td>'.$t->name.'</td>
            <td>'.$t->department->title.'</td>
            <td>'.$t->email.'</td>
            <td>'.ucfirst($t->affiliation).'</td>
        </tr>';
        $i++;
    }
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td><input type="radio" name="pres'.$j.'" value="0" '.$chk.'/></td>
            <td>'.$name.'</td>
            <td>(Other)</td>
            <td>'.Form::hidden('pres'.$j.'Name', $name).'</td>
        </tr>';
    $j++;
}
?>
    </tbody>
</table>

{{ Form::submit('Confirm Presenters', array('class' => 'submit')) }}

{{ Form::close() }}

@endif

@stop
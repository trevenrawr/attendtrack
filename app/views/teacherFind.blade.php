@extends('layouts.master')
@section('content')
<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;

if (Session::has('ws_att_id')) {
    $url = '/WS/addAtt/'.Session::get('ws_att_id');
} else {
    $url = '/T/find';
}

?>

@if(Session::has('attMess'))
    <p class="message">{{ Session::get('attMess') }}</p>
@endif

{{ Form::open(array('url' => $url, 'method' => 'get')) }}
<label for="name">Name:</label>
{{ Form::text('name', Session::get('name'), array('placeholder' => 'name', 'id' => 'name')) }}
{{ Form::submit('Search') }}
{{ Form::close() }}

@if(!empty($tList))

<table class="WSList">
    <thead><tr>
        <td>Teacher Name</td>
        <td>Email</td>
        <td>Department</td>
        <td>Last Updated</td>
        @if(Session::has('ws_att_id') && $user->permissions('attendance'))
        <td>Attend</td>
        @endif
    </tr></thead>
    <tbody>
<?php

$i = 0;
foreach ($tList as $t) {
    
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">';
    
    if ($user->permissions('tinfo')) {
        echo '
            <td><a href="/T/info/'.$t->id.'">'.$t->name.'</a></td>';
    } else {
        echo '
            <td>'.$t->name.'</td>';
    }
    
    echo '
            <td><a href="mailto:'.$t->email.'">'.$t->email.'</a></td>
            <td>'.$t->department->title.'</td>
            <td>'.date_format(date_create($t->updated_at), 'M d, Y').'</td>';
    
    if (Session::has('ws_att_id') && $user->permissions('attendance')) {
        if ($t->attended(Session::get('ws_att_id')))
            $hide = array('style' => 'visibility: hidden;');
        else
            $hide = array();
            
        echo '
            <td>'.Form::open(array_merge(array('url' => '/WS/insAtt/', 'class' => 'addnew', 'onclick' => 'confirm(\'Please confirm teacher attendance.\');'), $hide)).Form::hidden('t_id', $t->id).Form::submit('Attend').Form::close().'</td>';
    }
    
    echo '
        </tr>';
    
    $i++;
}
?>
    </tbody>
</table>

@endif
@stop
@extends('layouts.master')
@include('layouts.workshop')
@section('content')

<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;
?>

@if($VTCs)
<div style="text-align: center;">
    {{ Form::open(array('url' => '/WS/list')) }}
    Dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/WS/list')) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>
@endif

@if(!empty($leads))

<table class="WSList sortable">
    <thead><tr>
        <td>Teacher Name</td>
        <td>Department</td>
        @if(!$VTCs)
        <td>Email</td>
        <td>Lead Year</td>
        @endif
        <td>1st VTCs</td>
        <td>2nd VTCs</td>
    </tr></thead>
    <tbody>
<?php

$i = 0;
foreach ($leads as $t) {
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">';
    
    if ($user->permissions('tinfo')) {
        echo '
            <td><a href="/T/info/'.$t->id.'">'.$t->name.'</a></td>';
    } else {
        echo '
            <td>'.$t->name.'</td>';
    }
    
    $firstVTCs = Teacher::where('firstVTCer', '=', $t->id)->count();
    $secondVTCs = Teacher::where('secondVTCer', '=', $t->id)->count();
    if($t->department){
		echo '
		<td>'.$t->department->title.'</td>';
	}else{
		echo '
		<td></td>';	//department placeholder incase it's the first login 
	}
    
    if (!$VTCs)
        echo '
            <td><a href="mailto:'.$t->email.'">'.$t->email.'</a></td>
            <td>'.date_format(date_create($t->date), 'Y').'</td>';
    
    echo '
            <td>'.$firstVTCs.'</td>
            <td>'.$secondVTCs.'</td>
        </tr>';
    
    $i++;
}
?>
    </tbody>
</table>

{{ $leads->links() }}

@endif
@stop
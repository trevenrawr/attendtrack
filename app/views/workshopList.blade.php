@extends('layouts.master')
@include('layouts.workshop')
@section('content')

<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;
$i=0;
?>

<div style="text-align: center;">
    {{ Form::open(array('url' => '/WS/list', 'method' => 'get')) }}
    <label for="wsName">Search by name:</label>
    {{ Form::text('wsName', Session::get('wsName'), array('placeholder' => 'name', 'id' => 'wsName')) }}
    {{ Form::submit('Search') }}
    {{ Form::close() }}

    <hr style="margin-top: 5px" />

    {{ Form::open(array('url' => '/WS/list')) }}
    Filter by Series: {{ Form::selects('series_id', Session::get('series_id')) }}<br />
    Semester: {{ Form::selects('semsel', Session::get('semsel')) }}
    or dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/WS/list')) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>

@if($user->permissions('wsinfo'))
<p><a href="/WS/info" class="start">Create a new workshop</a></p>
@endif

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Workshop Title</th>
            <th>Date</th>
            <th>Presenter</th>
            <th>Series</th>
            @if($user->permissions('wssignin'))
            <th>Login</th>
            @endif
            <th>Att.</th>
            @if($user->permissions('fbinfo'))
            <th>FB Entry</th>
            @endif
        </tr>
    </thead>
    <tbody>
<?php
function displayWorkshopList($workshops,$user){
	global $i;
	foreach ($workshops as $ws) {
		$sess_ws = Session::get('workshop_id');
		
		if ($ws->date == date('Y-m-d')) {
			if ($ws->id == $sess_ws) {
				$login = Form::open(array_merge(array('url' => '/WS/stopAttend/', 'class' => 'delete', 'method' => 'get'))).Form::submit('Stop').Form::close();
			} else {
				$login = Form::open(array_merge(array('url' => '/WS/attend/', 'class' => 'addnew'))).Form::hidden('ws_id', $ws->id).Form::submit('Start').Form::close();
			}
		} else {
			$login = Form::submit('', array('style' => 'visibility: hidden;'));
		}
		
		if (!empty($ws->presenters[0]))
			$pres = $ws->presenters[0]->name;
		else
			$pres = '<a href="/WS/info/'.$ws->id.'">Add presenter!</a>';
		
		if ($user->permissions('wsinfo'))
			$ws_title = '<a href="/WS/info/'.$ws->id.'">'.$ws->title.'</a>';
		else
			$ws_title = $ws->title;
		
		echo '
			<tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
				<td>'.$ws_title.'</td>
				<td>'.date_format(date_create($ws->date), 'n/d/Y').'</td>
				<td>'.$pres.'</td>
				<td>'.$ws->series->title.'</td>';
		if ($user->permissions('wssignin')) {
			echo '
				<td>'.$login.'</td>';
		}
		echo '
				<td>'.$ws->attendees->count().'</td>';
		if ($user->permissions('fbinfo'))
			echo '<td><a href="/FB/list/'.$ws->id.'" class="start">FB List</a></td>';
		echo '
			</tr>';
		
		$i++;
	}
}
displayWorkshopList($todaysWorkshops,$user);
displayWorkshopList($otherWorkshops,$user);
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
{{ $otherWorkshops->links() }}
@stop
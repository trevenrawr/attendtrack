@extends('layouts.master')
@include('layouts.workshop')
@section('content')

<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;
?>

<p class="message">
    Teachers in {{ $dept->title }}
</p>

<div style="text-align: center;">
    {{ Form::open(array('url' => '/R/dept/'.$dept->id)) }}
    Series: {{ Form::selects('series_id', Session::get('series_id')) }}<br />
    Semester: {{ Form::selects('semsel', Session::get('semsel')) }}
    or dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/R/dept/'.$dept->id)) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>

<table class="WSList sortable">
    <thead>
        <tr>
            <td>Teacher</td>
            <td>WS Attended</td>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($dept->teachers as $t) {
    if ($user->permissions('tinfo'))
        $tname = '<a href="/T/info/'.$t->id.'">'.$t->name.'</a>';
    else
        $tname = $t->name;
    
    $att = Attendance::where('teacher_id', '=', $t->id)->
        whereHas('workshop', function($q) { $q->wsFilter(); })->
        get();
        
    //if ($att->count() > 0)
        echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td>'.$tname.'</td>
            <td>'.$att->count().'</td>
        </tr>';
    $i++;
}

?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

@stop
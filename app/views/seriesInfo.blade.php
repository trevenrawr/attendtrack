@extends('layouts.master')
@include('layouts.workshop')
@section('content')

<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;

$series = Series::find($s_id);
?>

<p class="message">Workshop information for the {{ $series->title }} series.</p>

<div style="text-align: center;">
    {{ Form::open(array('url' => '/R/series/'.$s_id)) }}
    Semester: {{ Form::selects('semsel', Session::get('semsel')) }}
    or dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/R/series/'.$s_id)) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Workshop Title</th>
            <th>Date</th>
            <th>Presenter</th>
            <th>Affiliation</th>
            <th>Att.</th>
            <th>Rec. Y:N</th>
            <th>WS Rating</th>
            <th>Pres Rating</th>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($workshops as $ws) {
    $sess_ws = Session::get('workshop_id');
    
    if (!empty($ws->presenters[0])) {
        $pres = $ws->presenters[0]->name;
        $aff = Teacher::find($ws->presenters[0]->teacher_id);
        if (!empty($aff)) $aff = $aff->affiliation;
        else $aff = 'Unknown';
    } else {
        $pres = '<a href="/WS/info/'.$ws->id.'">Add presenter!</a>';
        $aff = 'Unknown';
    }
    
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td><a href="/WS/info/'.$ws->id.'">'.$ws->title.'</a></td>
            <td>'.date_format(date_create($ws->date), 'n/d/Y').'</td>
            <td>'.$pres.'</td>
            <td>'.$aff.'</td>
            <td>'.$ws->attendees->count().'</td>
            <td>'.count($ws->fbYN('yes')).':'.count($ws->fbYN('no')).'</td>
            <td>'.Feedback::WSAvg($ws->id).'</td>
            <td>'.Feedback::PresAvg($ws->id).'</td>
        </tr>';
    
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

{{ $workshops->links() }}

@stop
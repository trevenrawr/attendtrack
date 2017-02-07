@extends('layouts.master')
@include('layouts.workshop')
@section('content')


<div style="text-align: center;">
    {{ Form::open(array('url' => '/R/series')) }}
    Semester: {{ Form::selects('semsel', Session::get('semsel')) }}
    or dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/R/series')) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>

<p><a href="/R/series/edit" class="start">Add or Remove Series</a></p>

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Series</th>
            <th>Workshops</th>
            <th>Attendees</th>
            <th>Att/WS</th>
            <th>Individuals</th>
            <th>STEM Att.</th>
            <th>STEM Ind.</th>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($series as $s) {
    $ws = $s->wsCount();
    $att = count($s->attendance());
    $avg = $ws > 0 ? ($att / $ws) : '0';
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <th><a href="/R/series/'.$s->id.'">'.$s->title.'</a></th>
            <td>'.$ws.'</td>
            <td>'.$att.'</td>
            <td>'.number_format($avg, 2).'</td>
            <td>'.count($s->individuals()).'</td>
            <td>'.count($s->STEMattendance()).'</td>
            <td>'.count($s->STEMindividuals()).'</td>
        </tr>';
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

@stop
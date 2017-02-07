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

<table class="WSList sortable">
    <thead>
        <tr>
            <td>Series</td>
            <td>WS count</td>
            <td>Yes:No</td>
            <td>Count</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($series as $s) {
    $ws = $s->wsCount();
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td>'.$s->title.'</td>
            <td>'.$ws.'</td>
            <td>'.count($s->fbYes()).':'.count($s->fbNo()).'</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>';
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

@stop
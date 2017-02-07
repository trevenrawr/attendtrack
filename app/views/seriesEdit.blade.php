@extends('layouts.master')
@section('content')


{{ Form::open(array('url' => '/R/series/add/')) }}
    <p>
        <span class="left"><a href="/R/series/">&#8592; Back to Series report</a></span>
    </p>

<fieldset>
    <div>
        <label for="title"><span>*</span>New Series Title</label>
        {{ Form::text('title', '', array('id' => 'title', 'style' => 'width:27em;', 'placeholder' => 'New series title')) }}
    </div>
</fieldset>

{{ Form::submit('Add Series', array('class' => 'submit')) }}
{{ Form::close() }}



<table class="WSList sortable">
    <thead>
        <tr>
            <td>Series</td>
            <td>Workshops</td>
            <td>Remove?</td>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;

foreach ($series as $ser) {
    $ws = $ser->wsCount();
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td>'.$ser->title.'</a></td>
            <td>'.$ws.'</td>
            <td>';
    if ($ws == 0) {
        echo Form::open(array('url' => '/R/series/rem/', 'onsubmit' => 'return confirm(\'Are you sure you want to delete this series?\');', 'class' => 'delete')).Form::hidden('s_id', $ser->id).Form::submit('Remove').Form::close();
    }
    echo '</td>
        </tr>';
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

@stop
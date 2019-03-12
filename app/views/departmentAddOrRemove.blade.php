@extends('layouts.master')
@section('content')


{{ Form::open(array('url' => '/R/dept/add/')) }}
    <p>
        <span class="left"><a href="/R/dept/">&#8592; Back to Department list</a></span>
    </p>

<fieldset>
    <div>
        <label for="title"><span>*</span>New Department Title</label>
        {{ Form::text('title', '', array('id' => 'title', 'style' => 'width:27em;', 'placeholder' => 'New department title')) }}
    </div>
    
    <label for="STEM"><span>*</span>STEM?</label>
    <select name="STEM" id="STEM" autocomplete="off">
        <option value="0">Not STEM</option>
        <option value="1">STEM</option>
    </select>
    
</fieldset>

{{ Form::submit('Add Department', array('class' => 'submit')) }}
{{ Form::close() }}



<table class="WSList sortable">
    <thead>
        <tr>
            <td>Department</td>
            <td>Teachers</td>
            <td>STEM?</td>
            <td>Remove?</td>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;

foreach ($depts as $d) {
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td><a href="/R/dept/'.$d->id.'">'.$d->title.'</a></td>
            <td>'.number_format($d->teachers->count()).'</td>
            <td>'.($d->STEM == 1 ? 'STEM' : '').'</td>
            <td>';
    if ($d->teachers->count() == 0) {
        echo Form::open(array('url' => '/R/dept/rem/', 'onsubmit' => 'return confirm(\'Are you sure you want to delete this department?\');', 'class' => 'delete')).Form::hidden('d_id', $d->id).Form::submit('Remove').Form::close();
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
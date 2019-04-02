@extends('layouts.master')
@section('content')

<p><a href="/R/dept/addOrRemove" class="start">Add or Remove Department</a></p>
<p><a href="/R/dept/edit" class="start">Edit Department</a></p>

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Department</th>
            <th>Teachers</th>
            <th>STEM?</th>
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
        </tr>';
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
{{ $depts->links() }}
@stop
@extends('layouts.master')
@section('content')

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Teacher</th>
            <th>Department</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
<?php
$user = Teacher::find(Session::get('user'));
$i = 0;
foreach ($teachers as $t) {
    echo '
		<tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">';
    if ($user->permissions('tinfo')) {
        echo '
        <td><a href="/T/info/'.$t->id.'">'.$t->name.'</a></td>';
    } else {
        echo '
        <td>'.$t->name.'</td>';
    }
	if($t->department){
		echo '
		<td>'.$t->department->title.'</td>';
	}else{
		echo '
		<td></td>';	//department placeholder incase it's the first login 
	}
            
    echo '<td>'.date_format(date_create($t->created_at), 'n/d/Y - H:i').'</td>
        </tr>';
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
{{ $teachers->links() }}
@stop
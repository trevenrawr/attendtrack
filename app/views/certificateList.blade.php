@extends('layouts.master')
@include('layouts.workshop')
@section('content')

<?php
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;
?>

<p class="message">
    A list of certificates awarded
</p>

<div style="text-align: center;">
    {{ Form::open(array('url' => '/R/cert/')) }}
    Dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    Program: {{ Form::selects('filter_prog', Session::get('filter_prog')) }}
    Certificate: {{ Form::selects('filter_cert', Session::get('filter_cert')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/R/cert/')) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Teacher</th>
            <th>Program</th>
            <th>CCT Status</th>
            <th>as of</th>
            <th>PDC Status</th>
            <th>as of</th>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($tList as $t) {
    if ($user->permissions('tinfo'))
        $tname = '<a href="/T/info/'.$t->id.'">'.$t->name.'</a>';
    else
        $tname = $t->name;
    
    if (!empty($t->CCT_status))
        $CCTdate = date_format(date_create($t->CCT_date), 'n/d/Y');
    else
        $CCTdate = '';
    
    if (!empty($t->PDC_status))
        $PDCdate = date_format(date_create($t->PDC_date), 'n/d/Y');
    else
        $PDCdate = '';
        
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td>'.$tname.'</td>
            <td>'.ucfirst($t->program).'</td>
            <td>'.ucfirst($t->CCT_status).'</td>
            <td>'.$CCTdate.'</td>
            <td>'.ucfirst($t->PDC_status).'</td>
            <td>'.$PDCdate.'</td>
        </tr>';
    $i++;
}

?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

@stop
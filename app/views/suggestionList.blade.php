@extends('layouts.master')
@include('layouts.workshop')
@section('content')


<div style="text-align: center;">
    {{ Form::open(array('url' => '/R/suggestions')) }}
    Series: {{ Form::selects('series_id', Session::get('series_id')) }}<br />
    Semester: {{ Form::selects('semsel', Session::get('semsel')) }}
    or dates from: {{ Form::date('date_start', Session::get('date_start')) }}
    to: {{ Form::date('date_end', Session::get('date_end')) }}<br />
    {{ Form::submit('Filter') }}
    {{ Form::close() }}

    {{ Form::open(array('url' => '/R/suggestions')) }}
    {{ Form::submit('Clear filters') }}
    {{ Form::close() }}
</div>


<table class="WSList sortable">
    <thead>
        <tr>
            <th>Date</th>
            <th>Speaker/Topic Suggestion</th>
        </tr>
    </thead>
    <tbody>
<?php $i = 0; ?>
@foreach($feedback as $fb)
    @if($fb->suggestions != '')
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td>{{ date_format(date_create($fb->updated_at), 'M d, Y g:i') }}</td>
            <td>{{ $fb->suggestions }}</td>
        </tr>
    <?php $i++; ?>
    @endif
@endforeach
    </tbody>
    <tfoot>
    </tfoot>
</table>

{{ $feedback->links() }}

@stop
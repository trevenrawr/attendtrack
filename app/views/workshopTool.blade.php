@extends('layouts.master')
@include('layouts.workshop')
@section('content')

<?php
if (empty($workshop->title)) $workshop->title = Input::old('title');
if (empty($workshop->date)) $workshop->date = Input::old('date');
if (empty($workshop->time)) $workshop->time = Input::old('time');
for ($i = 0; $i < $workshop::NUMPRES; $i++) {
    if (empty($workshop->presenters[$i])) {
        $workshop->presenters[$i] = new Presenter;
        $workshop->presenters[$i]->name = Input::old('presenter'.$i);
    }
}
if (empty($workshop->series_id)) $workshop->series_id = Input::old('series_id');
if (empty($workshop->semester)) $workshop->semester = Input::old('semester');
if (empty($workshop->head_count)) $workshop->head_count = Input::old('head_count');
if (isset($workshop->time)) {
    $time = $workshop->time;
} else {
    $time = '';
}
if ($edit) {
    $dis = array();
    $dtext = '';
} else {
    $dis = array('disabled' => 'disabled');
    $dtext = 'disabled="disabled" ';
}
$dperm = 'disabled="disabled" ';
$user = Teacher::find(Session::get('user'));
if (empty($user)) $user = new Teacher;
?>

{{ Form::open(array('url' => '/WS/update')) }}
{{ Form::hidden('id', $workshop->id) }}
    <p>
        <span class="left"><a href="/WS/list/">&#8592; Back to Workshop list</a></span>
    </p>

<fieldset>
    <div>
        <label for="title"><span>*</span>Workshop Title</label>
        {{ Form::text('title', isset($workshop->title) ? $workshop->title : '', array_merge(array('id' => 'title', 'style' => 'width:27em;', 'placeholder' => 'Workshop title'), $dis)) }}
    </div>
    
    <div id="WSdate">
        <label for="date"><span>*</span>Date</label>
        <input type="date" name="date" value="<?php echo isset($workshop->date) ? $workshop->date : ''; ?>" {{ $dtext }}/>
    </div>
    <div id="WStime">
        <label for="time"><span>*</span>Time</label>
        <input type="time" name="time" id="time" value="{{ $time }}" {{ $dtext }}/>
    </div>
    
    <label for="series_id"><span>*</span>Series</label>
    {{ Form::selects('series_id', isset($workshop->series_id) ? $workshop->series_id : '', $dtext) }}
    
    @if(isset($workshop->id) && $user->permissions('fbinfo'))
    <span class="right"><a href="/FB/list/{{ $workshop->id }}" class="start">Enter Feedback</a></span>
    @endif
    
    <label for="semester"><span>*</span>Semester</label>
    {{ Form::selects('semester', isset($workshop->semester) ? $workshop->semester : '', $dtext) }}
    <label for="presenter0"><span>*</span>Presenter(s)</label>
    {{ Form::presList($workshop->presenters, $workshop::NUMPRES, $dtext) }}
</fieldset>
<fieldset>
    <label for="credits"><span>*</span>Default Credits</label>
    {{ Form::text('credits', isset($workshop->credits) ? $workshop->credits : 1, array_merge(array('id' => 'credits'), $dis)) }}
    <label for="head_count">Head Count</label>
    {{ Form::text('head_count', isset($workshop->head_count) ? $workshop->head_count : '', array_merge(array('id' => 'head_count'), $dis)) }}
</fieldset>

@if($edit)
{{ Form::submit('Submit changes', array('class' => 'submit')) }}
@endif
{{ Form::close() }}

@if(isset($workshop->id))
    <p>
        Attendees: {{ $workshop->demographics->count() }}
        @if($user->permissions('attendance'))
        <span class="right"><a href="{{ '/WS/addAtt/'.$workshop->id }}" class="start">Add new attendee</a></span>
        @endif
    </p>

    @if($workshop->demographics->count() != 0)
        <table class="WSList sortable">
            <thead>
                <tr>
                    <td>Attendee</td>
                    <td>Email</td>
                    <td>Department</td>
                    @if($user->permissions('attendance'))
                    <td>Remove</td>
                    <td>Credits</td>
                    @endif
                </tr>
            </thead>
            <tbody>
        <?php
        $i = 0;
        foreach ($workshop->demographics as $att) {
            $t = Teacher::find($att->teacher_id);
			
            echo '
                <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">';
            
			if($t->id==0){
				echo '
                    <td>'.$att->attendee_name.'</td>';
				echo '
                    <td><a href="mailto:'.$att->attendee_email.'">'.$att->attendee_email.'</a></td>';
				echo '
					<td></td>';	//department placeholder
			}
			else{
				if ($user->permissions('tinfo')) {
					echo '
						<td><a href="/T/info/'.$t->id.'">'.$t->name.'</a></td>';
				} else {
					echo '
						<td>'.$t->name.'</td>';
				}
				echo '
                    <td><a href="mailto:'.$t->email.'">'.$t->email.'</a></td>';
				echo '
					<td>'.$t->department->title.'</td>';
            }
				
            if ($user->permissions('attendance')) {
                echo '
                    <td>'.Form::open(array('url' => '/WS/delAtt/', 'onsubmit' => 'return confirm(\'Are you sure you want to delete this attedance record?\');', 'class' => 'delete')).Form::hidden('att_id', $att->id).Form::submit('Remove').Form::close().'</td>';
				if($t->id!=0){
                    echo '<td>'.Form::open(array('url' => '/WS/attCred/')).Form::hidden('att_id', $att->id).Form::text('credits', $att->credits, array('style' => 'width:1.5em;')).Form::submit('&#x2713;', array('class' => 'creditSubmit')).Form::close().'</td>';
				}
            }
            echo '
                </tr>';
            $i++;
        }
        ?>
            </tbody>
            <tfoot></tfoot>
        </table>
    @endif
@endif

<datalist id="presList"></datalist>

@stop
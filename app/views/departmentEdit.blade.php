@extends('layouts.master')
@include('layouts.teacher')
@section('content')


{{ Form::open(array('url' => '/R/dept/update/')) }}
    <p>
        <span class="left"><a href="/R/dept/">&#8592; Back to Department list</a></span>
    </p>

<fieldset>
    <div>
		<label for="department_id"><span>*</span>Department/Major</label>
        {{ Form::deptList('',array('required' => 'true'))}}
		
        <label for="title"><span>*</span>New Department Title</label>
        {{ Form::text('title', '', array('id' => 'title', 'style' => 'width:27em;', 'placeholder' => 'New department title', 'required' => 'true')) }}
    </div>
    
    <label for="STEM"><span>*</span>Update STEM?</label>
    <select name="STEM" id="STEM" autocomplete="off" required="true">
		<option value disabled selected>Select an Option</option>
        <option value="0">Not STEM</option>
        <option value="1">STEM</option>
    </select>
    
</fieldset>

{{ Form::submit('Edit Department', array('class' => 'submit')) }}
{{ Form::close() }}

@stop
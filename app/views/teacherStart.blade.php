@extends('layouts.master')
@include('layouts.teacher')
@section('content')


@if(isset($workshop))
    <p style="message">Sign in for: <strong>{{ $workshop->title }}</strong></p>
@endif

@if($errors->any())
    <ul>
        <?php echo implode('', $errors->all('<li class="error">:message</li>')); ?>
    </ul>
@endif

{{ Form::open(array('url' => '/T/login')) }}

<fieldset>
    <label for="identikey">Identikey</label>
    {{ Form::text('identikey', '', array('placeholder' => 'identikey')) }}<br />
    <label for="password">Identikey Password</label>
    {{ Form::password('password', array('placeholder' => 'password')) }}
</fieldset>

{{ Form::submit('CU Student/Faculty/Staff', array('class' => 'submit', 'name'=>'usertype')) }}
@if(isset($workshop))
{{ Form::submit('Guest', array('class' => 'submit', 'name'=>'usertype')) }}
@endif
{{ Form::close() }}

@stop
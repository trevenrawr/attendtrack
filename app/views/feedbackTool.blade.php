@extends('layouts.master')
@include('layouts.feedback')
@section('content')

<?php
if ($tview) {
    $stem = '/T/fblist/';
} else {
    $stem = '/FB/list/';
}
$id = Session::get('wsfb_id');
?>

{{ Form::open(array('url' => '/FB/rem/', 'onsubmit' => 'return confirm(\'Are you sure you want to delete this feedback? This cannot be undone.\');', 'class' => 'delete')) }}
{{ Form::hidden('fb_id', $fb->id) }}

<p>
    <span class="left"><a href="{{ $stem.$id }}">&#8592; Back to list</a></span>
@if(!$tview && !empty($fb->id) && Session::has('editFB'))
    <span class="right">
        {{ Form::submit('Delete Feedback') }}
    </span>
@endif
</p>
{{ Form::close() }}

<?php
if (Session::has('editFB')) {
    echo Form::open(array('url' => '/FB/update'));
    Session::flash('editFB', 'true');
} else {
    if (empty($fb->id)) {
        $id = '';
    } else {
        $id = $fb->id;
    }
    echo Form::open(array('url' => '/FB/edit/'.$id));
}
?>

{{ Form::hidden('id', isset($fb->id) ? $fb->id : '') }}
<fieldset>
    {{ Form::rating('WS', isset($fb->workshop_rating) ? $fb->workshop_rating : 0) }}
    {{ Form::rating('Presenter', isset($fb->presenter_rating) ? $fb->presenter_rating : 0) }}
</fieldset>

<fieldset>
    {{ Form::longAnswer('most_helpful', isset($fb->most_helpful) ? $fb->most_helpful : '') }}
    {{ Form::longAnswer('least_helpful', isset($fb->least_helpful) ? $fb->least_helpful : '') }}
    {{ Form::longAnswer('improve', isset($fb->improve) ? $fb->improve : '') }}
</fieldset>

<fieldset>
    <label for="recommend">Would you recommend this workshop/session to a friend?  Why or why not?</label>
    {{ Form::yesNoWhy('recommend', isset($fb->recommend) ? $fb->recommend : '', isset($fb->recommend_why) ? $fb->recommend_why : '') }}

    {{ Form::longAnswer('suggestions', isset($fb->suggestions) ? $fb->suggestions : '') }}

    <label for="referred">Where did you hear about this workshop?</label>
    {{ Form::referred($fb) }}
</fieldset>


<p>
    <span class="left"><a href="{{ $stem.$id }}">&#8592; Back to list</a></span>
</p>

<?php
if (!$tview) {
    if (Session::has('editFB')) {
        echo Form::submit('Save feedback!', array('class' => 'submit'));
    } else {
        echo Form::submit('Edit feedback!', array('class' => 'submit'));
    }
}
?>

{{ Form::close() }}

@stop
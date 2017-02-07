@extends('layouts.master')
@section('content')

<?php
$ws = Workshop::find(Session::get('wsfb_id'));
?>
<p class="message">
    Feedback for: <strong>{{ $ws->title }}</strong><br />
     Presented by: <strong>{{ $ws->presenters[0]->name }}</strong><!--
--><?php
    $isFirst = true;
    foreach($ws->presenters as $pres) {
        if ($isFirst) {
            $isFirst = false;
            continue;
        }
        echo ', <strong>'.$pres->name.'</strong>';
    }
?>
    <br />
    on <strong>{{ date_format(date_create($ws->date), 'M d, Y') }}</strong>
</p>

@if(!$tview)
<p class="message"><a href="/FB/edit/" class="start">Enter new Feedback</a></p>
@endif

<table class="WSList">
    <thead>
        <tr>
            <th>Feedback Entered<br /></th>
            <th>Overall Rating</th>
            <th>Presenter Rating</th>
            @if(!$tview)
            <th>Edit</th>
            @endif
        </tr>
    </thead>
    <tbody>
<?php
if ($tview) {
    $stem = '/T/fbinfo/';
} else {
    $stem = '/FB/info/';
}

$i = 0;
foreach ($feedback as $fb) {    
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td><a href="'.$stem.$fb->id.'">'.date_format(date_create($fb->updated_at), 'M d, Y g:i').'</a></td>
            <td>'.$fb->workshop_rating.'</td>
            <td>'.$fb->presenter_rating.'</td>';
    if (!$tview) {
        echo '
            <td><a href="/FB/edit/'.$fb->id.'">Edit</a></td>';
    }
       echo '
        </tr>';
    $i++;
}

?>
    </tbody>
    <tfoot>
        <tr>
            <td>Averages:</td>
            <td>{{ Feedback::WSAvg(Session::get('wsfb_id')) }}</td>
            <td>{{ Feedback::PresAvg(Session::get('wsfb_id')) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@if(count($feedback) > 0)
<table class="WSList">
    <thead></thead>
    <tbody>
        <tr class="question">
            <td colspan="3">What was the most helpful part of the workshop/session?</td>
        </tr>
        <?php $i = 1; ?>
        @foreach($feedback as $fb)
        <tr class="<?php if ($i % 2 == 0) echo 'oddRow'; else echo 'evenRow'; ?>">
            <td class="anonStud">Teacher {{ $i++ }}</td>
            <td colspan="2">{{ $fb->most_helpful }}</td>
        <tr>
        @endforeach
        <tr class="question">
            <td colspan="3">What was the least helpful part of this workshop/session?</td>
        </tr>
        <?php $i = 1; ?>
        @foreach($feedback as $fb)
        <tr class="<?php if ($i % 2 == 0) echo 'oddRow'; else echo 'evenRow'; ?>">
            <td>Teacher {{ $i++ }}</td>
            <td colspan="2">{{ $fb->least_helpful }}</td>
        </tr>
        @endforeach
        <tr class="question">
            <td colspan="3">What teaching and learning activities do you plan to improve due to this workshop/session?</td>
        </tr>
        <?php $i = 1; ?>
        @foreach($feedback as $fb)
        <tr class="<?php if ($i % 2 == 0) echo 'oddRow'; else echo 'evenRow'; ?>">
            <td>Teacher {{ $i++ }}</td>
            <td colspan="2">{{ $fb->improve }}</td>
        </tr>
        @endforeach
        <tr class="question">
            <td colspan="3">Would you recommend this workshop/session to a friend?  Why or why not?</td>
        </tr>
        <?php $i = 1; ?>
        @foreach($feedback as $fb)
        <tr class="<?php if ($i % 2 == 0) echo 'oddRow'; else echo 'evenRow'; ?>">
            <td>Teacher {{ $i++ }}</td>
            <td class="yorn">{{ ucfirst($fb->recommend) }}</td>
            <td>{{ $fb->recommend_why }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<p>
    @if(!$tview)
    <span class="left"><a href="/WS/info/{{ $ws->id }}">&#8592; Back to Workshop</a></span>
    @else
    <span class="left"><a href="/T/info/">&#8592; Back to Teacher Info</a></span>
    @endif
</p>

{{ $feedback->links() }}

@stop
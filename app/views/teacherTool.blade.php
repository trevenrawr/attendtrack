@extends('layouts.master')
@include('layouts.teacher')
@section('content')

<?php
    if ($edit) {
        $dis = array();
        $dtext = '';
    } else {
        $dis = array('disabled' => 'disabled', 'readOnly' => 'true');
        $dtext = 'disabled="disabled" readOnly="true"';
    }
    $dperm = 'disabled="disabled" readOnly="true"';
?>

{{ Form::open(array('url' => '/T/update', 'files' => 'true')) }}
{{ Form::hidden('identikey', $teacher->identikey) }}
<div class="dontBreak">
    <h2 class="section">Demographic Information:</h2>
    <hr />
    <fieldset>
        <label for="name"><span>*</span>Full Name</label>
        {{ Form::text('name', isset($teacher->name) ? $teacher->name : '', array('id' => 'name', 'placeholder' => 'name', 'required' => 'true')) }}<br />

        <label for="email"><span>*</span>Email</label>
        {{ Form::email('email', isset($teacher->email) ? $teacher->email : '', array('id' => 'email', 'style' => 'width: 18.9em;', 'placeholder' => 'you@colorado.edu', 'required' => 'true')) }}
    </fieldset>

    <fieldset>
        <label for="department_id"><span>*</span>Department/Major</label>
        {{ Form::deptList(isset($teacher->department_id) ? $teacher->department_id : '') }}<br />
        <label for="program"><span>*</span>Affiliation</label>
        {{ Form::choices('program', isset($teacher->program) ? $teacher->program : '') }}
        <label for="affiliation"><span>*</span>Position</label>
        {{ Form::choices('affiliation', isset($teacher->affiliation) ? $teacher->affiliation : '') }}
        <label for="year"><span>*</span>Year in Grad School</label>
        {{ Form::text('year', isset($teacher->year) ? $teacher->year : '', array('id' => 'year', 'style' => 'width: 3em;', 'required' => 'true')) }}
    </fieldset>

    <fieldset>
        <label for="gender">Gender</label>
        {{ Form::choices('gender', isset($teacher->gender) ? $teacher-> gender : '') }}
        <label for="international">International</label>
        {{ Form::choices('international', isset($teacher->international) ? $teacher->international : '') }}
    </fieldset>

    @if($su)
    <fieldset id="permsBox">
        <label for="permissions">Permissions</label>
        {{ Form::permissions($teacher) }}
    </fieldset>
    @endif

    <?php
        if (Session::has('workshop_id')) {
            $butt = 'Save and Confirm Attendance';
        } else {
            if (Session::get('user') != $teacher->id || !Session::has('workshop_id')) {
                $butt = 'Save';
            } else {
                $butt = 'Save and Logout';
            }
        }
        echo Form::submit($butt, array('class' => 'submit'));
    ?>
</div>

@if($full)

<datalist id="names">
</datalist>


<!-- --------------------------------------------------------------------------------------------- -->
<div class="dontBreak">
    <h2 class="section">Workshop Attendance Information:</h2>
    <hr />
    <?php
        $creds = 0;
        foreach ($teacher->attendance as $att) {
            $creds += $att->credits;
        }
    ?>
        <p>
            Workshops attended: <strong>{{ $teacher->workshops->count() }}</strong> &mdash; credits earned: <strong>{{ $creds }}</strong>
        </p>

    @if($teacher->workshops->count() != 0)
        <table class="WSList sortable">
            <thead>
                <tr>
                    <th scope="col">Workshop Title</th>
                    <th scope="col">Presenter</th>
                    <th scope="col">Date</th>
                    <th scope="col">Credits</th>
                </tr>
            </thead>
            <tbody>
        <?php
            $i = 0;
            foreach ($teacher->workshops as $ws) {
                $att = Attendance::where('workshop_id', '=', $ws->id)->where('teacher_id', '=', $teacher->id)->first();
                echo '
                <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
                    <td scope="row">'.$ws->title.'</td>
                    <td>'.$ws->presenters[0]->name.'</td>
                    <td>'.date_format(date_create($ws->date), 'n/d/Y').'</td>
                    <td>'.$att->credits.'</td>
                </tr>';
                $i++;
            }

        ?>
            </tbody>
        </table>
    @endif
</div>


<!-- --------------------------------------------------------------------------------------------- -->
<div class="dontBreak">
    @if($teacher->presentations->count() != 0 && !$su)
    <h2 class="section">Workshop Presentation Information:</h2>
    <hr />

        <p>Workshops presented: <strong>{{ $teacher->presentations->count() }}</strong></p>

        <table class="WSList sortable">
            <thead>
                <tr>
                    <th scope="col">Workshop Title</th>
                    <th scope="col">Date</th>
                    <th scope="col">Att.</th>
                    <th scope="col">Overall<br />Rating</th>
                    <th scope="col">Presenter<br />Rating</th>
                </tr>
            </thead>
            <tbody>
        <?php
            $i = 0;
            foreach ($teacher->presentations as $pres) {
                echo '
                <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
                    <td scope="row"><a href="/T/fblist/'.$pres->id.'">'.$pres->title.'</a></td>
                    <td>'.date_format(date_create($pres->date), 'n/d/Y').'</td>
                    <td>'.$pres->attendees->count().'</td>
                    <td>'.Feedback::WSAvg($pres->id).'</td>
                    <td>'.Feedback::PresAvg($pres->id).'</td>
                </tr>';
                $i++;
            }

        ?>
            </tbody>
        </table>
    @endif
</div>



<!-- --------------------------------------------------------------------------------------------- -->
<div class="dontBreak">
    <h2 class="section"><a id="CCT"></a>Certificate in College Teaching Information:</h2>
    <hr />

    <fieldset>
        <label for="status">Status</label>
        {{ Form::status('CCT_status', $teacher, $dtext) }}
        as of {{ Form::date('CCT_date', $teacher, $dperm) }}
    </fieldset>

    <fieldset>
        <label for="firstVTCdate">First VTC</label>
        Peformed by
        <?php
            if (!empty($teacher->firstVTCer)) {
                $fVTC = Teacher::find($teacher->firstVTCer);
                $firstVTCer = $fVTC->name;
            } else {
                $firstVTCer = '';
            }
            $teacher->firstVTCer = $firstVTCer;
            echo Form::who('firstVTCer', $teacher, $dtext);
        ?>
        on {{ Form::date('firstVTCdate', $teacher, $dtext) }}
        @if($edit)
            <br />
            with notes: {{ Form::file('firstVTCnotes') }}
        @endif
        @if(!empty($teacher->firstVTCnotes))
            (<a target="_blank" href="/T/notes/{{ $teacher->id }}/1" class="start">First VTC notes</a>)<br />
        @endif

        <label for="secondVTCdate">Second VTC</label>
        Performed by 
        <?php
            if (!empty($teacher->secondVTCer)) {
                $sVTC = Teacher::find($teacher->secondVTCer);
                $secondVTCer = $sVTC->name;
            } else {
                $secondVTCer = '';
            }
            $teacher->secondVTCer = $secondVTCer;
            echo Form::who('secondVTCer', $teacher, $dtext);
        ?>
        on {{ Form::date('secondVTCdate', $teacher, $dtext) }}
        @if($edit)
            <br />
            with notes: {{ Form::file('secondVTCnotes') }}
        @endif
        @if(!empty($teacher->secondVTCnotes))
            (<a target="_blank" href="/T/notes/{{ $teacher->id }}/2" class="start">Second VTC notes</a>)
        @endif

        <label for="CCT_kolb_quad">Kolb LSI</label>
        {{ Form::status('CCT_kolb_quad', $teacher, $dtext) }}
        as of {{ Form::date('CCT_kolb_date', $teacher, $dtext) }}
        administered by {{ Form::who('CCT_kolb_who', $teacher, $dtext) }}

        <label for="CCT_wing_date">Wingspread</label>
        Administered by {{ Form::who('CCT_wing_who', $teacher, $dtext) }}
        on {{ Form::date('CCT_wing_date', $teacher, $dtext) }}
    </fieldset>

    <fieldset>
        <label for="CCT_disc_spec">Discipline-Specific Hours</label>
            {{ Form::text('CCT_disc_spec', isset($teacher->CCT_disc_spec) ? $teacher->CCT_disc_spec : '', $dis) }}
            signed off by {{ Form::who('CCT_disc_spec_who', $teacher, $dtext) }}

        <label for="CCT_obser_date">Classroom Observation</label>
            Observed by {{ Form::who('CCT_obser_who', $teacher, $dtext) }}
            on {{ Form::date('CCT_obser_date', $teacher, $dtext) }}

        <label for="CCT_depteval_date">Department Evaluation</label>
            Evaluated by {{ Form::who('CCT_depteval_who', $teacher, $dtext) }}
            on {{ Form::date('CCT_depteval_date', $teacher, $dtext) }}
    </fieldset>

    <fieldset>
        <label for="CCT_port_status">Teaching Portfolio</label>
        {{ Form::status('CCT_port_status', $teacher, $dtext) }}
        as of {{ Form::date('CCT_port_date', $teacher, $dperm) }}

        <label for="CCT_survey_status">Exit Survey</label>
        {{ Form::status('CCT_survey_status', $teacher, $dtext) }}
        as of {{ Form::date('CCT_survey_date', $teacher, $dperm) }}
    </fieldset>

    @if($edit)

    <fieldset>
        <label for="CCT_notes">Office notes (not visible to teachers)</label>
        {{ Form::longAnswer('CCT_notes', $teacher->CCT_notes) }}
    </fieldset>

    {{ Form::submit($butt, array('class' => 'submit')) }}
    @endif
</div>



<!-- --------------------------------------------------------------------------------------------- -->
<div class="dontBreak">
    <h2 class="section">Professional Development Certificate Information:</h2>
    <hr />

    <fieldset>
        <label for="PDC_status">Status</label>
        {{ Form::status('PDC_status', $teacher, $dtext) }}
        as of {{ Form::date('PDC_date', $teacher, $dperm) }}
    </fieldset>

    <fieldset>
        <label for="PDC_CV_status">CV/Resume</label>
        {{ Form::status('PDC_CV_status', $teacher, $dtext) }}
        as of {{ Form::date('PDC_CV_date', $teacher, $dperm) }}

        <label for="PDC_plan_status">Plan</label>
        {{ Form::status('PDC_plan_status', $teacher, $dtext) }}
        as of {{ Form::date('PDC_plan_date', $teacher, $dperm) }}
    </fieldset>

    <fieldset>
        <label for="PDC_mentor_hrs">Mentorship Hours</label>
        {{ Form::text('PDC_mentor_hrs', isset($teacher->PDC_mentor_hrs) ? $teacher->PDC_mentor_hrs : '', $dis) }}
        signed off by {{ Form::who('PDC_mentor_who', $teacher, $dtext) }}

        <label for="PDC_eval_date">Mentorship Evaluation</label>
        Performed by {{ Form::who('PDC_eval_who', $teacher, $dtext) }}
        on {{ Form::date('PDC_eval_date', $teacher, $dtext) }}
    </fieldset>

    <fieldset>
        <label for="PDC_visit_where">Site visit</label>
        Visited {{ Form::text('PDC_visit_where', isset($teacher->PDC_visit_where) ? $teacher->PDC_visit_where : '', $dis) }}
        on {{ Form::date('PDC_visit_date', $teacher, $dtext) }}

        <label for="PDC_pres_title">Colloquium/Presentation</label>
        Titled {{ Form::text('PDC_pres_title', isset($teacher->PDC_pres_title) ? $teacher->PDC_pres_title : '', $dis) }}
        on {{ Form::date('PDC_pres_date', $teacher, $dtext) }}
    </fieldset>

    <fieldset>
        <label for="PDC_port_status">Socratic Portfolio</label>
        {{ Form::status('PDC_port_status', $teacher, $dtext) }}
        as of {{ Form::date('PDC_port_date', $teacher, $dperm) }}

        <label for="PDC_survey_status">Exit Survey</label>
        {{ Form::status('PDC_survey_status', $teacher, $dtext) }}
        as of {{ Form::date('PDC_survey_date', $teacher, $dperm) }}
    </fieldset>



    @if($edit)
    <fieldset>
        <label for="PDC_notes">Office notes (not visible to teachers)</label>
        {{ Form::longAnswer('PDC_notes', $teacher->PDC_notes) }}
    </fieldset>

    {{ Form::submit($butt, array('class' => 'submit')) }}
    @endif
</div>


@endif

{{ Form::close() }}
        
@stop
@extends('layouts.master')
@section('content')

<table class="WSList sortable">
    <thead>
        <tr>
            <th>Time</th>
            <th>IP</th>
            <th>Name</th>
            <th>Action</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
<?php
$i = 0;
foreach ($aList as $act) {
    $name = $act->teacher->name;
    
    preg_match('/[A-Z]{2}/', $act->note, $target);
    $target = !empty($target[0]) ? $target[0] : '';
    
    preg_match('/[0-9]{1,}/', $act->note, $id);
    $id = !empty($id[0]) ? $id[0] : '';
    
    switch ($target) {
        case 'AT':
            // ATtendance
            $item = Attendance::find($id);
            if (!empty($item))
                $notes = $item->teacher->name.' attended WS #'.$item->workshop_id;
            else
                $notes = '(Attendance #'.$id.' was deleted)';
            break;
        case 'SA':
            // Start Attendance
            $notes = 'Started attendance for WS #'.$id;
            break;
        case 'EA':
            // End Attendance
            $notes = 'Ended attendance for WS #'.$id;
            break;
        case 'TD':
            // Teacher - Demographics
            $item = Teacher::find($id);
            if (!empty($item))
                $notes = $item->name.'\'s demographics';
            else
                $notes = '(Teacher #'.$id.' has gone missing)';
            break;
        case 'TP':
            // Teacher - Permissions
            $item = Teacher::find($id);
            if (!empty($item))
                $notes = $item->name.'\'s permissions';
            else
                $notes = '(Teacher #'.$id.' has gone missing)';
            break;
        case 'TC':
            // Teacher - Certification
            $item = Teacher::find($id);
            if (!empty($item))
                $notes = $item->name.'\'s cert info';
            else
                $notes = '(Teacher #'.$id.' has gone missing)';
            break;
        case 'WS':
            // WorkShops
            $item = Workshop::find($id);
            if (!empty($item))
                $notes = 'WS "'.$item->title.'" (id='.$id.')';
            else
                $notes = '(Workshop #'.$id.' has gone missing)';
            break;
        case 'FB':
            // FeedBack
            $item = Feedback::find($id);
            if (!empty($item))
                $notes = 'Feedback for WS #'.$item->workshop_id;
            else
                $notes = '(Feedback #'.$id.' was deleted)';
            break;
        case 'DP':
            // DePartment
            $item = Department::find($id);
            if (!empty($item))
                $notes = 'Department: '.$item->title;
            else
                $notes = '(Department #'.$id.' was deleted)';
            break;
        case 'SE':
            // SEries
            $item = Series::find($id);
            if (!empty($item))
                $notes = 'Series: '.$item->title;
            else
                $notes = '(Series #'.$id.' was deleted)';
            break;
        default:
            $notes = $target.' '.$id;
    }
    
    echo '
        <tr class="'.((($i % 2) == 0) ? 'evenRow' : 'oddRow').'">
            <td>'.date_format(date_create($act->created_at), 'n/d/Y - H:i').'</td>
            <td>'.$act->ip.'</a></td>
            <td>'.$name.'</td>
            <td>'.$act->action.'</td>
            <td>'.$notes.'</td>
        </tr>';
    $i++;
}
?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

{{ $aList->links() }}

@stop
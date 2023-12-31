@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.my_attendance_report')
@endsection
<style>
    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }

    /*
  tbody {
   display:block;
   height:500px;
   overflow:auto;
  }
  thead, tbody tr {
   display:table;
   width:100%;
   table-layout:fixed;
  }
  thead {
   width: calc( 100% - 1em )
  }*/
</style>
<script>
    jQuery(function() {
        $("#monthlyAttendance").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'myAttendanceReport.myAttendanceReport', 'id' => 'monthlyAttendance']) }}
                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">@lang('common.employee')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}">{{ $value->first_name }}
                                                    {{ $value->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.from_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="@lang('common.to_date')" name="to_date"
                                            value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        <h4 class="text-right" hidden>
                            @if (isset($from_date))
                                @if (count($results) > 0)
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ URL('downloadMyAttendance/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date) }}" hidden><i
                                            class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                        PDF</a>
                                @endif
                            @else
                                @if (count($results) > 0)
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ URL('downloadMyAttendance/?employee_id=' . session('logged_session_data.employee_id') . '&from_date=' . dateConvertDBtoForm(date('Y-m-01')) . '&to_date=' . dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01'))))) }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true" hidden></i> @lang('common.download')
                                        PDF</a>
                                @endif
                            @endif
                        </h4>
                        <div class="table-responsive">
                            <table id="" class="table table-bordered" style="font-size: 12px">
                                <thead class="tr_header">
                                    <tr>
                                        <th style="width:100px;">@lang('common.serial')</th>
                                        <th>@lang('common.date')</th>
                                        <th>@lang('common.employee_name')</th>
                                        <th>@lang('common.id')</th>
                                        <th>@lang('attendance.in_time')</th>
                                        <th>@lang('attendance.out_time')</th>
                                        <th>@lang('attendance.working_time')</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($results) > 0)
                                        <?php
                                        $totalPresent = 0;
                                        $totalAbsence = 0;
                                        $totalLeave = 0;
                                        $totalLate = 0;
                                        $totalHour = 0;
                                        $totalMinit = 0;
                                        ?>

                                        {{ $serial = null }}
                                        @forelse ($results as $value)
                                            <tr>
                                                <td style="width:100px;">{{ ++$serial }}</td>
                                                <td>{{ $value['date'] }}</td>
                                                <td>{{ $value['fullName'] }}</td>
                                                <td>{{ $value['finger_print_id'] }}</td>
                                                <td>
                                                    <?php
                                                    // dd($value);
                                                    if ($value['in_time'] != '') {
                                                        echo $value['in_time'];
                                                    } else {
                                                        echo '--';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($value['out_time'] != '') {
                                                        echo $value['out_time'];
                                                    } else {
                                                        echo '--';
                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    if ($value['working_time'] == '') {
                                                        echo '--';
                                                    } else {
                                                        if ($value['working_time'] != '00:00:00') {
                                                            echo $d = date('H:i', strtotime($value['working_time']));
                                                    
                                                            $hour_minit = explode(':', $d);
                                                    
                                                            $totalHour += $hour_minit[0];
                                                            $totalMinit += $hour_minit[1];
                                                        } else {
                                                            echo 'One Time Punch';
                                                        }
                                                    }
                                                    
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($value['action'] == 'Absence') {
                                                        echo "<span class='label label-danger'>" . __('common.absence') . '</span>';
                                                        $totalAbsence += 1;
                                                    } elseif ($value['action'] == 'Leave') {
                                                        echo "<span class='label label-info'>" . __('common.leave') . '</span></p>';
                                                        $totalLeave += 1;
                                                    } else {
                                                        echo "<span class='label label-success'>" . __('common.present') . '</span>';
                                                        $totalPresent += 1;
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9">@lang('common.no_data_available') !</td>
                                            </tr>
                                        @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

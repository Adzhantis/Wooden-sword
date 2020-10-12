@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Groups Page</h1>
@stop

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Auto Groups</h3>
                    <a href="{{ \Illuminate\Support\Facades\URL::to('adminpanel/groups/reset-counters') }}"
                    style="float: right" class="btn btn-warning">
                        Reset Counters
                    </a>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 10px">Group</th>
                                <th>Weight</th>
                                <th>%</th>
                                <th>Players</th>
                                <th>Players %</th>
                            </tr>

                            @foreach($autoGroupsState->autoGroups as $groupId => $groupStateData)
                                <tr>
                                    <td style="width: 40%">{{ $groupLabels[(string)$groupId] }}</td>
                                    <td>{{ $groupStateData->weight }}</td>
                                    <td>{{ $groupStateData->weightPercent }} %</td>
                                    <td>{{ $groupStateData->countPlayer }}</td>
                                    <td>{{ $groupStateData->countPlayerPercent }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td style="font-weight: bold;">Total</td>
                                <td>{{ $autoGroupsState->total->weight }}</td>
                                <td></td>
                                <td>{{ $autoGroupsState->total->countPlayer }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="center" style="font-size: 20px">EDIT</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->

            </div>
            <!-- /.box -->

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Groups</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 10px">Id</th>
                                <th>Label</th>
                                <th>Created Date</th>
                                <th>Update Date</th>
                            </tr>

                            @foreach($groups as $group)
                                <tr>
                                    <td>{{ $group->id }}</td>
                                    <td @if (property_exists($autoGroupsState->autoGroups, $group->id))
                                        style="color:red"
                                    @endif>{{ $group->label }}</td>
                                    <td>{{ $group->created_at }}</td>
                                    <td>{{ $group->updated_at }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->

            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>

@stop
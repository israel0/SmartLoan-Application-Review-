@extends('layouts.master')
@section('title')
    {{ trans_choice('general.user',2) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.user',2) }}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('users.create'))
                    <a href="{{ url('user/create') }}" class="btn btn-warning btn-xs">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table  table-striped table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans('general.phone') }}</th>
                    <th>{{ trans_choice('general.email',1) }}</th>
                    <th>{{ trans_choice('general.role',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->first_name }} {{ $key->last_name }}</td>
                        <td>{{ $key->phone }}</td>
                        <td>{{ $key->email }}</td>
                        <td>
                            @if(!empty($key->roles))
                                @if(!empty( $key->roles->first()))
                                    <span class="label label-success">{{ $key->roles->first()->name }} </span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if(Sentinel::hasAccess('users.view'))
                                <a href="{{ url('user/'.$key->id.'/show') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @endif
                            @if(Sentinel::hasAccess('users.update'))
                                <a href="{{ url('user/'.$key->id.'/edit') }}"  class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endif
                            @if(Sentinel::hasAccess('users.delete'))
                                <a href="{{ url('user/'.$key->id.'/delete') }}" class="delete btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')

    <script>

        $('#data-table').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [6]}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });
    </script>
@endsection

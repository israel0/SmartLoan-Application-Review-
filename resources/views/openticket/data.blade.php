@extends('layouts.master')
@section('title')
    {{trans_choice('general.openticket',2)}}  
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.openticket',2)}} </h6>
            <div class="heading-elements">
                @if(Sentinel::hasAccess('savings.create'))
                    <span data-toggle="modal" data-target="#openTicket"
                        class="btn btn-warning btn-sm"> {{trans_choice('general.new-ticket',2)}} </span>
                @endif
            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                     
                        <th> Ticket ID </th>
                        <th> Sender </th>
                        <th> Subject </th>
                        <th> Message </th>
                        <th>Department</th>
                        <th>Priority</th>
                        <th> Time </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->ticket_id }}</td>
                            <td>
                                {{ App\Models\User::where("id" , $key->user_id)->first()->first_name . " " . App\Models\User::where("id" , $key->user_id)->first()->last_name }}
                            </td>
                            <td>
                               {{ $key->subject}}
                            </td>
                            <td>
                                {{ $key->message}}
                             </td>
                            <td>{{ $key->department }}</td>
                            <td> {{ $key->priority }} </td>
                            <td> <span style="color:green">{{ $key->created_at->diffForHumans() }}</span>  </td>
        
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [4]}
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

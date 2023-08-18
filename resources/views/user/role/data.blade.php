@extends('layouts.master')
@section('title')
    {{trans_choice('general.role',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.role',2)}}</h6>

            <div class="heading-elements">
                <a href="{{ url('user/role/create') }}" class="btn btn-warning btn-xs">
                    {{trans_choice('general.add',1)}} {{trans_choice('general.role',1)}}
                </a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table responsive table-hover table-stripped" id="">
                <thead>
                    <tr>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans('general.slug')}}</th>
                        <th>{{trans_choice('general.action',1)}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->name }}</td>
                            <td>{{ $key->slug}}</td>
                            <td>
                                <a href="{{ url('user/role/'.$key->id.'/edit') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                @if($key->id!=1)
                                <a href="{{ url('user/role/'.$key->id.'/delete') }}" class="delete btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

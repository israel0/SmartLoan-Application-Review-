@extends('layouts.master')
@section('title')
    {{trans_choice('general.loan',1)}}  {{trans_choice('general.list',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.loan',1)}}  {{trans_choice('general.list',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>
            <h4>
                @if(!empty($status))
                    Status: <b>{{$status}}</b>
                @endif
            </h4>

            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
            </div>
        </div>
        <div class="panel-body hidden-print">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-4">
                    {!! Form::text('start_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"From Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-4">
                    {!! Form::text('end_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"To Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-4">
                    {!! Form::select('status',array('all'=>'All','pending'=>'Pending','approved'=>'Approved','disbursed'=>'Disbursed','declined'=>'Declined','withdrawn'=>'Withdrawn','written_off'=>'Written off','closed'=>'Closed','rescheduled'=>'Rescheduled'),'all', array('class' => 'form-control select2', 'placeholder'=>"Select Status",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">{{trans_choice('general.search',1)}}!
                          </button>
                        </span>
                        <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    <div class="panel panel-white">
        <div class="panel-body table-responsive no-padding">
            <table id="data-table" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('general.borrower',1)}}</th>
                    <th>#</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.released',1)}}</th>
                    <th>{{trans_choice('general.due',1)}}</th>
                    <th>{{trans_choice('general.paid',1)}}</th>
                    <th>{{trans_choice('general.balance',1)}}</th>
                    <th>{{trans_choice('general.status',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $principal = 0;
                $balance = 0;
                $due = 0;
                $paid = 0;
                ?>
                @foreach($data as $key)
                    <?php
                    $principal = $principal + $key->principal;
                    $balance = $balance + \App\Helpers\GeneralHelper::loan_total_balance($key->id);
                    $paid = $paid + \App\Helpers\GeneralHelper::loan_total_paid($key->id);
                    ?>
                    <tr>
                        <td>
                            @if(!empty($key->borrower))
                                <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                            {{ $key->name }}
                        </td>
                        <td>{{$key->id}}</td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($key->principal,2)}}
                            @else
                                {{number_format($key->principal,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif

                        </td>
                        <td>{{$key->release_date}}</td>

                        <td>
                            @if($key->override==1)
                                <?php   $due = $due + $key->balance; ?>
                                <s>{{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($key->id),2)}}</s><br>
                                {{number_format($key->balance,2)}}
                            @else
                                <?php   $due = $due + \App\Helpers\GeneralHelper::loan_total_due_amount($key->id); ?>
                                {{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($key->id),2)}}
                            @endif

                        </td>
                        <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_paid($key->id),2)}}</td>
                        <td>
                            {{number_format(\App\Helpers\GeneralHelper::loan_total_balance($key->id),2)}}
                        </td>
                        <td>
                            @if($key->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($key->id)>0)
                                <span class="label label-danger">{{trans_choice('general.past_maturity',1)}}</span>
                            @else
                                @if($key->status=='pending')
                                    <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}</span>
                                @endif
                                @if($key->status=='approved')
                                    <span class="label label-info">{{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}</span>
                                @endif
                                @if($key->status=='disbursed')
                                    <span class="label label-info">{{trans_choice('general.active',1)}}</span>
                                @endif
                                @if($key->status=='declined')
                                    <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                @endif
                                @if($key->status=='withdrawn')
                                    <span class="label label-danger">{{trans_choice('general.withdrawn',1)}}</span>
                                @endif
                                @if($key->status=='written_off')
                                    <span class="label label-danger">{{trans_choice('general.written_off',1)}}</span>
                                @endif
                                @if($key->status=='closed')
                                    <span class="label label-success">{{trans_choice('general.closed',1)}}</span>
                                @endif
                                @if($key->status=='pending_reschedule')
                                    <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.reschedule',1)}}</span>
                                @endif
                                @if($key->status=='rescheduled')
                                    <span class="label label-info">{{trans_choice('general.rescheduled',1)}}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('general.choose') }} <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('loans.view'))
                                        <li><a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                            </a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.create'))
                                        <li><a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.delete'))
                                        <li><a href="{{ url('loan/'.$key->id.'/delete') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($principal,2)}}</b>
                        @else
                            <b>{{number_format($principal,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td></td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($due,2)}}</b>
                        @else
                            <b>{{number_format($due,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($paid,2)}}</b>
                        @else
                            <b> {{number_format($paid,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($balance,2)}}</b>
                        @else
                        <b> {{number_format($balance,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
@endsection
@section('footer-scripts')

@endsection

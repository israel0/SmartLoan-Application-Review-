@extends('layouts.master')
@section('title')
    {{ trans('general.dashboard') }}
@endsection
@section('content')
    <div class="row">
        @if(Sentinel::hasAccess('dashboard.registered_borrowers'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-primary has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            <h3 class="no-margin">{{ \App\Models\Borrower::count() }}</h3>
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.total',1) }} {{ trans_choice('general.borrower',2) }}</span>
                        </div>

                        <div class="media-right media-middle">
                            <i class="icon-users4 icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Sentinel::hasAccess('dashboard.total_loans_released'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-warning has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::loans_total_principal(),2) }} </h3>
                            @else
                                <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::loans_total_principal(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                            @endif
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.loan',2) }} {{ trans_choice('general.released',1) }}</span>
                        </div>
                        <div class="media-right media-middle">
                            <i class="icon-drawer-out icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Sentinel::hasAccess('dashboard.total_collections'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-success has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::loans_total_paid(),2) }} </h3>
                            @else
                                <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::loans_total_paid(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                            @endif
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.payment',2) }}</span>
                        </div>
                        <div class="media-right media-middle">
                            <i class="icon-enter6 icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-danger has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::loans_total_due(),2) }} </h3>
                            @else
                                <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::loans_total_due(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                            @endif
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.due',1) }} {{ trans_choice('general.amount',1) }}</span>
                        </div>
                        <div class="media-right media-middle">
                            <i class="icon-pen-minus icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
            <div class="col-md-4">
                <div class="panel panel-flat">
                    <div class="panel-body">

                        <canvas id="loan_status_pie" height="300"></canvas>
                        <div class="list-group no-border no-padding-top">
                            @foreach(json_decode($loan_statuses) as $key)
                                <a href="{{$key->link}}" class="list-group-item">
                                    <span class="badge bg-{{$key->class}} pull-right">{{$key->value}}</span>
                                    {{$key->label}}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-8">
        @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
            <!-- Sales stats -->
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title">{{ trans_choice('general.collection',1) }} {{ trans_choice('general.statistic',2) }}</h6>
                        <div class="heading-elements">
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                        $target = 0;
                        foreach (\App\Models\LoanSchedule::where('year', date("Y"))->where('month',
                            date("m"))->get() as $key) {
                            $target = $target + $key->principal + $key->interest + $key->fees + $key->penalty;
                        }
                        $paid_this_month = \App\Models\LoanTransaction::where('transaction_type',
                            'repayment')->where('reversed', 0)->where('year', date("Y"))->where('month',
                            date("m"))->sum('credit');
                        if ($target > 0) {
                            $percent = round(($paid_this_month / $target) * 100);
                        } else {
                            $percent = 0;
                        }

                        ?>
                        <div class="container-fluid">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="content-group">
                                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                            <h5 class="text-semibold no-margin">{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->where('date',date("Y-m-d"))->sum('credit'),2) }}  </h5>
                                        @else
                                            <h5 class="text-semibold no-margin">{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->where('date',date("Y-m-d"))->sum('credit'),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                                        @endif

                                        <span class="text-muted text-size-small">{{ trans_choice('general.today',1) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="content-group">
                                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                            <h5 class="text-semibold no-margin">{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->whereBetween('date',array('date_sub(now(),INTERVAL 1 WEEK)','now()'))->sum('credit'),2) }} </h5>
                                        @else
                                            <h5 class="text-semibold no-margin">{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->whereBetween('date',array('date_sub(now(),INTERVAL 1 WEEK)','now()'))->sum('credit'),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                                        @endif
                                        <span class="text-muted text-size-small">{{ trans_choice('general.last',1) }} {{ trans_choice('general.week',1) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="content-group">
                                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                            <h5 class="text-semibold no-margin">{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format($paid_this_month,2) }} </h5>
                                        @else
                                            <h5 class="text-semibold no-margin">{{ number_format($paid_this_month,2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                                        @endif
                                        <span class="text-muted text-size-small">{{ trans_choice('general.this',1) }} {{ trans_choice('general.month',1) }}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h6 class="no-margin text-semibold">{{ trans_choice('general.monthly',1) }} {{ trans_choice('general.target',1) }}</h6>
                                    </div>
                                    <div class="progress" data-toggle="tooltip"
                                         title="Target:{{number_format($target,2)}}">

                                        <div class="progress-bar bg-teal progress-bar-striped active"
                                             style="width: {{$percent}}%">
                                            <span>{{$percent}}% {{ trans_choice('general.complete',1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(Sentinel::hasAccess('dashboard.loans_collected_monthly_graph'))
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title">{{ trans_choice('general.monthly',1) }} {{trans_choice('general.overview',1)}}</h6>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="monthly_actual_missed_data" class="chart" style="height: 320px;">
                        </div>
                    </div>
                </div>
            @endif
        </div>

            @if(!empty($release))
            <!-- Modal -->
            <div class="modal" id="lastReleaseNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title margin-bottom-sm tablet:margin-bottom-0 tablet:margin-right-md display-flex flex-items-center">
                                <span class="fa-stack margin-right-4xs">
                                    <span class="fa fa-book"></span>
                                </span>
                                <span>Latest Released Update</span>
                            </h4>
                        </div>
                        <div class="modal-body">
                            <article>
                                <h6 style="font-size: 30px"><span class="glyphicon glyphicon-leaf"></span> <b>SL {{ $release->version_number }}</b></h6>
                                <span class="pull-right" style="font-size: 14px"><i>Released {{ Carbon\Carbon::parse($release->created_at, 'UTC')->isoFormat('MMMM Do YYYY') }}</i></span>
                                @if($release->is_added)
                                        <?php
                                        $isAdded = str_replace("<ul>", "<ul class='fa-ul'>", $release->added_note);
                                        $anotherIsAdded = str_replace("<li>", "<li class='margin-bottom-sm'
                                <span class='fa-li muted'>
                                    <i class='fa fa-plus' aria-hidden='true'></i>
                                </span>
                            ", $isAdded);
                                        ?>
                                    <h5 style="font-size: 20px; color:#00b300;"><b>What's New</b></h5>
                                    <div style="font-size: 18px">
                                        {!! $anotherIsAdded !!}
                                    </div>
                                @endif
                                @if($release->is_changed)
                                        <?php
                                        $isChanged = str_replace("<ul>", "<ul class='fa-ul'>", $release->changed_note);
                                        $anotherIsChanged = str_replace("<li>", "<li class='margin-bottom-sm'
                                <span class='fa-li muted'>
                                    <i class='fa fa-pencil' aria-hidden='true'></i>
                                </span>
                            ", $isChanged);
                                        ?>
                                    <h5 style="font-size: 20px; color:#cccc00;"><b>Changed Feature</b></h5>
                                    <div style="font-size: 18px">
                                        {!! $anotherIsChanged !!}
                                    </div>
                                @endif
                                @if($release->is_fixed)
                                        <?php
                                        $isFixed = str_replace("<ul>", "<ul class='fa-ul'>", $release->fixed_note);
                                        $anotherIsFixed = str_replace("<li>", "<li class='margin-bottom-sm'
                                <span class='fa-li muted'>
                                    <i class='fa fa-wrench' aria-hidden='true'></i>
                                </span>
                            ", $isFixed);
                                        ?>
                                    <h5 style="font-size: 20px; color:#cc0000;"><b>Bug Fixes</b></h5>
                                    <div style="font-size: 18px">
                                        {!! $anotherIsFixed !!}
                                    </div>
                                @endif
                            </article>
                        </div>
                        <div class="modal-footer">
                            <form class="form-horizontal" method="POST" action="{{url('change-release-note-status')}}">
                                {{ csrf_field() }}
                                <input type="submit" class="btn btn-secondary" id="change-status" value="Close">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
    </div>

    <script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}"
            type="text/javascript"></script>
    <script>
        AmCharts.makeChart("monthly_actual_missed_data", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_actual_missed_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#370fc6",
                "lineThickness": 4,
                "negativeLineColor": "#0dd102",
                "title": "{{trans_choice('general.actual',1)}}",
                "type": "smoothedLine",
                "valueField": "actual"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#d1655d",
                "lineThickness": 4,
                "negativeLineColor": "#d1cf0d",
                "title": "{{trans_choice('general.missed',2)}}",
                "type": "smoothedLine",
                "valueField": "missed"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }, "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }, "legend": {
                "position": "bottom",
                "marginRight": 100,
                "autoMargins": false
            },


        });

    </script>
    <script src="{{ asset('assets/plugins/chartjs/Chart.min.js') }}"
            type="text/javascript"></script>
    <script>
        var ctx3 = document.getElementById("loan_status_pie").getContext("2d");
        var data3 ={!! $loan_statuses !!};
        var myPieChart = new Chart(ctx3).Pie(data3, {
            segmentShowStroke: true,
            segmentStrokeColor: "#fff",
            segmentStrokeWidth: 0,
            animationSteps: 100,
            tooltipCornerRadius: 0,
            animationEasing: "easeOutBounce",
            animateRotate: true,
            animateScale: false,
            responsive: true,

            legend: {
                display: true,
                labels: {
                    fontColor: 'rgb(255, 99, 132)'
                }
            }
        });
    </script>
    @if(\Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser()->release_note_status && !empty($release))
        <script>
            $(document).ready(function(){
                $("#lastReleaseNote").modal('show');
            });
        </script>
    @endif
@endsection

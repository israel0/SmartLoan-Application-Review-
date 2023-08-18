@extends('layouts.master')
@section('title')
    {{trans_choice('general.release_note',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h4 style="font-size: 32px"><b>{{trans_choice('general.release_note',2)}}</b> </h4>
            <div class="heading-elements">
                @if(\Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser()->email == "admin@thinkingsmart.io")
                    <span data-toggle="modal" data-target="#releaseNote"
                          class="btn btn-success btn-sm"> {{trans_choice('general.new_release_note',2)}} </span>
                @endif
            </div>
        </div>
        <div class="panel-body ">
            @foreach($data as $release)
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
            @endforeach
        </div>
        <!-- /.panel-body -->
        <!-- Modal -->
        <!-- SupportModal -->
        <div class="modal fade" id="releaseNote">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form class="form-horizontal" method="POST" action="{{url('store-release-note')}}">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">*</span></button>
                            <h4 class="modal-title">Release Note</h4>
                        </div>
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label"><b>Version Number</b> <input type="text" name="version_number" id="version_number"> </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label"><b>Added Feature Note</b> <input type="checkbox" name="is_added" id="is_added"> </label>
                                        <input name="added_feature" type="textarea" class="form-control col-sm-10 tinymce" id="added_feature">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label"><b>Changed Feature Note</b> <input type="checkbox" name="is_changed" id="is_changed"> </label>
                                        <input name="changed_feature" type="textarea" class="form-control col-sm-10 tinymce" id="changed_feature">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label"><b>Fixed Feature Note</b> <input type="checkbox" name="is_fixed" id="is_fixed"></label>
                                        <input name="fixed_feature" type="textarea" class="form-control col-sm-10 tinymce" id="fixed_feature">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <input type="submit" name="submit" id="action" class="btn btn-success" value="Release" >
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>

    </script>
@endsection

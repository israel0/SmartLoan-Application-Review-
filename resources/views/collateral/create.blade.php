@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.collateral',1)}}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.collateral',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('collateral/'.$id.'/store'), 'method' => 'post','class'=>'', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            @if(isset($_REQUEST['return_url']))
                <input type="hidden" value="{{$_REQUEST['return_url']}}" name="return_url">
            @endif
            <div class="form-group">
                {!! Form::label('collateral_type_id',trans_choice('general.type',1),array('class'=>' control-label')) !!}
                {!! Form::select('collateral_type_id',$types,null, array('class' => 'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.product',1).' '.trans_choice('general.name',1),array('class'=>'')) !!}
                {!! Form::text('name',null, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('value',trans_choice('general.value',1),array('class'=>'')) !!}
                {!! Form::text('value',null, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.register',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                {!! Form::text('date',null, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('status',trans_choice('general.current',1).' '.trans_choice('general.status',1),array('class'=>' control-label')) !!}
                {!! Form::select('status',array('deposited_into_branch'=>'Deposited into branch','collateral_with_borrower'=>'Collateral with borrower','returned_to_borrower'=>'Returned to borrower','repossession_initiated'=>'Repossession initiated','repossessed'=>'Repossessed','sold'=>'Sold','lost'=>'Lost'),null, array('class' => 'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('serial_number',trans_choice('general.serial_number',1),array('class'=>'')) !!}
                {!! Form::text('serial_number',null, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('model_name',trans_choice('general.model_name',1),array('class'=>'')) !!}
                {!! Form::text('model_name',null, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('model_number',trans_choice('general.model_number',1),array('class'=>'')) !!}
                {!! Form::text('model_number',null, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('manufacture_date',trans_choice('general.manufacture_date',1),array('class'=>'')) !!}
                {!! Form::text('manufacture_date',null, array('class' => 'form-control date-picker')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'')) !!}
                {!! Form::textarea('notes',null, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('photo',trans_choice('general.collateral',1).' '.trans_choice('general.photo',1),array('class'=>'')) !!}
                {!! Form::file('photo', array('class' => 'form-control',)) !!}

            </div>
            <div class="form-group">
                {!! Form::label('files',trans_choice('general.collateral',1).' '.trans_choice('general.file',2).'('.trans_choice('general.borrower_file_types',1).')',array('class'=>'')) !!}
                {!! Form::file('files[]', array('class' => 'form-control', 'multiple'=>"",'rows'=>'3')) !!}
            </div>
            <p class="bg-navy disabled color-palette">{{trans_choice('general.custom_field',2)}}</p>
            @foreach($custom_fields as $key)

                <div class="form-group">
                    {!! Form::label($key->id,$key->name,array('class'=>'')) !!}
                    @if($key->field_type=="number")
                        <input type="number" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                    @if($key->field_type=="textfield")
                        <input type="text" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                    @if($key->field_type=="date")
                        <input type="text" class="form-control date-picker" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                    @if($key->field_type=="textarea")
                        <textarea class="form-control" name="{{$key->id}}"
                                  @if($key->required==1) required @endif></textarea>
                    @endif
                    @if($key->field_type=="decimal")
                        <input type="text" class="form-control touchspin" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                </div>
            @endforeach

        </div>

        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-warning pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
    {!! Form::close() !!}
    <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>

        $(document).ready(function (e) {

        })

    </script>
@endsection


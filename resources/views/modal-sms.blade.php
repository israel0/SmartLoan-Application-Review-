<!-- SupportModal -->
<div class="modal fade" id="updateAccount">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{url('setting/update-sms')}}" id="sms-form" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">*</span></button>
                <h4 class="modal-title">Update SMS login Details</h4>
            </div>
            <div class="modal-body">
               {{ csrf_field() }}
               <span id="form_output"></span>
             
    
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                    {!! Form::label('Email Address ', 'Email Address ',array('class'=>'col-sm-12 control-label')) !!}
                    <div class="col-sm-10">
                        {!! Form::email('email',\App\Models\Setting::where('setting_key','betasms_email')->first()->setting_value,array('class'=>'form-control','required'=>'required' , )) !!}
                    </div>
                        </div>
                    </div>    
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                    {!! Form::label('Password', 'Password',array('class'=>'col-sm-12 control-label')) !!}
                    <div class="col-sm-10">
                        {!! Form::text('password',\App\Models\Setting::where('setting_key','betasms_pass')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                    </div>
                </div>
                  </div>  
                </div>

            </div>

            <div class="modal-footer">
                <input type="hidden" name="button_action" id="button_action" value="insert" />
                <input type="submit" name="submit" id="action" class="btn btn-success pull-left" value="Update" >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> 
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
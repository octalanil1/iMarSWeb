<script type="text/javascript">
    $(document).ready(function() {
        // $('#type').select2();
        // $('#country_id').select2();

        $.LoadingOverlay("hide");
        $('#addContentForm').on('submit', function(e) {
            e.preventDefault();
            $.LoadingOverlay("show");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            //var title = $('#title').val();			// var message = $('#message').val();			  //var type = $('#type').val();


            $.ajax({
                dataType: 'json',
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                url: '{{ URL::to('/admin/add-notification-post') }}',
            }).done(function(data) {
                $.LoadingOverlay("hide");
                error_remove();
                if (data.success == false) {
                    $.each(data.errors, function(key, value) {
                        $('#' + key).closest('.form-group').addClass('has-error');
                        $('<div class="jquery-validate-error help-block animated fadeInDown">' + value + '</div>').insertAfter($('#' + key));
                    });

                } else {

                    search();
                    if(data.class == 'success'){showMsg(data.message, "success");}
                    $("#myModal").modal('hide');

                    return false;
                }


            });

        });
    });
</script>
<style>
    .upload-name {
        border: 0px;
        background: transparent;
        position: relative;
        top: 10px;
        padding-left: 15px;
    }
</style>
{!! Form::open(array('url' => '/admin/add-notification-post', 'method' => 'post','name'=>'addContentForm','files'=>true,'novalidate' => 'novalidate','id' => 'addContentForm')) !!}
<div class="col-sm-12 p-r-30">
    <div class="panel form-horizontal panel-transparent">
        <div class="panel-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="row form-group">
                        <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Notification Title<span class="red-star">*</span></label>
                        <div class="col-lg-10 col-sm-6 col-xs-12">

                            {!! Form::text('title',null, ['class' => 'form-control','placeholder' => 'Title','required'=>'required','id'=>'title','onkeypress' => 'error_remove()' ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="row form-group">
                        <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Notification Type<span class="red-star">*</span></label>
                        <div class="col-lg-10 col-sm-6 col-xs-12">

                            {!! Form::text('noti_type',null, ['class' => 'form-control','placeholder' => 'Notification Type','required'=>'required','id'=>'noti_type','onkeypress' => 'error_remove()' ]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="row form-group">
                        <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Notification Message<span class="red-star">*</span></label>
                        <div class="col-lg-10 col-sm-6 col-xs-12">
                            <textarea id="message" name="message" rows="5" cols="100" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="row form-group">
                        <label class="col-md-4 col-sm-6 col-xs-12 control-label">Attach File</label>

                        <div class="col-md-8">
                            <span class="import-excel">
                                <input type="file" name="file" class="form-control input-file" onkeypress="error_remove()">
                                <button class="btn btn-outline-success">Browse</button>
                            </span>
                        </div>
                        

                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="row form-group">
                        <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Recipients <span class="red-star">*</span></label>
                        <div class="col-lg-5 col-sm-6 col-xs-12">

                            <?php
                            $helper = new App\Helpers;
                             $type=$helper->UserTypeList();
                             array_shift($type);
                            ?>
                            {!! Form::select('user_type[]',$helper->UserTypeList(),null, ['class' => 'form-control','required'=>'required','id'=>'user_type','onkeypress' => 'error_remove()','multiple'=>'multiple']) !!}

                        </div>
                        <div class="col-lg-5 col-sm-6 col-xs-12">

                            <?php $helper = new App\Helpers; 
                            $CountryList=$helper->CountryList();
                            array_shift($CountryList);?>

                            {!! Form::select('country[]',$helper->CountryList(),null, ['class' => 'form-control','required'=>'required','id'=>'country','multiple'=>'multiple']) !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="row form-btn text-center">
    <div class="col-sm-12 p-r-30">
        <div class="col-md-12">
            {!! Form::submit('Save',['class' => 'btn btn-primary btn-flat subbtn', 'type' => 'submit']) !!}
            {!! Form::submit('Cancel',['class' => 'btn btn-flat subbtn','data-dismiss'=>'modal']) !!}
        </div>
    </div>

    {!! Form::close() !!}
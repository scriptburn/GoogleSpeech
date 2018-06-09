@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <div class="container">
      <h1>Google Text to speech
      </h1>

  </div>
    </section>
@endsection

@section('content')
   <div class="row">
    <div class="col-md-12">
        <div class="container">

            <div class="alert alert-success" role="alert" style="display:none;min-height: 85px;" id="success_alert">
                {{--
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                --}}
                <div class="pull-left">
                <strong>Your audio file is ready</strong>
                <br/>
                <a href="#" class="alert-link">click here</a> to download
                 </div>
                <div id="player"  class="pull-right">
                </div>
              
            </div>
            <div class="alert alert-danger" role="alert" style="display:none" id="danger_alert"> {{--
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button> --}}
                <strong>Oh snap!</strong> <br/>
                <span class="danger_alert_body"></span>
            </div>
            <form method="post" id="speech-form">{!! csrf_field() !!}
                <div class="box box-default">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="speech_text">Text to speak</label>
                            <textarea class="form-control" id="speech_text" name="speech_text" rows="10"></textarea>
                            <small id="emailHelp" class="form-text text-muted">Enter text to convert into speech</small>
                             
                                <div class="checkbox pull-right" style="display: inline">
   
                                <input type="checkbox" data-toggle="toggle" name="speech_ssml" value="1" data-on="SSML"  data-off="Text">
 
   
 
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-2">
                                    <label for="speech_lang">Language / locale</label>
                                    <select class="form-control" id="speech_lang" name="speech_lang" >
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label for="speech_voice_type">Voice type</label>
                                    <select class="form-control" id="speech_voice_type" name="speech_voice_type">
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label for="speech_voice_name">Voice name</label>
                                    <select class="form-control" id="speech_voice_name"  name="speech_voice_name">
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label for="speech_voice_speed">Speed <a style="padding-left:5px" class="slider-edit" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
</a></label>
                                    <br/>
                                    <input type="text" class="slider_with_label" data-label="Speed" id="speech_voice_speed" name="speech_voice_speed" data-slider-tooltip="always" data-provide="slider" ' data-slider-min="0.25" data-slider-tooltip-position="bottom" data-slider-max="4" data-slider-step="0.05" data-slider-value="1" />
                                </div>
                                <div class="col-lg-3">
                                    <label for="speech_voice_pitch">Pitch <a style="padding-left:5px" class="slider-edit" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
</a></label><br/>
                                    <input type="text" class="slider_with_label"  id="speech_voice_pitch"  data-label="Pitch" data-slider-tooltip="always"  name="speech_voice_pitch" data-provide="slider"    data-slider-min="-20" data-slider-max="20" data-slider-step="1"  data-slider-tooltip-position="bottom" data-slider-value="0" />
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="form_submit_btn"><span id="form_submit_text"> <i class="fa fa-paper-plane" aria-hidden="true"></i>
 Submit</span> <i style="display: none" id="form_submit_spinner" class="fa fa-circle-o-notch fa-spin"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('after_scripts')

<script>
var speech_voices={!! json_encode($voices) !!};
var cur_lang="{{ $cur_lang }}";
 $( document ).ready(function() {


    $('.slider-edit').on('click',function(){
    
    $slider=$(this).parent().parent().find('.slider_with_label');
    input=prompt("Please enter "+ $($slider).data('label'));
    if(input<$($slider).data('slider-min') || input>$($slider).data('slider-max')  )
    {
        alert("Please choose value in range " + $($slider).data('slider-min')  + " to " + $($slider).data('slider-max') );
        return;
    }
    $($slider).slider().slider('setValue',input);
      

    });


    var keys=Object.keys(speech_voices);
    html="";
    for(i=0;i<keys.length;i++)
    {
        html+="<option value='"+keys[i]+"'"+ (cur_lang==keys[i]?'selected=selected':'') +">"+keys[i]+"</option>";
    }
    $('#speech_lang').html(html);

     $('#speech_lang').on('change',function(){

        var keys=Object.keys(speech_voices[$(this).val()]['voice_type']);
       // console.log(keys);
        var html="";
        for(i=0;i<keys.length;i++)
        {
            html+="<option value='"+keys[i]+"'>"+keys[i]+"</option>";
        }
         $('#speech_voice_type').html(html);
         $('#speech_voice_type').change();
     });
     $('#speech_voice_type').on('change',function(){

        var keys=Object.keys(speech_voices[$('#speech_lang').val()]['voice_type'][$(this).val()] );
         var html="";
         
        for(i=0;i<keys.length;i++)
        {
            html+="<option value='"+keys[i]+"'>"+keys[i]+"</option>";
        }
         $('#speech_voice_name').html(html);
     });
     $('#speech_lang').change();



     $("#speech-form").submit(function(e) {
    $('.alert').hide();
 
$('#form_submit_btn').attr('disabled','disabled');
$('#form_submit_text').hide();
$('#form_submit_spinner').show();

    $.ajax({
           type: "POST",
           url: "{{ route('home') }}",
           data: $("#speech-form").serialize(), // serializes the form's elements.
           success: function(data)
           {
                           $('#form_submit_btn').removeAttr('disabled');
$('#form_submit_text').show();
$('#form_submit_spinner').hide();

            if(data && data.status )
            {
             
                $('#player').html('<audio controls  src="'+"{{ route('home') }}/download/" + data.data['file_name']+'" type="audio/mpeg"></audio>')
                $('#success_alert>.pull-left>a.alert-link').attr('href',"{{ route('home') }}/download/" + data.data['file_name']);
                $('#success_alert').show();
            }
            else
            {
                if(!data)
                {
                     $('#danger_alert>span.danger_alert_body').html('Unknown response');
                    
                }
                else if(!data.data)
                {
                     $('#danger_alert>span.danger_alert_body').html('Unknown response');

                }
                else
                {
                    $('#danger_alert>span.danger_alert_body').html(data.data);

                }
                $('#danger_alert').show();
            }
                
           },
           error: function(jqXHR,   textStatus,   errorThrown)
           {
                $('#danger_alert>span.danger_alert_body').html(errorThrown);
                $('#danger_alert').show("fold", 1000);
                $('#form_submit_btn').removeAttr('disabled');
$('#form_submit_text').show();
$('#form_submit_spinner').hide();
           }
         });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});
});
</script>
@endsection
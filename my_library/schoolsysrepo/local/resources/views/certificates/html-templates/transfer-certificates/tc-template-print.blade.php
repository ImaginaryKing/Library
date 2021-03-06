<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TC</title>
</head>

<body onload="printDiv()">

    <table cellpadding="0" cellspacing="0" border="0" bgcolor="#FFF" style="font-family: arial;font-size: 12px;color: #555; line-height: 18px; border-top: 1px solid #ccc;border-left: 1px solid #ccc;" align="center" width="100%">

        <tr>
            <td colspan="2" style="border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;">
                <table cellpadding="0" cellspacing="0" width="100%">

                <tr>
                    <td colspan="3">
                        <table width="100%">
                            <tr>

                                <td align="left"><img src="{{IMAGE_PATH_SETTINGS.getSetting('site_logo', 'site_settings')}}" height="60" alt=""></td>
                                <td  align="right"><span style=" font-family:arial; color:#777">{{getPhrase('date')}}:

              <b>{{date('d M Y')}}</b>
              </span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                    <tr>

                        <td align="center" style="padding: 10px;">
                            <h4 style="font-family: arial;font-size: 20px; margin-top: 0; color: #555; line-height: 24px; text-align: center;">{!!getSetting('site_address','site_settings')!!}</h4><hr><h2>{{getPhrase('transfer_certificate')}}</h2>
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <strong>{{getPhrase('serial_no')}}: {{$tcprint_details->serial_no}}</strong>
                                    </td>
                                    <td align="right">
                                        <strong>{{getPhrase('Admission_no')}}: {{$tcprint_details->admission_no}}</p></strong>
                                    </td>
                                </tr>
                            </table>
                            </td>
                            </tr>
                </table>
            </td>
        </tr>
        <?php if(getSetting('name_of_student', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Name_of_the_Pupil')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->student_name}}</strong></td>
        </tr>
        <?php } ?>

        <?php  if(getSetting('father_guardian_name', 'transfer_certificate_fields')) { ?>

        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Father_or_Guardian Name')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->fathername}}</strong></td>
        </tr>
        <?php } ?>

        <?php if(getSetting('mother_name', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Mother_Name')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->mothername}}</strong></td>
        </tr>
        <?php } ?>

         <?php if(getSetting('nationality', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Nationality')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->nation}}</strong></td>
        </tr>
        <?php } ?>
         <?php if(getSetting('candidate_caste', 'transfer_certificate_fields'))  { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Candidate_belongs_to_Schedule_Caste_or_Schedule_Tribe')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->caste}}</strong></td>
        </tr>
        <?php } ?>
         <?php if(getSetting('date_of_admission_with_class', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Date_of_first_Admission_in_the_school')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->joining_date}}</strong></td>
        </tr>
          <?php } ?>

           <?php if(getSetting('first_admission_in_the_school', 'transfer_certificate_fields')) {?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('first_Admission_Class_in_the_school_with_Name')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->joining_class}}</strong></td>
        </tr>
         <?php } ?>

          <?php if(getSetting('date_of_birth', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Date_Of_Birth_according_to_the_admission_register')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->date_of_birth}}</strong></td>
        </tr>
        <?php } ?>
         <?php if(getSetting('last_class_studied', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Class_in_which_the_last_studied_with_name')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->last_class_study}}</strong></td>
        </tr>
        <?php } ?>
        <?php if(getSetting('last_taken_exam_and_result', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('School_Board_Annual_Examination_last_taken_and_Result')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->exam_details}}</strong></td>
        </tr>
        <?php } ?>
        <?php if(getSetting('whether_failed_if_once_twice_in_the_same_class', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Whether_failed_if_once_twice_in_the_same_class')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->exam_status}}</strong></td>
        </tr>
        <?php } ?>
        <?php if(getSetting('promotion_class', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Whether_qualified_for_promotion_to_higer_class_if_so_to_which_class')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->promotion_class}}</strong></td>
        </tr>
        <?php } ?>
         <?php if(getSetting('total_working_days', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Total_Number_NO.of_working_days')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->working_days}}</strong></td>
        </tr>
        <?php } ?>
        <?php if(getSetting('total_present_days', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Total_Number_NO.of_days_present')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->present_days}}</strong></td>
        </tr>
        <?php } ?>
        <?php if(getSetting('ncc_boy_scout_girls_guide', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('whether_NCC_cadet_boy_scout_girls_guide')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->ncc}}</strong></td>
        </tr>
        <?php } ?>
          <?php if(getSetting('games_played_or_extra_curricular_activities', 'transfer_certificate_fields')) { ?>
         <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Games_played_or_extra-curricular_activities')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->activities}}</strong></td>
        </tr>
        <?php } ?>
          <?php if(getSetting('general_conduct', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('General_Conduct')}}</strong></td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->conduct}}</strong></td>
        </tr>
        <?php } ?>
         <?php if(getSetting('date_of_apply', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Date_of_Application_for_Certificate')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->apply_date}}</strong></td>
        </tr>
        <?php } ?>
        <?php if(getSetting('date_of_issue', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Date_of_issue_Certificate')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{date('d M Y')}}</strong></td>
        </tr>
        <?php } ?>
        <?php  if(getSetting('reason', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Reason_for_leaving_the_school')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->reason}}</strong></td>
        </tr>
        <?php } ?>
         <?php if(getSetting('remarks', 'transfer_certificate_fields')) { ?>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;" width="50%"><strong>{{getPhrase('Any_other_remarks')}}</strong> </td>
            <td style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;"><strong>{{$tcprint_details->remarks}}</strong></td>
        </tr>
        <?php } ?>

        <?php if(getSetting('print_footer', 'transfer_certificate_settings')) { ?>

        <tr>
            <td colspan="2" style="padding: 10px; border-bottom: 1px solid #ccc;border-right: 1px solid #ccc;">
                <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>
                        <?php if(getSetting('show_left_side_sign', 'transfer_certificate_settings')) { ?>

                    <img src="{{IMAGE_PATH_SETTINGS.getSetting('left_sign_image', 'certificate')}}" height="60" alt="">

                  <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(getSetting('show_right_side_sign', 'transfer_certificate_settings')) { ?>

                         <img src="{{IMAGE_PATH_SETTINGS.getSetting('right_sign_image', 'certificate')}}" height="60" alt="">


                         <?php  }?>
                    </td>
                </tr>
                    <tr>


                    <?php if(getSetting('show_left_side_name', 'transfer_certificate_settings')) { ?>
                        <td  align="left" width="50%"  valign="bottom">
                           {{getSetting('left_sign_name','certificate')}}
                        </td>
                        <?php } ?>



                         <?php if(getSetting('show_right_side_name', 'transfer_certificate_settings')) { ?>

                        <td  align="right" width="50%"  valign="bottom">
                          {{getSetting('right_sign_name','certificate')}}
                        </td>

                        <?php  }?>

                    </tr>
                     <tr>


                    <?php if(getSetting('show_left_side_designation', 'transfer_certificate_settings')) { ?>
                        <td  align="left" width="50%"  valign="bottom">
                            {{getSetting('left_sign_designation','certificate')}}
                        </td>
                        <?php } ?>



                         <?php if(getSetting('show_right_side_designation', 'transfer_certificate_settings')) { ?>

                        <td  align="right" width="50%"  valign="bottom">
                          {{getSetting('right_sign_designation','certificate')}}
                        </td>

                        <?php  }?>

                    </tr>

                </table>
            </td>
        </tr>
        <?php }?>



</body>
<!-- <script >
function printDiv()
{

  var divToPrint=document.getElementById('DivIdToPrint');

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

  newWin.document.close();

  setTimeout(function(){newWin.close();},10);

}
</script>
 -->
</html>

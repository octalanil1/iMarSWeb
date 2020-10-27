<table width="400" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
	<tr>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;"> Corporate Identification Number (CIN)</td>
     <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['cin']}}</td>
   
  </tr>
 <tr>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Company Name</td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['company_name']}}</td>
  </tr>
  <tr>
 
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Date of Incorporation</td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['date_of_incorporation']}}</td>
  </tr>
  <tr>
 
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Listed</td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['listed']}}</td>
  </tr>
    <tr>
 
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Registered address</td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['registered_address']}}</td>
  </tr>
   
    <tr>
 
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Phone No </td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['phone_no']}}</td>
  </tr>
   
  <tr>
 
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Email </td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['email']}}</td>
  </tr>
  <tr>
 
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">Contact person mobile </td>
    <td style="border:1px solid #000; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12;">{{ $data['contact_person_mobile']}}</td>
  </tr>
</table><div>Please Click to <a href="{{ $data['activate_url']}}">activate</a> or <a href="{{ $data['deactivate_url']}}">deactivate </a> this account</div>
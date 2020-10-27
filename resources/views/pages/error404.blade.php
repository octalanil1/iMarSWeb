@extends('layouts.master')
@section('title')
Error 404 @stop
@section('content')
<div class="contaner">
 <div class="wrraper">
  
   <div class="text-center">
   
    <h1>Error 404 Page Not found!.</h1>
   <h3><?php if(isset($msg)){echo $msg; } ?></h3>
   </div>

   </div>
 </div>
 <div class="clear-both"></div>
   
   
   
   

@stop
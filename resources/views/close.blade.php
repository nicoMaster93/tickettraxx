<?php 
    $time = 3;
?>
@extends('layouts.app')
@section('title', 'Closing tab')
@section('content')
<div class="container-fluid">   
    <div class="row align-items-center">
        <div class="col-12">
            <h1>Closing Tab </h1>
        </div>
    </div> 
    <div class="row align-items-center">
        <div class="col-12">
            <div class="alert alert-success">
                {{ $message }}
            </div>
        </div>
    </div>
    <br />
    <p>Closing... [ <small id="closeId"><?=$time?></small> ]</p>
</div>

@endsection

<script>
    let interval = setInterval(function(){
        let number = $("#closeId").text();
        let res = number - 1;
        $("#closeId").text(res)
        if(res == 0){
            clearInterval(interval);
            window.close();
        }
    },1000);
</script>
    
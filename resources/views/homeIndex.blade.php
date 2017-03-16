@extends('layouts.master')

@section('title')
  Home | Dream Catcher Network
@endsection

@section('content')
<br/>
  <!--h3>Welcome to the Dream Catcher Network</h3-->
  @include('layouts.roundedcircle')
        <!-- START THE FEATURETTES -->

        <hr class="featurette-divider">

        <div class="row featurette">
          <div class="col-md-7">
            <h2 class="featurette-heading">First featurette heading. <span class="text-muted">ARE YOU READY?</span></h2>
            <p class="lead">Place Holder Text</p>
          </div>
          <div class="col-md-5">
            <img class="featurette-image img-fluid mx-auto" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
          </div>
        </div>

        <hr class="featurette-divider">

        <div class="row featurette">
          <div class="col-md-7 push-md-5">
            <h2 class="featurette-heading">Oh yeah, it's that good. <span class="text-muted">YOU SHOULD BE!</span></h2>
            <p class="lead">Place Holder Text</p>
          </div>
          <div class="col-md-5 pull-md-7">
            <img class="featurette-image img-fluid mx-auto" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
          </div>
        </div>

        <hr class="featurette-divider">

        <div class="row featurette">
          <div class="col-md-7">
            <h2 class="featurette-heading">And lastly, this one. <span class="text-muted">BOOM!</span></h2>
            <p class="lead">Place Holder Text</p>
          </div>
          <div class="col-md-5">
            <img class="featurette-image img-fluid mx-auto" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
          </div>
        </div>

        <hr class="featurette-divider">
@endsection

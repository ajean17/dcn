@extends('layouts.master')

@section('title')
  Home | Dream Catcher Network
@endsection

@section('content')
  <!--canvas id="canvasOne" width="100" height="100"></canvas-->
  <svg viewBox="0 0 8000 580">
  <symbol id="s-text">
      <text text-anchor="middle" x="49.8%" y="98%">Dream Catcher Network</text>
  </symbol>
  <!-- Each line corresponds to an animated line in the CSS code-->
  <g>
    <use xlink:href="#s-text" class="text-copy"></use>
    <use xlink:href="#s-text" class="text-copy"></use>
    <use xlink:href="#s-text" class="text-copy"></use>
    <use xlink:href="#s-text" class="text-copy"></use>
    <use xlink:href="#s-text" class="text-copy"></use>
    <use xlink:href="#s-text" class="text-copy"></use>
  </g>
  </svg>
  <center><a class="btn btn-lg btn-outline-info" href="/register" role="button">Sign up today</a></center>
  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
      <div class="carousel-item active">
        <!--img class="first-slide" src="" alt="First slide"-->
        <div class="container">
          <div class="carousel-caption d-none d-md-block text-left">
            <img class="first-slide" width="800px" height="450px" src="images/home.jpeg" alt="First slide">
            <p>
              We are getting with the client to fill out some cool words to put here.
            </p>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="container">
          <div class="carousel-caption d-none d-md-block text-left">
            <img class="second-slide" width="800px" height="450px" src="images/dcn2.png" alt="Second slide">
              <p>
                We are also going to get better pictures once everything is 100% sexy.
              </p>
          </div>
          <!--div class="carousel-caption d-none d-md-block">
            <p><a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
          </div-->
        </div>
      </div>
      <div class="carousel-item">
        <div class="container">
          <div class="carousel-caption d-none d-md-block text-left">
            <img class="third-slide" width="800px" height="450px" src="images/dcn3.png" alt="Third slide">
            <p>
              This is our ugly golden goose.
            </p>
          </div>
        </div>

      </div>
    </div>
    <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
@endsection

@section('javascript')
  <script>
    $('.carousel').carousel({
      interval: 5000
    })
  </script>
@endsection
